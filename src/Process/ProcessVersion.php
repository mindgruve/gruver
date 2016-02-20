<?php

namespace Mindgruve\Gruver\Process;

class ProcessVersion
{
    /**
     * @var int
     */
    protected $major;

    /**
     * @var int
     */
    protected $minor;

    /**
     * @var int
     */
    protected $patch;

    /**
     * @param $major
     * @param $minor
     * @param $patch
     */
    public function __construct($major, $minor, $patch)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
    }

    /**
     * @return int
     */
    public function getMajor()
    {
        return $this->major;
    }

    /**
     * @return int
     */
    public function getMinor()
    {
        return $this->minor;
    }

    /**
     * @return int
     */
    public function getPatch()
    {
        return $this->patch;
    }

    /**
     * @param $major
     * @param int $minor
     * @param int $patch
     * @return bool
     */
    public function compareVersion($major, $minor = 0, $patch = 0)
    {

        if ($major > $this->getMajor()) {
            return true;
        }

        if ($major < $this->getMajor()) {
            return false;
        }

        if ($minor > $this->getMinor()) {
            return true;
        }

        if ($minor < $this->getMinor()) {
            return false;
        }

        if ($patch > $this->getPatch()) {
            return true;
        }

        return false;
    }
}