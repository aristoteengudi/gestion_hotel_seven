#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/config/logger.php';
require __DIR__.'/config/load_config.php';
require __DIR__.'/config/load_db.php';

date_default_timezone_set('Africa/Kinshasa');


use Symfony\Component\Console\Application;
use App\Command\CreateUser;

$application = new Application();
$application->add(new CreateUser());
$exitcode = $application->run();
exit($exitcode);