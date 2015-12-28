<?php

namespace PedroTroller\CircleCIParallelTestsBuilder\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use PedroTroller\CircleCIParallelTestsBuilder\Command\DisplayCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Yaml\Yaml;
use PedroTroller\CircleCIParallelTestsBuilder\SuiteBuilder\SuiteBuilder;
use Symfony\Component\Process\Process;

class RunCommand extends DisplayCommand
{
    public function configure()
    {
        parent::configure();

        $this
            ->setName('run')
            ->setDescription('Build and run a tests suite for the current Circle node.')
            ->addOption('index', 'i', InputOption::VALUE_OPTIONAL, 'Index of the current suite test. By default, the content os the CIRCLE_NODE_INDEX environment variable.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        if (null === $input->getOption('index')) {
            $input->setOption('index', getenv('CIRCLE_NODE_INDEX') ?: 1);
        }

        $config = Yaml::parse(file_get_contents($input->getArgument('tests')));
        $suites = (new SuiteBuilder($config))->buildSuites($input->getOption('total'));

        $index = $input->getOption('index');

        $output->writeln('');
        $output->writeln(
            $this
                ->getHelper('formater')
                ->formatBlock([sprintf('Suite #%s', $index)], 'bg=green;fg=black', true)
        );
        $output->writeln('');

        foreach ($suites[$index-1] as $command) {
            $output->writeln(
                $this
                    ->getHelper('formater')
                    ->formatBlock([sprintf('Test : %s', $command)], 'bg=yellow;fg=black')
            );
            $output->writeln('');

            $process = new Process($command, null, null, null, null, array());
            $process->enableOutput();
            $process->run(
                function ($e) use ($output, $process) {
                    $output->write($process->getIncrementalOutput());
                }
            );
            $output->writeln($process->getIncrementalOutput());

            if (false === $process->isSuccessful()) {
                $output->writeln(
                    $this
                        ->getHelper('formater')
                        ->formatBlock([sprintf('"%s" failed', $command)], 'error')
                );
            } else {
                $this->setCode(1);
                $output->writeln(
                    $this
                        ->getHelper('formater')
                        ->formatBlock([sprintf('"%s" succeed', $command)], 'bg=green;fg=black', true)
                );
            }
            $output->writeln('');
        }
    }
}
