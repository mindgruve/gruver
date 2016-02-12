<?php

namespace Mindgruve\Gruver\Command;

use Mindgruve\Gruver\Command;

class PromoteCommand extends Command
{
    const COMMAND = 'promote';
    const DESCRIPTION = 'Promote a container to accept live traffic.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }
}
