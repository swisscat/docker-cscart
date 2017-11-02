<?php

namespace Swisscat\DockerCsCart\Command;

use InvalidArgumentException;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Swisscat\DockerCsCart\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NewEnvironmentCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('new')
            ->setDescription('Provision a new environment (if none currently exists)')
            ->addOption(
                'overwrite',
                null,
                InputOption::VALUE_NONE,
                'Override current installation if existing found'
            )
            ;

        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $currentDirectory = $this->getCurrentDirectory();

        if (!file_exists($currentDirectory.'/config.local.php')) {
            $output->writeln([
                sprintf('<error>Directory %s does not appear to contain a cs-cart installation.</error>', $currentDirectory),
                '<error>Please execute this command from a cs-cart root folder</error>'
            ]);

            return 10;
        }

        $this->copy(Application::getResourcesDirectory(), $currentDirectory);

        $output->writeln(sprintf('<info>Directory %s setup successfully.</info>', $currentDirectory));
    }

    protected function copy(string $from, string $to, array $params = [])
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            $filePath = $file->getPathname();

            $pos = strpos($filePath, $from);

            $destPath = substr_replace($filePath, $to, $pos, strlen($from));

            if ($file->isDir()) {
                mkdir($destPath);
            } else {
                copy($filePath, $destPath);
            }
        }

        return true;
    }

    protected function getCurrentDirectory()
    {
        return getcwd();
    }
}