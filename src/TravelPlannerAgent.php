<?php

declare(strict_types=1);

namespace App;

use App\Nodes\Delegator;
use App\Nodes\Receptionist;
use App\Nodes\Flights;
use App\Nodes\GenerateItinerary;
use App\Nodes\Hotels;
use App\Nodes\Places;
use NeuronAI\Chat\History\ChatHistoryInterface;
use NeuronAI\Chat\History\FileChatHistory;
use NeuronAI\Exceptions\ChatHistoryException;
use NeuronAI\Exceptions\WorkflowException;
use NeuronAI\Workflow\Persistence\PersistenceInterface;
use NeuronAI\Workflow\Workflow;
use NeuronAI\Workflow\WorkflowState;

class TravelPlannerAgent extends Workflow
{
    protected ChatHistoryInterface $history;

    /**
     * @throws ChatHistoryException
     * @throws WorkflowException
     */
    public function __construct(?WorkflowState $state = null, ?PersistenceInterface $persistence = null, ?string $workflowId = null)
    {
        parent::__construct($state, $persistence, $workflowId);

        $this->history = new FileChatHistory(__DIR__.'/../', 'planner');
    }

    protected function nodes(): array
    {
        return [
            new Receptionist($this->history),
            new Delegator(),
            new Flights(),
            new Hotels(),
            new Places(),
            new GenerateItinerary($this->history)
        ];
    }
}