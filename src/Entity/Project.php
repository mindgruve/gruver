<?php

namespace Mindgruve\Gruver\Entity;

/**
 * @Entity(repositoryClass="Mindgruve\Gruver\Repository\ProjectRepository")
 * @Table(name="project",uniqueConstraints={@UniqueConstraint(name="project_name_constraint", columns={"name"})})
 */
class Project
{
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
}