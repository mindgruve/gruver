<?php

namespace Mindgruve\Gruver\Command;

use Symfony\Component\Console\Command\Command;

class BuildContainerCommand extends Command
{

    const COMMAND = 'build-container';
    const DESCRIPTION = 'Build the container';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }

}