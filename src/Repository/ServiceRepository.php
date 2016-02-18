<?php

namespace Mindgruve\Gruver\Repository;

use Doctrine\ORM\EntityRepository;
use Mindgruve\Gruver\Entity\Project;

class ServiceRepository extends EntityRepository
{
    const CLASS_NAME = 'Mindgruve\Gruver\Entity\Service';

    public function loadServiceByName(Project $project, $serviceName){
        return $this->findOneBy(array('project' => $project, 'name' => $serviceName));
    }
}
