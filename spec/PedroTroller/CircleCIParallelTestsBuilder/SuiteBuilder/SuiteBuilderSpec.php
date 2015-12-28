<?php

namespace spec\PedroTroller\CircleCIParallelTestsBuilder\SuiteBuilder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SuiteBuilderSpec extends ObjectBehavior
{
    function let()
    {
        $suites = [
            'behat -di' => ['weigth' => 1],
            'behat features/api' => ['weigth' => 10],
            'behat features/front' => ['weigth' => 7],
            'phpspec run' => ['weigth' => 3],
            'app/console do:sc:va' => ['weigth' => 1],
            'behat features/manager' => ['weigth' => 16],
        ];

        $this->beConstructedWith($suites);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('PedroTroller\CircleCIParallelTestsBuilder\SuiteBuilder\SuiteBuilder');
    }

    function it_returns_a_single_suite_if_asked()
    {
        $this->buildSuites()->shouldReturn([
            [
                'app/console do:sc:va',
                'behat -di',
                'behat features/api',
                'behat features/front',
                'behat features/manager',
                'phpspec run',
            ]
        ]);
    }

    function it_paginates_suites()
    {
        $this->buildSuites(3)->shouldReturn([
            [
                'behat features/manager',
            ],
            [
                'app/console do:sc:va',
                'behat -di',
                'behat features/api',
            ],
            [
                'behat features/front',
                'phpspec run',
            ]
        ]);

        $this->buildSuites(4)->shouldReturn([
            [
                'behat features/manager',
            ],
            [
                'behat features/api',
            ],
            [
                'app/console do:sc:va',
                'behat -di',
                'behat features/front',
            ],
            [
                'phpspec run',
            ],
        ]);
    }
}
