<?php

namespace Swisscat\DockerCsCart\Command;

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
            ->addOption('vhost',
                null,
                InputOption::VALUE_REQUIRED,
                'The vhost to use')
            ;

        parent::configure();
    }

    const DefaultVhostTemplate = 'cscart-%s.local';

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

        $overwrite = $input->getOption('overwrite');

        if (!$overwrite && file_exists($currentDirectory.'/docker-compose.yml')) {
            $output->writeln([
                sprintf('<error>A docker configuration has already been found.</error>', $currentDirectory),
                '<error>Please specify the overwrite option to replace the existing one.</error>'
            ]);

            return 10;
        }

        $this->copy(Application::getResourcesDirectory(), $currentDirectory,['vhost' => $vhost = $input->getOption('vhost') ?: sprintf(self::DefaultVhostTemplate, substr(md5($currentDirectory),0,3)),'overwrite' => $input->hasOption('overwrite')]);

        $output->writeln(sprintf('<info>Directory %s setup successfully.</info>', $currentDirectory));
    }

    protected function copy(string $from, string $to, array $params = [])
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);

        $overwrite = $params['overwrite'] ?? false;

        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            $filePath = $file->getPathname();

            $pos = strpos($filePath, $from);

            $destPath = substr_replace($filePath, $to, $pos, strlen($from));

            if ($file->isDir()) {
                if (!file_exists($destPath)) {
                    mkdir($destPath);
                }
            } else {
                if (file_exists($destPath) && $overwrite) {
                    unlink($destPath);
                }

                copy($filePath, $destPath);
            }

            switch ($file->getFilename()) {
                case 'docker-compose.yml':
                    file_put_contents($destPath, str_replace('<vhost>', $params['vhost'], file_get_contents($destPath)));
            }
        }

        return true;
    }

    protected function getCurrentDirectory()
    {
        return getcwd();
    }
}