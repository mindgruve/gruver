<?php

namespace Mindgruve\Gruver\Entity;

/**
 * @Entity(repositoryClass="Mindgruve\Gruver\Repository\ServiceRepository")
 * @Table(name="service",uniqueConstraints={@UniqueConstraint(name="service_name_constraint", columns={"name"})})
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
     * @Column(type="datetime",name="created_at")
     */
    protected $createdAt;

    /**
     * @Column(type="datetime",name="modified_at")
     */
    protected $modifiedAt;

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
     * @return $this
     */
    public function setCreatedAt(\DateTime $dateTime)
    {
        $this->createdAt = $dateTime;

        return $this;
    }

    public function getCurrentRelease()
    {
        return $this->currentRelease;
    }

    public function setCurrentRelease(Release $release)
    {
        $this->currentRelease = $release;

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
