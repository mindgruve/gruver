<?php

namespace Mindgruve\Gruver\Command;

use Symfony\Component\Console\Command\Command;

class CleanupCommand extends Command
{
    const COMMAND = 'cleanup';
    const DESCRIPTION = 'Remove unused containers.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }
}