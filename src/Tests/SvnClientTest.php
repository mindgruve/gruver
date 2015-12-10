<?php

namespace Mindgruve\Gruver\Tests;

use Mindgruve\Gruver\Vcs\SvnClient;
use Mindgruve\Gruver\Vcs\VcsClientInterface;

class SvnClientTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $path2;

    /**
     * @var SvnClient
     */
    protected $sut;

    /**
     * @var SvnClient
     */
    protected $sut2;


    public function setup()
    {
        $this->path = __DIR__ . '/Temp/Svn-Working-Copy';
        svn_revert($this->path, true);
        svn_update($this->path);
        $this->sut = new SvnClient($this->path);

        $this->path2 = __DIR__ . '/Temp/Svn-Working-Copy-2';
        svn_revert($this->path2, true);
        svn_update($this->path2);
        $this->sut2 = new SvnClient($this->path2);
    }

    public function tearDown()
    {
        if (file_exists($this->path . '/unversioned-file.txt')) {
            unlink($this->path . '/unversioned-file.txt');
        }

        if (file_exists($this->path . '/modified-file.txt')) {
            unlink($this->path . '/modified-file.txt');
            svn_revert($this->path . '/modified-file.txt');
        }
    }

    protected function modifyFile($repoPath, $filename, $commit = false)
    {
        if (!file_exists($repoPath . '/' . $filename)) {
            $fp = fopen($repoPath . '/' . $filename, 'w');
            $string = 'test - ' . uniqid();
            fwrite($fp, $string);

            svn_add($repoPath . '/' . $filename);
            svn_commit($string, array($repoPath . '/' . $filename));
        }

        $fp = fopen($repoPath . '/' . $filename, 'w');
        $string = 'test - ' . uniqid();
        fwrite($fp, $string);

        if ($commit) {
            svn_commit('test commit', array($repoPath . '/' . $filename));
            svn_update($repoPath);
        }
    }

    public function testInterface()
    {
        $this->assertTrue($this->sut instanceof VcsClientInterface);
    }

    public function testDirtyWorkingCopy()
    {
        $this->assertTrue($this->sut->isCleanWorkingCopy());

        $fp = fopen($this->path . '/unversioned-file.txt', 'w');
        fclose($fp);
        $this->assertFalse($this->sut->isCleanWorkingCopy());
        unlink($this->path . '/unversioned-file.txt');
    }

    public function testModifiedFile()
    {
        $this->assertTrue($this->sut->isCleanWorkingCopy());
        $this->modifyFile($this->path, 'modified-file.txt');
        $this->assertFalse($this->sut->isCleanWorkingCopy());
    }

    public function testDeletedFile()
    {
        $this->assertTrue($this->sut->isCleanWorkingCopy());

        if (!file_exists($this->path . '/deleted-file.txt')) {
            $fp = fopen($this->path . '/deleted-file.txt', 'w');
            $string = 'test - ' . uniqid();
            fwrite($fp, $string);

            svn_add($this->path . '/deleted-file.txt');
            svn_commit($string, array($this->path . '/deleted-file.txt'));
            svn_update($this->path);
        }

        unlink($this->path . '/deleted-file.txt');
        $this->assertFalse($this->sut->isCleanWorkingCopy());
    }

    public function testGetVersion()
    {
        $this->assertTrue($this->sut->isCleanWorkingCopy());
        $versionNumber = $this->sut->getVersion();
        $this->modifyFile($this->path, 'modified-file.txt', true);
        $this->assertEquals($versionNumber + 1, $this->sut->getVersion());
    }

    public function testUpdateToHead()
    {
        $this->assertTrue($this->sut->isCleanWorkingCopy());
        $this->assertTrue($this->sut2->isCleanWorkingCopy());

        $this->assertEquals($this->sut->getVersion(), $this->sut2->getVersion());
        $this->modifyFile($this->path, 'modified-file.txt', true);
        $this->assertNotEquals($this->sut->getVersion(), $this->sut2->getVersion());
        $this->sut2->updateToHead();
        $this->assertEquals($this->sut->getVersion(), $this->sut2->getVersion());

        $this->assertTrue($this->sut->isCleanWorkingCopy());
        $this->assertTrue($this->sut2->isCleanWorkingCopy());
    }
}