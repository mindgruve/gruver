<?php

namespace Mindgruve\Gruver\Repository;

use Doctrine\ORM\EntityRepository;
use Mindgruve\Gruver\Entity\Project;
use Mindgruve\Gruver\Entity\Service;

class ReleaseRepository extends EntityRepository
{
    public function checkIfTagExists(Project $project, Service $service, $tag)
    {
        $result = $this->findReleaseByTag($project, $service, (string) $tag);

        if ($result) {
            return true;
        }

        return false;
    }

    public function findReleaseByTag(Project $project, Service $service, $tag)
    {
        return $this->findOneBy(array('project' => $project, 'service' => $service, 'tag' => (string)$tag));
    }
}
