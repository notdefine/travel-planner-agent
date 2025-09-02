<?php

namespace App\Agents;

use NeuronAI\StructuredOutput\SchemaProperty;
use NeuronAI\StructuredOutput\Validation\Rules\WordsCount;

class ExtractedInfo
{
    #[SchemaProperty(
        description: 'Your reasoning under 10 words behind the extracted information. ',
        required: true
    )]
    #[WordsCount(min: 3, max: 20)]
    public string $description;

    #[SchemaProperty(
        description: 'The extracted tour information. If you are not able to fully extract the tour information, you must leave this field empty.',
        required: false
    )]
    public TourInfo $tour;
}