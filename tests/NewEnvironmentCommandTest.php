<?php

namespace Swisscat\DockerCsCart\Command;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;

class NewEnvironmentCommandTest extends TestCase
{
    protected function setUp()
    {
        file_exists($dir = __DIR__.'/fixtures/cscart-dir/var') && $this->cleanupDir($dir);
        file_exists($file = __DIR__.'/fixtures/cscart-dir/docker-compose.yml') && unlink($file);
    }

    protected function tearDown()
    {
        file_exists($dir = __DIR__.'/fixtures/cscart-dir/var') && $this->cleanupDir($dir);
        file_exists($file = __DIR__.'/fixtures/cscart-dir/docker-compose.yml') && unlink($file);
    }

    private function cleanupDir(string $dir): void
    {
        $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
            RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
    }

    public function testErrorMessageOnInvalidDirectory()
    {
        $cmd = $this->getMockBuilder(NewEnvironmentCommand::class)
            ->setMethods(['getCurrentDirectory'])
            ->getMock();

        $cmd->expects($this->once())
            ->method('getCurrentDirectory')
            ->willReturn(__DIR__.'/fixtures/nocscart-dir');

        $output = $this->getMockBuilder(OutputInterface::class)
            ->getMock();

        $output->expects($this->once())
            ->method('writeln')
            ->with([
                sprintf('<error>Directory %s does not appear to contain a cs-cart installation.</error>', __DIR__.'/fixtures/nocscart-dir'),
                '<error>Please execute this command from a cs-cart root folder</error>'
            ])
        ;

        $cmd->execute(new ArgvInput(), $output);
    }

    public function testOnValidDirectory()
    {
        $cmd = $this->getMockBuilder(NewEnvironmentCommand::class)
            ->setMethods(['getCurrentDirectory'])
            ->getMock();

        $cmd->expects($this->once())
            ->method('getCurrentDirectory')
            ->willReturn(__DIR__.'/fixtures/cscart-dir');

        $output = $this->getMockBuilder(OutputInterface::class)
            ->getMock();

        $output->expects($this->once())
            ->method('writeln')
            ->with(sprintf('<info>Directory %s setup successfully.</info>', __DIR__.'/fixtures/cscart-dir'));

        $cmd->execute(new ArgvInput(), $output);

        $this->assertFileExists(__DIR__.'/fixtures/cscart-dir/var/toolbox.sh');
        $this->assertDirectoryExists(__DIR__.'/fixtures/cscart-dir/var/config');
    }

    public function testOverwriteDirectory()
    {
        $cmd = $this->getMockBuilder(NewEnvironmentCommand::class)
            ->setMethods(['getCurrentDirectory'])
            ->getMock();

        $cmd->expects($this->any())
            ->method('getCurrentDirectory')
            ->willReturn(__DIR__.'/fixtures/cscart-dir');

        $output = $this->getMockBuilder(OutputInterface::class)
            ->getMock();

        $output->expects($this->any())
            ->method('writeln')
            ->with(sprintf('<info>Directory %s setup successfully.</info>', __DIR__.'/fixtures/cscart-dir'));

        $cmd->execute(new ArgvInput(), $output);
        $cmd->execute($inp = new ArgvInput(['bin/docker-cscart', '--overwrite'], $cmd->getDefinition()), $output);

        $this->assertFileExists(__DIR__.'/fixtures/cscart-dir/var/toolbox.sh');
        $this->assertDirectoryExists(__DIR__.'/fixtures/cscart-dir/var/config');
    }

    public function testGetCwd()
    {
        $cmd = new NewEnvironmentCommand();

        $output = $this->getMockBuilder(OutputInterface::class)
            ->getMock();

        $output->expects($this->once())
            ->method('writeln')
        ;

        $cmd->execute(new ArgvInput(), $output);
    }
}
