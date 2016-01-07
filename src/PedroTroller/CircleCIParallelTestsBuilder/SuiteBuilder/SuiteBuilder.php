<?php

namespace PedroTroller\CircleCIParallelTestsBuilder\SuiteBuilder;

use PedroTroller\CircleCIParallelTestsBuilder\Suite;
use PedroTroller\CircleCIParallelTestsBuilder\Suite\Test;

class SuiteBuilder
{
    /**
     * @var array
     */
    private $tests;

    public function __construct(array $tests)
    {
        foreach ($tests as $script => $duration) {
            $process                        = new Test($script, $duration);
            $this->tests[(string) $process] = $process;
        }
    }

    public function buildSuites($number = 1)
    {
        $suites = [];

        for ($i = 0; $i < $number; ++$i) {
            $suites[] = new Suite($i);
        }

        $weigths  = [];
        $commands = $this->tests;

        foreach ($commands as $command) {
            $weigths[] = $command->getInitialDuration();
        }

        array_multisort($weigths, SORT_DESC, SORT_REGULAR, $commands);

        foreach ($commands as $command) {
            $this->getShorterSuite($suites)->addTest($command);
        }

        return $suites;
    }

    private function getShorterSuite(array $suites)
    {
        $min = null;

        foreach ($suites as $suite) {
            if (null === $min || $suite->getDuration() < $min->getDuration()) {
                $min = $suite;
            }
        }

        return $min;
    }
}
