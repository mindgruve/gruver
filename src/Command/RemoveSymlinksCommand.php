<?php

namespace Mindgruve\Gruver\Command;

use Symfony\Component\Console\Command\Command;

class RemoveSymlinksCommand extends Command
{
    const COMMAND = 'remove-symlinks';
    const DESCRIPTION = 'Remove the symlinks to shared directories.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }
}