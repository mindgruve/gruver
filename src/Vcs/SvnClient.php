<?php

namespace Mindgruve\Gruver\Vcs;

class SvnClient implements VcsClientInterface
{

    protected $repositoryPath;

    public function __construct($repositoryPath)
    {
        $this->repositoryPath = realpath($repositoryPath);
    }

    /**
     * @return bool
     */
    public function updateToHead()
    {
        return $this->update();
    }

    /**
     * @param null $revision
     * @return bool|int
     * @throws \Exception
     */
    public function update($revision = null)
    {
        if (is_null($revision)) {
            return svn_update($this->repositoryPath, SVN_REVISION_HEAD);
        }

        if (!is_int($revision)) {
            throw new \Exception('Revision number should be an integer');
        }

        if (!$this->isCleanWorkingCopy()) {
            return false;
        }

        return svn_update($this->repositoryPath, $revision);
    }

    /**
     * @return boolean
     */
    public function isCleanWorkingCopy()
    {
        $status = svn_status($this->repositoryPath);
        if (is_array($status) && count($status) == 0) {
            return true;
        }

        return false;
    }

    public function revertToCleanWorkingCopy()
    {
        svn_revert($this->repositoryPath, true);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getVersion()
    {
        $statusDataArray = svn_status($this->repositoryPath . '/', SVN_NON_RECURSIVE | SVN_ALL);

        if (is_array($statusDataArray)) {
            foreach ($statusDataArray as $statusDataItem) {
                if ($statusDataItem['path'] == $this->repositoryPath) {
                    return $statusDataItem['revision'];
                }
            }
        }

        throw new \Exception('Repository version number not found');
    }

}