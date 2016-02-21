<?php

namespace Mindgruve\Gruver\Repository;

use Doctrine\ORM\EntityRepository;
use Mindgruve\Gruver\Entity\Project;
use Mindgruve\Gruver\Entity\Service;
use Mindgruve\Gruver\Entity\StatusInterface;

class ReleaseRepository extends EntityRepository
{
    public function checkIfTagExists(Project $project, Service $service, $tag)
    {
        $result = $this->findReleaseByTag($project, $service, (string)$tag);

        if ($result) {
            return true;
        }

        return false;
    }

    public function findReleaseByTag(Project $project, Service $service, $tag)
    {
        return $this->findOneBy(array('project' => $project, 'service' => $service, 'tag' => (string)$tag));
    }

    public function findAll(Project $project = null, Service $service = null, $status = StatusInterface::STATUS_ENABLED)
    {
        $criteria = array();

        if ($project) {
            $criteria['project'] = $project;
        }

        if ($service) {
            $criteria['service'] = $service;
        }

        if ($status) {
            $criteria['status'] = $status;
        }

        return $this->findBy($criteria);
    }
}
