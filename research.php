<?php

use App\Events\ProgressEvent;
use App\TravelPlannerAgent;
use NeuronAI\Workflow\WorkflowInterrupt;
use NeuronAI\Workflow\WorkflowState;


include __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Interactive console
echo 'Describe the topic: ';
$input = \rtrim(\fgets(STDIN));

$workflow = new TravelPlannerAgent(new WorkflowState(['query' => $input]));

$interrupted = false;
while (true) {
    if (empty($input)) {
        exit(0);
    }

    try {
        if ($interrupted) {
            $handler = $workflow->start($interrupted, $input);
        } else {
            $handler = $workflow->start();
        }

        foreach ($handler->streamEvents() as $event) {
            if ($event instanceof ProgressEvent) {
                echo $event->message;
            }
        }

        exit(0);
    } catch (WorkflowInterrupt $interrupt) {
        $interrupted = true;
        echo "\n\n========= Interrupted =========\n\n";
        echo "\n\nAgent: ".$interrupt->getData()['question'];
        echo "\n\nYou: ";
        $input = \rtrim(\fgets(STDIN));
    }
}

