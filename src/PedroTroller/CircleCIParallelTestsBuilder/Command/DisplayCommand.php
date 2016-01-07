<?php

namespace PedroTroller\CircleCIParallelTestsBuilder\Command;

use PedroTroller\CircleCIParallelTestsBuilder\Command\Helper\SuiteHelper;
use PedroTroller\CircleCIParallelTestsBuilder\SuiteBuilder\SuiteBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class DisplayCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('display')
            ->setDescription('Display suites sets')
            ->addArgument('tests', InputArgument::OPTIONAL, 'Tests file', sprintf('%s/circle-tests.yml', getcwd()))
            ->addOption('total', 't', InputOption::VALUE_OPTIONAL, 'Number of parallel builds. By default, the content os the CIRCLE_NODE_TOTAL environment variable.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $input->getOption('total')) {
            $input->setOption('total', getenv('CIRCLE_NODE_TOTAL') ?: 1);
        }

        $config = Yaml::parse(file_get_contents($input->getArgument('tests')));
        $suites = (new SuiteBuilder($config))->buildSuites($input->getOption('total'));

        $output->writeln(
            $this
                ->getHelper('formater')
                ->formatBlock(['Available suites'], 'bg=green;fg=black', true)
        );
        $output->writeln('');

        $helper = new SuiteHelper($output);

        foreach ($suites as $suite) {
            $helper->renderSuite($suite);
        }
    }
}
