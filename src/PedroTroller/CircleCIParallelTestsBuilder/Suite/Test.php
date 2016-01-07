<?php

namespace PedroTroller\CircleCIParallelTestsBuilder\Suite;

use Symfony\Component\Process\Process;

class Test extends Process
{
    private $script;
    private $initialDuration;
    private $duration;

    public function __construct($script, $duration)
    {
        $this->script          = $script;
        $this->initialDuration = $duration;
        $this->enableOutput();

        parent::__construct($script, null, null, null, null, []);
    }

    public function __toString()
    {
        return $this->script;
    }

    public function getInitialDuration()
    {
        return $this->initialDuration;
    }

    public function getDuration()
    {
        if (null !== $this->duration) {
            return $this->duration;
        }

        return $this->initialDuration;
    }

    /**
     * {@inheritdoc}
     */
    public function run($callback = null)
    {
        $time = time();

        $result = parent::run($callback);

        $this->duration = ceil((time() - $time) / 60);

        return $result;
    }
}
