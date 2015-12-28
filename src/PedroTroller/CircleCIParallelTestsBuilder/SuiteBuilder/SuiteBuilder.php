<?php

namespace PedroTroller\CircleCIParallelTestsBuilder\SuiteBuilder;

class SuiteBuilder
{
    /**
     * @var array
     */
    private $suites;

    public function __construct(array $suites)
    {
        $this->suites = $suites;

        foreach ($this->suites as $command => $test) {
            $this->suites[$command] = $this->normalize($test);
        }
    }

    public function buildSuites($number = 1)
    {
        $weigths = [];

        foreach ($this->suites as $command => $test) {
            $weigths[] = $test['weigth'];
        }

        $average = floor(array_sum($weigths) / $number);

        $suites = $this->suites;

        array_multisort($weigths, SORT_ASC, SORT_REGULAR, $suites);

        $result = [];

        for ($i = 0; $i < $number; $i ++) {
            $suite = [];
            $test = end($suites);
            $command = key($suites);
            $weigth = $test['weigth'];
            $suite[] = $command;
            array_pop($suites);

            while($weigth < $average && false === empty($suites)) {
                $test = reset($suites);
                $command = key($suites);
                $weigth = $weigth + $test['weigth'];
                $suite[] = $command;
                array_shift($suites);
            }

            $result[] = $suite;
        }

        foreach ($result as $index => $suite) {
            sort($suite);
            $result[$index] = $suite;
        }

        return $result;
    }

    private function normalize(array $test)
    {
        return array_merge([
            'weigth' => 1
        ], $test);
    }
}
