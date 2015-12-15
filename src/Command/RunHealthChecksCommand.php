<?php

namespace Mindgruve\Gruver\Command;

use Symfony\Component\Console\Command\Command;

class RunHealthChecksCommand extends Command
{

    const COMMAND = 'run-health-checks';
    const DESCRIPTION = 'Run health checks';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }

}