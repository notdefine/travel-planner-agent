<?php

declare(strict_types=1);

namespace App\Tools\SerpAPI;

use GuzzleHttp\Client;
use NeuronAI\Tools\Tool;

class SerpAPIFlight extends Tool
{
    protected Client $client;

    public function __construct(protected string $key)
    {
        // Define Tool name and description
        parent::__construct(
            'SerpAPIFlight',
            'describe the purpose of the tool',
        );
    }
    
    /**
     * Return the list of properties.
     */
    protected function properties(): array
    {
        return [
            // ...
        ];
    }
    
    /**
     * Implementing the tool logic
     */
    public function __invoke(): string
    {
        // ...
    }

    protected function getClient(): Client
    {
        return $this->client ?? $this->client = new Client([
            'base_uri' => 'https://serpapi.com/',
            'headers' => [
                'X-API-KEY' => $this->key,
                'Content-Type' => 'application/json',
            ]
        ]);
    }
}