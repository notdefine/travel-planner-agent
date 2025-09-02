<?php

use App\Events\ProgressEvent;
use App\TravelPlannerAgent;
use NeuronAI\Workflow\WorkflowInterrupt;
use NeuronAI\Workflow\WorkflowState;


include __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "\n============ Travel Agent Planner ============\n";

// Interactive console
echo "Where do you want to go?\n> ";
$input = \rtrim(\fgets(STDIN));

$workflow = new TravelPlannerAgent(new WorkflowState(['query' => $input]));

$interrupted = false;
while (true) {
    if (empty($input)) {
        exit(0);
    }

    try {
        if ($interrupted) {
            echo "\nResuming...\n";
            $handler = $workflow->wakeup($input);
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
        echo "\n\nAgent: ".$interrupt->getData()['question'];
        echo "\n\nYou: ";
        $input = \rtrim(\fgets(STDIN));
    }
}

