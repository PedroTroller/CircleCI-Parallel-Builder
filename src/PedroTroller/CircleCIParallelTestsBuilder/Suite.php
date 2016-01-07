<?php

namespace PedroTroller\CircleCIParallelTestsBuilder;

use PedroTroller\CircleCIParallelTestsBuilder\Suite\Test;

class Suite
{
    private $index;
    private $tests;

    public function __construct($index)
    {
        $this->index = $index;
        $this->tests = [];
    }

    public function getName()
    {
        return sprintf('Suite #%d', $this->index + 1);
    }

    public function addTest(Test $test)
    {
        $this->tests[(string) $test] = $test;
    }

    public function getTests()
    {
        ksort($this->tests);

        return $this->tests;
    }

    public function getDuration()
    {
        $duration = 0;

        foreach ($this->tests as $test) {
            $duration = $duration + $test->getDuration();
        }

        return $duration;
    }

    public function isSuccessful()
    {
        $successful = true;

        foreach ($this->tests as $test) {
            if (false === $test->isSuccessful()) {
                $successful = false;
            }
        }

        return $successful;
    }
}
