<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Command;
use Symfony\Component\Console\Input\InputArgument;

class CheckCommand extends Command
{
    const COMMAND = 'check';
    const DESCRIPTION = 'Run health checks on an application.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }

}