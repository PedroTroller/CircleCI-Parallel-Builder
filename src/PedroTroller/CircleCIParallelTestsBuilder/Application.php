<?php

namespace PedroTroller\CircleCIParallelTestsBuilder;

use PedroTroller\CircleCIParallelTestsBuilder\Command\DisplayCommand;
use PedroTroller\CircleCIParallelTestsBuilder\Command\RunCommand;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $input  = null !== $input ? $input : new ArgvInput();
        $output = null !== $output ? $output : new ConsoleOutput();

        $command = new RunCommand();

        $this->add($command);
        $this->add(new DisplayCommand());
        $this->setDefaultCommand($command->getName());
        $this->configureIO($input, $output);

        parent::run($input, $output);
    }

    protected function getDefaultHelperSet()
    {
        return new HelperSet(['formater' => new FormatterHelper()]);
    }
}
