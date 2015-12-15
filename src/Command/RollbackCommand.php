<?php

namespace Mindgruve\Gruver\Command;

use Symfony\Component\Console\Command\Command;

class RollbackCommand extends Command
{

    const COMMAND = 'rollback';
    const DESCRIPTION = 'Rollback a deployment and use previous container.';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }


}