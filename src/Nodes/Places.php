<?php

namespace App\Nodes;

use App\Events\RetrievePlaces;
use App\Tools\SerpAPI\SerpAPIPlace;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StartEvent;
use NeuronAI\Workflow\WorkflowState;

class Places extends Node
{
    public function __invoke(RetrievePlaces $event, WorkflowState $state): StartEvent
    {
        $tool = SerpAPIPlace::make($_ENV['SERPAPI_KEY']);

        $state->set('places', $tool($event->tour->city));

        return new StartEvent();
    }
}