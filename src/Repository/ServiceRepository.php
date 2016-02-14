<?php

namespace Mindgruve\Gruver\Repository;

use Doctrine\ORM\EntityRepository;
use Mindgruve\Gruver\Entity\Service;

class ServiceRepository extends EntityRepository
{

    const CLASS_NAME = 'Mindgruve\Gruver\Entity\Service';

    public function getServiceOrCreate($serviceName)
    {
        $em = $this->getEntityManager();
        $service = $this->findOneBy(array('name' => $serviceName));

        if (!$service) {
            $service = new Service();
            $service->setName($serviceName);
            $em->persist($service);
            $em->flush($service);
        }

        return $service;
    }
}