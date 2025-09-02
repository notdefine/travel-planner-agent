<?php

namespace App\Nodes;

use App\Events\RetrievePlaces;
use App\Tools\SerpAPI\SerpAPIFlight;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StartEvent;
use NeuronAI\Workflow\WorkflowState;

class Flights extends Node
{
    public function __invoke(RetrievePlaces $event, WorkflowState $state): StartEvent
    {
        $tool = SerpAPIFlight::make($_ENV['SERPAPI_KEY']);

        $flights = $tool(
            $event->tour->airport_from,
            $event->tour->airport_to,
            $event->tour->departure_date,
            $event->tour->return_date??null,
        );

        $state->set('flights', $flights);

        return new StartEvent();
    }
}