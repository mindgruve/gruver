<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Command;

class DeployCommand extends Command
{
    const COMMAND = 'deploy';
    const DESCRIPTION = 'Deploy an application.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }
}
