<?php

namespace Mindgruve\Gruver\Repository;

use Doctrine\ORM\EntityRepository;
use Mindgruve\Gruver\Entity\Service;

class ReleaseRepository extends EntityRepository
{

    public function tagExistsForService(Service $service, $tag)
    {
        $results = $this->findOneBy(array('service' => $service, 'tag' => $tag));

        if ($results) {
            return true;
        }

        return false;
    }

}