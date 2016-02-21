<?php

namespace Mindgruve\Gruver\Entity;

/**
 * @Entity(repositoryClass="Mindgruve\Gruver\Repository\ReleaseRepository")
 * @Table(name="release",uniqueConstraints={@UniqueConstraint(name="tag_constraint", columns={"service_id","tag"})})
 * @HasLifecycleCallbacks
 */
class Release implements StatusInterface
{
    use StatusTrait;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(length=140)
     */
    protected $tag;

    /**
     * @ManyToOne(targetEntity="Project", inversedBy="releases")
     * @JoinColumn(name="project_id", referencedColumnName="id", nullable=false)
     */
    protected $project;

    /**
     * @ManyToOne(targetEntity="Service", inversedBy="releases")
     * @JoinColumn(name="service_id", referencedColumnName="id", nullable=false)
     */
    protected $service;

    /**
     * @ManyToOne(targetEntity="Release")
     * @JoinColumn(name="previous_release_id", referencedColumnName="id")
     */
    protected $previousRelease;

    /**
     * @ManyToOne(targetEntity="Release")
     * @JoinColumn(name="next_release_id", referencedColumnName="id")
     */
    protected $nextRelease;

    /**
     * @Column(type="datetime",name="created_at")
     */
    protected $createdAt;

    /**
     * @Column(type="datetime",name="modified_at")
     */
    protected $modifiedAt;

    /**
     * @Column(length=140)
     */
    protected $containerId;

    /**
     * @Column(length=140)
     */
    protected $containerIp;

    /**
     * @Column(length=140)
     */
    protected $containerPort;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param Service $service
     *
     * @return $this
     */
    public function setService(Service $service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @param $tag
     *
     * @return $this
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    public function getPreviousRelease()
    {
        return $this->previousRelease;
    }

    /**
     * @param Release $release
     *
     * @return $this
     */
    public function setPreviousRelease(Release $release)
    {
        $this->previousRelease = $release;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNextRelease()
    {
        return $this->nextRelease;
    }

    /**
     * @param Release $release
     *
     * @return $this
     */
    public function setNextRelease(Release $release)
    {
        $this->nextRelease = $release;

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

    public function getContainerId()
    {
        return $this->containerId;
    }

    public function setContainerId($containerId)
    {
        $this->containerId = $containerId;

        return $this;
    }

    public function getContainerIp()
    {
        return $this->containerIp;
    }

    public function setContainerIp($ip)
    {
        $this->containerIp = $ip;

        return $this;
    }

    public function getContainerPort()
    {
        return $this->containerPort;
    }

    public function setContainerPort($port)
    {
        $this->containerPort = $port;

        return $this;
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

    public function getProject()
    {
        return $this->project;
    }

    public function setProject(Project $project)
    {
        $this->project = $project;

        return $this;
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
}
