<?php

namespace Mindgruve\Gruver\Command;

use Symfony\Component\Console\Command\Command;

class SymlinkCommand extends Command
{

    const COMMAND = 'symlink';
    const DESCRIPTION = 'Symlink shared folders';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }
}