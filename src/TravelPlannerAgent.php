<?php

declare(strict_types=1);

namespace App;

use App\Nodes\DelegationNode;
use App\Nodes\Flights;
use App\Nodes\Format;
use App\Nodes\Hotels;
use App\Nodes\Places;
use NeuronAI\Chat\History\ChatHistoryInterface;
use NeuronAI\Chat\History\FileChatHistory;
use NeuronAI\Workflow\Persistence\PersistenceInterface;
use NeuronAI\Workflow\Workflow;
use NeuronAI\Workflow\WorkflowState;

class TravelPlannerAgent extends Workflow
{
    protected ChatHistoryInterface $history;

    public function __construct(?WorkflowState $state = null, ?PersistenceInterface $persistence = null, ?string $workflowId = null)
    {
        parent::__construct($state, $persistence, $workflowId);

        $this->history = new FileChatHistory(__DIR__.'/../', 'planner');
    }

    protected function nodes(): array
    {
        return [
            new DelegationNode($this->history),
            new Flights(),
            new Hotels(),
            new Places(),
            new Format($this->history)
        ];
    }
}