<?php

namespace Swisscat\DockerCsCart\Test;

use PHPUnit\Framework\TestCase;
use Swisscat\DockerCsCart\Application;

class ApplicationTest extends TestCase
{
    public function testCommandsAreAvailable()
    {
        $app = new Application();

        $this->assertTrue($app->has('new'));
    }
}
