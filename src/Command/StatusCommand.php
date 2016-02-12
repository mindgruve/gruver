<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Command;

class StatusCommand extends Command
{
    const COMMAND = 'status';
    const DESCRIPTION = 'Deployment status.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }
}
