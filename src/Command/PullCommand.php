<?php

namespace Mindgruve\Gruver\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class PullCommand extends Command
{
    const COMMAND = 'pull';
    const DESCRIPTION = 'Pull a container from a docker repository.';


    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }
}