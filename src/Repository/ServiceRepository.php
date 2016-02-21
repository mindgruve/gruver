<?php

namespace Mindgruve\Gruver\Repository;

use Doctrine\ORM\EntityRepository;
use Mindgruve\Gruver\Entity\Project;
use Mindgruve\Gruver\Entity\StatusInterface;

class ServiceRepository extends EntityRepository
{
    const CLASS_NAME = 'Mindgruve\Gruver\Entity\Service';

    public function loadServiceByName(Project $project, $serviceName)
    {
        return $this->findOneBy(array('project' => $project, 'name' => $serviceName));
    }

    public function findAll(Project $project = null, $status = StatusInterface::STATUS_ENABLED)
    {
        $criteria = array();

        if ($project) {
            $criteria['project'] = $project;
        }
        if ($status) {
            $criteria['status'] = $status;
        }

        return $this->findBy($criteria);
    }
}
