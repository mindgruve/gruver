<?php

namespace Mindgruve\Gruver\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

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