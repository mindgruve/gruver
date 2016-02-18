<?php

namespace Mindgruve\Gruver\Repository;

use Doctrine\ORM\EntityRepository;
use Mindgruve\Gruver\Entity\Project;
use Mindgruve\Gruver\Entity\Service;

class ReleaseRepository extends EntityRepository
{
    public function checkIfTagExists(Project $project, Service $service, $tag)
    {
        $results = $this->findOneBy(array('project' => $project, 'service' => $service, 'tag' => $tag));

        if ($results) {
            return true;
        }

        return false;
    }

    public function findReleaseByTag($project, $service, $tag)
    {

    }
}
