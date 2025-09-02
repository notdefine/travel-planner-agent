<?php

namespace App\Nodes;

use App\Events\RetrieveHotels;
use App\Tools\SerpAPI\SerpAPIHotel;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\StartEvent;
use NeuronAI\Workflow\WorkflowState;

class Hotels extends Node
{
    public function __invoke(RetrieveHotels $event, WorkflowState $state): StartEvent
    {
        $tool = SerpAPIHotel::make($_ENV['SERPAPI_KEY']);

        $hotels = $tool(
            $event->tour->city,
            $event->tour->airport_to,
            $event->tour->departure_date,
        );

        $state->set('hotels', $hotels);

        return new StartEvent();
    }
}