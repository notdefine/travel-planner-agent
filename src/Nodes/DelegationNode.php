<?php

declare(strict_types=1);

namespace App\Nodes;

use App\Agents\ExtractedInfo;
use App\Agents\ResearchAgent;
use App\Events\CreateItinerary;
use App\Events\ProgressEvent;
use App\Events\RetrieveFlights;
use App\Events\RetrieveHotels;
use App\Events\RetrievePlaces;
use App\Prompts;
use NeuronAI\Chat\History\FileChatHistory;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Exceptions\AgentException;
use NeuronAI\Exceptions\WorkflowException;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StartEvent;
use NeuronAI\Workflow\WorkflowInterrupt;
use NeuronAI\Workflow\WorkflowState;

class DelegationNode extends Node
{
    /**
     * @throws \Throwable
     * @throws WorkflowInterrupt
     * @throws AgentException
     * @throws \ReflectionException
     * @throws WorkflowException
     */
    public function __invoke(
        StartEvent $event, WorkflowState $state
    ): \Generator|RetrieveHotels|RetrievePlaces|RetrieveFlights|CreateItinerary {

        $history = new FileChatHistory(__DIR__, 'planner');

        $query = $state->get('query');

        if ($this->isResuming) {
            $query = $this->interrupt([]);
        }

        yield new ProgressEvent("\n========== Extracting information from the user request... ==========\n");

        $msg = \str_replace('{query}', $query, Prompts::TOUR_PLANNER);

        /** @var ExtractedInfo $info */
        $info = ResearchAgent::make()
            ->withChatHistory($history)
            ->structured(
                new UserMessage($msg),
                ExtractedInfo::class
            );

        if (!isset($info->tour)) {
            $this->interrupt(['message' => $info->description]);
        }

        $history->flushAll();

        if (!$state->has('flights')) {
            return new RetrieveFlights($info->tour);
        }

        if (!$state->has('hotels')) {
            return new RetrieveHotels($info->tour);
        }

        if (!$state->has('places')) {
            return new RetrievePlaces($info->tour);
        }

        return new CreateItinerary($info->tour);
    }
}
