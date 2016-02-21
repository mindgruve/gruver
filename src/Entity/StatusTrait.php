<?php

namespace Mindgruve\Gruver\Entity;

trait StatusTrait
{
    /** @Column(length=10) */
    protected $status = StatusInterface::STATUS_DISABLED;

    /**
     * @param $status
     * @return $this
     * @throws \Exception
     */
    public function setStatus($status)
    {
        if (!in_array($status, $this->getStatusOptions())) {
            throw new \Exception('Invalid status - ' . $status);
        }
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public function getStatusOptions()
    {
        return array(
            StatusInterface::STATUS_ENABLED,
            StatusInterface::STATUS_DISABLED,
        );
    }

}