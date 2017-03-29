<?php
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Symfony\Component\Console\Application;
use NeedleProject\FlowMeter\Command\MonitorCommand;

$application = new Application();
$application->add(new MonitorCommand());
$application->run();
