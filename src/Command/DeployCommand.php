<?php

namespace Mindgruve\Gruver\Command;

use Symfony\Component\Console\Command\Command;

class DeployCommand extends Command
{

    const COMMAND = 'deploy';
    const DESCRIPTION = 'Builds the container, runs health checks, and if continuous deployment is enabled, will promote the container to production.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }

}