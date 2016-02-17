<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\BaseCommand;

class DeployCommand extends BaseCommand
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
