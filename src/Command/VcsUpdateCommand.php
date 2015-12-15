<?php

namespace Mindgruve\Gruver\Command;

use Symfony\Component\Console\Command\Command;

class VcsUpdateCommand extends Command
{

    const COMMAND = 'vcs-update';
    const DESCRIPTION = 'Update source code though vcs';

    public function configure()
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION);
    }

}