<?php

namespace Mindgruve\Gruver\Command;

use Symfony\Component\Console\Command\Command;

class PromoteContainerCommand extends Command
{
    const COMMAND = 'promote-container';
    const DESCRIPTION = 'Promote a container to accept live traffic';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }

}