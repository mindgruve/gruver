<?php

namespace Mindgruve\Gruver\Repository;

use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository
{
    const CLASS_NAME = 'Mindgruve\Gruver\Entity\Project';

    public function loadProjectByName($projectName)
    {
        return $this->findOneBy(array('name' => $projectName));
    }
}
