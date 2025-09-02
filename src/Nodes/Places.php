<?php

namespace App\Nodes;

use App\Agents\ResearchAgent;
use App\Events\Retrieve;
use App\Events\RetrievePlaces;
use App\Tools\SerpAPI\SerpAPIPlace;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;

class Places extends Node
{
    public function __invoke(RetrievePlaces $event, WorkflowState $state): Retrieve
    {
        $response = ResearchAgent::make()
            ->addTool(
                SerpAPIPlace::make($_ENV['SERPAPI_KEY'])
            )
            ->chat(
                new UserMessage(
                    "Find the best points of interest and places to visit in the area of CITY: {$event->tour->city}"
                )
            );

        $state->set('places', $response->getContent());

        return new Retrieve($event->tour);
    }
}