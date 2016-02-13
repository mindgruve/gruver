<?php

namespace Mindgruve\Gruver\Entity;

/**
 * @Entity
 * @Table(name="application")
 */
class Application
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(length=140) */
    protected $name;

    /**
     * @OneToMany(targetEntity="Release", mappedBy="application")
     */
    protected $releases;

    /**
     * @OneToOne(targetEntity="Release")
     * @JoinColumn(name="current_release_id", referencedColumnName="id")
     */
    protected $currentRelease;

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

}
