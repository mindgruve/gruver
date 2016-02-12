<?php

namespace Mindgruve\Gruver\Entity;

/** @Entity */
class Release
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(length=140) */
    protected $tag;
}