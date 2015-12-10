<?php

namespace Mindgruve\Gruver\Vcs;

interface VcsClientInterface
{
    /**
     * @param string $version
     * @return mixed
     */
    public function update($version = NULL);

    /**
     * @return mixed
     */
    public function updateToHead();

    /**
     * @return boolean
     */
    public function isCleanWorkingCopy();

    /**
     * @return mixed
     */
    public function revertToCleanWorkingCopy();

    /**
     * @return mixed
     */
    public function getVersion();

}