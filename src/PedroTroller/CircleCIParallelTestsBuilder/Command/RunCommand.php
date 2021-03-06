<?php

namespace PedroTroller\CircleCIParallelTestsBuilder\Command;

use PedroTroller\CircleCIParallelTestsBuilder\Command\Helper\SuiteHelper;
use PedroTroller\CircleCIParallelTestsBuilder\SuiteBuilder\SuiteBuilder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

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

        if (false === array_key_exists($index, $suites)) {
            return;
        }

        $suite = $suites[$index];

        $output->writeln('');
        $output->writeln(
            $this
                ->getHelper('formater')
                ->formatBlock([$suite->getName()], 'bg=green;fg=black', true)
        );

        $output->writeln('');

        $helper = new SuiteHelper($output);

        foreach ($suite->getTests() as $test) {
            $output->writeln(
                $this
                    ->getHelper('formater')
                    ->formatBlock([sprintf('Test : %s', $test)], 'info', true)
            );
            $output->writeln('');

            $test->run(
                function ($e) use ($test, $helper) {
                    $helper->renderTestLine($test);
                }
            );
            $output->writeln($test->getIncrementalOutput());

            if (false === $test->isSuccessful()) {
                $output->writeln(
                    $this
                        ->getHelper('formater')
                        ->formatBlock([sprintf('"%s" failed', $test)], 'error', true)
                );
            } else {
                $output->writeln(
                    $this
                        ->getHelper('formater')
                        ->formatBlock([sprintf('"%s" succeed', $test)], 'bg=green;fg=black', true)
                );
            }
            $output->writeln('');
        }

        $helper->renderErrors($suite);

        $output->writeln('');

        return $suite->isSuccessful() ? 0 : 1;
    }
}
