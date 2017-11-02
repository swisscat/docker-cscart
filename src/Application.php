<?php

namespace Swisscat\DockerCsCart;

use Swisscat\DockerCsCart\Command\NewEnvironmentCommand;
use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), [
           new NewEnvironmentCommand(),
        ]);
    }

    public static function getResourcesDirectory(): string
    {
        return dirname(__DIR__).'/var';
    }
}