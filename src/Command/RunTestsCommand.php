<?php

namespace Mindgruve\Gruver\Command;

use Symfony\Component\Console\Command\Command;

class RunTestsCommand extends Command
{

    const COMMAND = 'run-tests';
    const DESCRIPTION = 'Run unit tests';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }

}