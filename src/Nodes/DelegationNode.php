<?php

namespace App\Nodes;

use App\Events\CreateItinerary;
use App\Events\ProgressEvent;
use App\Events\Retrieve;
use App\Events\RetrieveFlights;
use App\Events\RetrieveHotels;
use App\Events\RetrievePlaces;
use NeuronAI\Workflow\Node;
use NeuronAI\Workflow\WorkflowState;

class DelegationNode extends Node
{
    public function __invoke(
        Retrieve $event, WorkflowState $state
    ): \Generator|RetrieveHotels|RetrievePlaces|RetrieveFlights|CreateItinerary {
        if (!$state->has('flights')) {
            yield new ProgressEvent("\n- Retrieving flights information...");
            return new RetrieveFlights($event->tour);
        }

        if (!$state->has('hotels')) {
            yield new ProgressEvent("\n- Retrieving hotels information...");
            return new RetrieveHotels($event->tour);
        }

        if (!$state->has('places')) {
            yield new ProgressEvent("\n- Retrieving points of interest information...");
            return new RetrievePlaces($event->tour);
        }

        return new CreateItinerary($event->tour);
    }
}