<?php

declare(strict_types=1);

namespace App\Nodes;

use App\Agents\ExtractedInfo;
use App\Agents\ResearchAgent;
use App\Events\ProgressEvent;
use App\Events\Retrieve;
use App\Prompts;
use NeuronAI\Chat\History\ChatHistoryInterface;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Exceptions\AgentException;
use NeuronAI\Exceptions\WorkflowException;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StartEvent;
use NeuronAI\Workflow\WorkflowInterrupt;
use NeuronAI\Workflow\WorkflowState;

class ReceptionistNode extends Node
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
    public function __invoke(StartEvent $event, WorkflowState $state): \Generator|Retrieve
    {
        $query = str_replace('{query}', $state->get('query'), Prompts::TOUR_PLANNER);
        //$query = $state->get('query');

        if ($this->isResuming) {
            $query = $this->interrupt([]);
        }

        yield new ProgressEvent("\n- Processing the request...");

        /** @var ExtractedInfo $info */
        $info = ResearchAgent::make()
            ->withChatHistory($this->history)
            ->structured(
                new UserMessage($query),
                ExtractedInfo::class
            );

        if (!isset($info->tour) || !$info->tour->isComplete()) {
            $feedback = $this->interrupt(['question' => $info->description]);
        }

        return new Retrieve($info->tour);
    }
}
