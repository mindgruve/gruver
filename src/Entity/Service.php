<?php

namespace Mindgruve\Gruver\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="Mindgruve\Gruver\Repository\ServiceRepository")
 * @Table(name="service",uniqueConstraints={@UniqueConstraint(name="service_name_constraint", columns={"project_id","name"})})
 * @HasLifecycleCallbacks
 */
class Service
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(length=140) */
    protected $name;

    /** @Column(type="array") */
    protected $hosts;

    /** @Column(length=8) */
    protected $publicPort;

    /**
     * @ManyToOne(targetEntity="Project", inversedBy="services")
     * @JoinColumn(name="project_id", referencedColumnName="id", nullable=false)
     */
    protected $project;

    /**
     * @OneToMany(targetEntity="Release", mappedBy="service")
     */
    protected $releases;

    /**
     * @OneToOne(targetEntity="Release")
     * @JoinColumn(name="current_release_id", referencedColumnName="id")
     */
    protected $currentRelease;

    /**
     * @OneToOne(targetEntity="Release")
     * @JoinColumn(name="pending_release_id", referencedColumnName="id")
     */
    protected $pendingRelease;

    /**
     * @OneToOne(targetEntity="Release")
     * @JoinColumn(name="most_recent_release_id", referencedColumnName="id")
     */
    protected $mostRecentRelease;

    /**
     * @OneToOne(targetEntity="Release")
     * @JoinColumn(name="rollback_release_id", referencedColumnName="id")
     */
    protected $rollbackRelease;

    /**
     * @Column(type="datetime",name="created_at")
     */
    protected $createdAt;

    /**
     * @Column(type="datetime",name="modified_at")
     */
    protected $modifiedAt;

    public function __construct()
    {
        $this->releases = new ArrayCollection();
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return $this
     */
    public function setModifiedAt(\DateTime $dateTime)
    {
        $this->modifiedAt = $dateTime;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $dateTime)
    {
        $this->createdAt = $dateTime;

        return $this;
    }

    /**
     * @return Release
     */
    public function getCurrentRelease()
    {
        return $this->currentRelease;
    }

    /**
     * @param Release $release
     *
     * @return $this
     */
    public function setCurrentRelease(Release $release = null)
    {
        $this->currentRelease = $release;

        return $this;
    }

    /**
     * @return Release
     */
    public function getPendingRelease()
    {
        if ($this->currentRelease) {
            return $this->currentRelease->getNextRelease();
        }

        if ($this->mostRecentRelease) {
            return $this->mostRecentRelease;
        }

        return null;
    }


    /**
     * @return Release
     */
    public function getRollbackRelease()
    {
        if ($this->currentRelease) {
            return $this->currentRelease->getPreviousRelease();
        }

        return null;
    }


    public function setMostRecentRelease(Release $release = null)
    {
        $this->mostRecentRelease = $release;

        return $this;
    }

    public function getMostRecentRelease()
    {
        return $this->mostRecentRelease;
    }

    /**
     * @PrePersist
     * @PreUpdate
     */
    public function updateTimestamps()
    {
        $date = new \DateTime(date('Y-m-d H:i:s'));
        $this->setModifiedAt($date);

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt($date);
        }
    }

    public function getProject()
    {
        return $this->project;
    }

    public function setProject(Project $project)
    {
        $this->project = $project;

        return $this;
    }

    public function setHosts(array $hosts)
    {
        $this->hosts = $hosts;
    }

    public function getHosts()
    {
        return $this->hosts;
    }

    public function setPublicPort($port)
    {
        $this->publicPort = $port;
    }

    public function getPublicPort()
    {
        return $this->publicPort;
    }
}
