<?php

declare(strict_types=1);

namespace App\Nodes;

use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StartEvent;
use NeuronAI\Workflow\StopEvent;
use NeuronAI\Workflow\WorkflowState;

class DelegationNode extends Node
{
    public function __invoke(StartEvent $event, WorkflowState $state): StopEvent
    {
        return new StopEvent();
    }
}
