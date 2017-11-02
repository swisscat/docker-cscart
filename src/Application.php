<?php

namespace Swisscat\DockerCsCart;

use Swisscat\DockerCsCart\Command\NewEnvironmentCommand;
use Symfony\Component\Console\Application as ConsoleApplication;

class Application extends ConsoleApplication
{
    const Version = '0.0.0-snapshot';

    const Name = 'Docker CS Cart';

    public function __construct()
    {
        parent::__construct(self::Name, self::Version);
    }

    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), [
           new NewEnvironmentCommand(),
        ]);
    }

    public static function getResourcesDirectory(): string
    {
        return dirname(__DIR__).'/cscart-data';
    }
}