<?php

declare(strict_types=1);

namespace App\Nodes;

use App\Agents\ExtractedInfo;
use App\Agents\ResearchAgent;
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
use NeuronAI\Workflow\StopEvent;
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
    ): \Generator|RetrieveHotels|RetrievePlaces|RetrieveFlights|StopEvent {

        $history = new FileChatHistory(__DIR__, 'planner');

        $query = $state->get('query', $this->feedback[static::class] ?? '');;

        if ($this->isResuming && $this->feedback ) {

        }

        yield new ProgressEvent("\n========== Extracting information from the user request... ==========\n");

        $msg = \str_replace('{query}', $state->get('query'), Prompts::TOUR_PLANNER);

        /** @var ExtractedInfo $info */
        $info = ResearchAgent::make()
            ->withChatHistory($history)
            ->structured(
                new UserMessage($msg),
                ExtractedInfo::class
            );

        if (!isset($info->tour)) {
            $feedback = $this->interrupt(['message' => $info->description]);
        }

        $history->flushAll();

        return new StopEvent();
    }
}
