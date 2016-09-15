<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\Samples\Pubsub\SubscriptionCommand;
use Google\Cloud\Samples\Pubsub\TopicCommand;
use Google\Cloud\Samples\Pubsub\IamCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new SubscriptionCommand());
$application->add(new TopicCommand());
$application->add(new IamCommand());
$application->run();
