#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/config/logger.php';
require __DIR__.'/config/load_config.php';
require __DIR__.'/config/load_db.php';

date_default_timezone_set('Africa/Kinshasa');


use Symfony\Component\Console\Application;
use App\Command\RetrieveFileCommand;
use App\Command\LoadingDataFtp;
use App\Command\LoadingData;
use App\Command\ProcessCommand;

$application = new Application();
$exitcode = $application->run();
exit($exitcode);