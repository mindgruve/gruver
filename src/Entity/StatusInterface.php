<?php

namespace Mindgruve\Gruver\Entity;

interface StatusInterface
{
    const STATUS_ENABLED = 'enabled';
    const STATUS_DISABLED = 'disabled';

    /**
     * @param $status
     * @return $this
     * @throws \Exception
     */
    public function setStatus($status);

    /**
     * @return mixed
     */
    public function getStatus();

    /**
     * @return array
     */
    public function getStatusOptions();
}