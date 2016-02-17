<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\BaseCommand;

class CheckCommand extends BaseCommand
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
