<?php

declare(strict_types=1);

namespace App;

use App\Nodes\DelegationNode;
use NeuronAI\Workflow\Workflow;

class TravelPlannerAgent extends Workflow
{
    protected function nodes(): array
    {
        return [
            new DelegationNode(),
        ];
    }
}