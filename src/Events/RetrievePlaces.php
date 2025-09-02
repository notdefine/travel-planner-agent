<?php

namespace App\Events;

use App\Agents\TourInfo;
use NeuronAI\Workflow\Event;

class RetrievePlaces implements Event
{
    public function __construct(public TourInfo $tour)
    {
    }
}