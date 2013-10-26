<?php

namespace Webforge\Framework\CLI;

use mockery as m;

class GitSyncPackagesTest extends CommandTestCase {

  public function setUp() {
    $this->chainClass = __NAMESPACE__.'\\GitSyncPackagesTest';
    $this->useNewRegistry = FALSE; // use the one from packagesTestCaseWithMoreTestcases
    parent::setUp();

    $this->cmd = new GitSyncPackages($this->container);
  }

  public function testCLIDescriptionIsNotEmpty() {
    $this->assertNotEmpty($this->cmd->getDescription());
  }

  public function testThatCommandPullsFastForwardForAllRegisteredPackagesWithWebforge() {
    $test = $this;
    $this->assertGreaterThan(3, count($packages = $this->container->getPackageRegistry()->getPackages()));

    foreach ($packages as $package) {
      $this->system->shouldReceive('setWorkingDirectory')
        ->once()
        ->with(m::on(function($dir) use ($package) {
          return (string) $dir === (string) $package->getRootDirectory();
        }));

      $this->system->shouldReceive('passthru')
        ->once()
        ->with(m::on(function($cmdLine) use ($test) {
          $test->assertContains('git pull', $cmdLine);
          $test->assertContains('ff-only',$cmdLine);

          return TRUE;
        }));
    }

    $this->execute();
  }


  protected function execute() {
    $this->initIO($this->cmd);

    return $this->executeCLI($this->cmd);
  }
}  
