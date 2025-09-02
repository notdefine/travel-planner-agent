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
use NeuronAI\Chat\History\ChatHistoryInterface;
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
    public function __construct(protected ChatHistoryInterface $history)
    {
    }

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

        yield new ProgressEvent("\n============ Planning the itinerary ============\n");

        $query = $state->get('query');

        if ($this->isResuming) {
            $query = $this->interrupt([]);
        }

        yield new ProgressEvent("\n- Processing the request...");

        $msg = \str_replace('{query}', $query, Prompts::TOUR_PLANNER);

        /** @var ExtractedInfo $info */
        $info = ResearchAgent::make()
            ->withChatHistory($this->history)
            ->structured(
                new UserMessage($msg),
                ExtractedInfo::class
            );

        if (!$info->tour->isComplete()) {
            $this->interrupt(['question' => $info->description]);
        }

        if (!$state->has('flights')) {
            yield new ProgressEvent("\n- Retrieving flights information...");
            return new RetrieveFlights($info->tour);
        }

        if (!$state->has('hotels')) {
            yield new ProgressEvent("\n- Retrieving hotels information...");
            return new RetrieveHotels($info->tour);
        }

        if (!$state->has('places')) {
            yield new ProgressEvent("\n- Retrieving points of interest information...");
            return new RetrievePlaces($info->tour);
        }

        return new CreateItinerary($info->tour);
    }
}
