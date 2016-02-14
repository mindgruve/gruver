<?php

namespace Mindgruve\Gruver\Entity;

/**
 * @Entity(repositoryClass="Mindgruve\Gruver\Repository\ReleaseRepository")
 * @Table(name="release")
 */
class Release
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(length=140) */
    protected $tag;

    /**
     * @ManyToOne(targetEntity="Service", inversedBy="releases")
     * @JoinColumn(name="service_id", referencedColumnName="id")
     */
    protected $service;

    /**
     * @OneToOne(targetEntity="Release")
     * @JoinColumn(name="previous_release_id", referencedColumnName="id")
     */
    protected $previousRelease;

    /**
     * @OneToOne(targetEntity="Release")
     * @JoinColumn(name="next_release_id", referencedColumnName="id")
     */
    protected $nextRelease;

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
    public function getService(){
        return $this->service;
    }

    /**
     * @param Service $service
     * @return $this
     */
    public function setService(Service $service){
        $this->service = $service;
        return $this;
    }

    /**
     * @param $tag
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
     * @return $this
     */
    public function setNextRelease(Release $release)
    {
        $this->nextRelease = $release;

        return $this;
    }
}

