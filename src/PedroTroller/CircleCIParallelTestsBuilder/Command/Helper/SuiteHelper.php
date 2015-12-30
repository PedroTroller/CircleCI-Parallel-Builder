<?php

namespace PedroTroller\CircleCIParallelTestsBuilder\Command\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use PedroTroller\CircleCIParallelTestsBuilder\Suite;
use Symfony\Component\Console\Helper\TableSeparator;
use PedroTroller\CircleCIParallelTestsBuilder\Suite\Test;
use Symfony\Component\Console\Helper\FormatterHelper;

class SuiteHelper extends Helper
{
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function renderSuite(Suite $suite)
    {
        $table = new Table($this->output);

        $table->addRow([$suite->getName(), $suite->getDuration(), '']);
        $table->addRow(new TableSeparator());
        foreach ($suite->getTests() as $test) {
            $table->addRow([$test, $test->getDuration(), $this->getStatus($test)]);
        }

        return $table->render();
    }

    public function renderErrors(Suite $suite)
    {
        $this->renderSuite($suite);

        if (true === $suite->isSuccessful()) {
            return;
        }

        $formatter = new FormatterHelper();

        foreach ($suite->getTests() as $test) {
            if (true === $test->isSuccessful()) {
                continue;
            }

            $this->output->writeln('');

            $lines = [ $test ];

            if ( false === empty($test->getErrorOutput())) {
                $lines[] = '';
                $lines[] = $test->getErrorOutput();
            }

            $this->output->writeln($formatter->formatBlock($lines, 'error', true));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'suite';
    }

    private function getStatus(Test $test)
    {
        if (true === $test->isSuccessful()) {
            return '<fg=green>✔</fg=green>';
        }

        if (true === $test->isStarted()) {
            return '<fg=red>✘</fg=red>';
        }

        return '⌛';
    }
}
