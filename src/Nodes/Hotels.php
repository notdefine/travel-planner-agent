<?php

namespace App\Nodes;

use App\Agents\ResearchAgent;
use App\Events\Retrieve;
use App\Events\RetrieveHotels;
use App\Tools\SerpAPI\SerpAPIHotel;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;

class Hotels extends Node
{
    public function __invoke(RetrieveHotels $event, WorkflowState $state): Retrieve
    {
        $response = ResearchAgent::make()
            ->addTool(
                SerpAPIHotel::make($_ENV['SERPAPI_KEY'])
            )
            ->chat(
                new UserMessage(
                    "Find the best hotels in the area of CITY: {$event->tour->city} between " .
                    " CHECK_IN_DATE: " . $event->tour->departure_date . " and CHECK_IN_DATE: " . $event->tour->return_date .
                    "Select the hotels with the lowest price and best position."
                )
            );

        $state->set('hotels', $response->getContent());

        return new Retrieve($event->tour);
    }
}