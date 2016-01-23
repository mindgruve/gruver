<?php

namespace Mindgruve\Gruver\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

class RollbackCommand extends Command
{
    use GruverCommandTrait;

    const COMMAND = 'rollback';
    const DESCRIPTION = 'Rollback a deployment and use previous container.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }


}