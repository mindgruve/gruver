<?php

namespace Mindgruve\Gruver\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="Mindgruve\Gruver\Repository\ProjectRepository")
 * @Table(name="project",uniqueConstraints={@UniqueConstraint(name="project_name_constraint", columns={"name"})})
 * @HasLifecycleCallbacks
 */
class Project implements StatusInterface
{
    use StatusTrait;
    use TimestampTrait;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(length=140) */
    protected $name;

    /**
     * @OneToMany(targetEntity="Service", mappedBy="project")
     */
    protected $services;

    /**
     * @OneToMany(targetEntity="Release", mappedBy="project")
     */
    protected $releases;

    /** @Column(length=140, nullable=true) */
    protected $configHash;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->releases = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSafeName()
    {
        return preg_replace('/[^\da-z]/i', '_', $this->name);
    }

    /**
     * @param Release $release
     */
    public function addRelease(Release $release)
    {
        if (!$this->releases->contains($release)) {
            $this->releases->add($release);
        }
    }

    /**
     * @param Release $release
     */
    public function removeRelease(Release $release)
    {
        if (!$this->releases->contains($release)) {
            $this->releases->removeElement($release);
        }
    }

    public function getReleases()
    {
        $releases = $this->releases->toArray();

        uasort(
            $releases,
            function ($a, $b) {
                if ($a->getCreatedAt() < $b->getCreatedAt()) {
                    return true;
                }

                return false;
            }
        );

        return $releases;
    }

    /**
     * @param Service $service
     */
    public function addService(Service $service)
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
        }
    }

    /**
     * @param Service $service
     */
    public function removeService(Service $service)
    {
        if (!$this->services->contains($service)) {
            $this->services->removeElement($service);
        }
    }

    public function getServices()
    {
        return $this->services;
    }

    public function getConfigHash()
    {
        return $this->configHash;
    }

    public function setConfigHash($hash)
    {
        $this->configHash = $hash;
    }
}
