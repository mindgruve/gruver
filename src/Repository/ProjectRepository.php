<?php

namespace Mindgruve\Gruver\Repository;

use Doctrine\ORM\EntityRepository;
use Mindgruve\Gruver\Entity\StatusInterface;

class ProjectRepository extends EntityRepository
{
    const CLASS_NAME = 'Mindgruve\Gruver\Entity\Project';

    public function loadProjectByName($projectName)
    {
        return $this->findOneBy(array('name' => $projectName));
    }

    public function findAll($status = StatusInterface::STATUS_ENABLED)
    {
        $criteria = array();

        if ($status) {
            $criteria['status'] = $status;
        }

        return $this->findBy($criteria);
    }
}
