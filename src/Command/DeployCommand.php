<?php

namespace Mindgruve\Gruver\Command;

use Symfony\Component\Console\Command\Command;

class DeployCommand extends Command
{

    const COMMAND = 'deploy';
    const DESCRIPTION = 'Equivalent to running update-source + build-container + run-tests + run-health-checks + promote-container';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }

}