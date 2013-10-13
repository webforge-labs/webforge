<?php

namespace Webforge\Framework\CLI;

class ReleaseTest extends CommandTestCase {
  
  public function setUp() {
    $this->chainClass = __NAMESPACE__ . '\\Release';
    parent::setUp();

    $this->package = $this->injectVirtualPackage('ACMESuperBlog');

    $this->cmd = new Release($this->container);
  }

  public function testCLIDescriptionIsNotEmpty() {
    $this->assertNotEmpty($this->cmd->getDescription());
  }

  public function testReleaseInstallsRMTWhenExecutedAndRMTIsNotInstalled() {
    $this->expectAskForRMTInstall(TRUE);
    $this->expectComposerInstall(0);

    $this->execute();
  }

  public function testIfNotInstalledJustExits() {
    $this->expectAskForRMTInstall(FALSE);

    $this->assertSame(1, $this->execute());
  }

  public function testIfComposerFailsJustExits() {
    $this->expectAskForRMTInstall(TRUE);
    $this->expectComposerInstall(255);

    $this->assertSame(1, $this->execute());
  }

  protected function expectAskForRMTInstall($answer) {
    $this->expectConfirm()
    ->with('/install RMT now/i', $default = TRUE)
    ->andReturn($answer);
  }

  protected function expectComposerInstall($exitCode = 0) {
    $package = $this->package;
    $rmtFake = $this->getTestDirectory()->sub('packages/RMT/');

    return $this->system->shouldReceive('passthru')
      ->with('/composer require --dev liip\/rmt/i')
      ->once()
      ->andReturnUsing(function () use ($exitCode, $package, $rmtFake) {
        if ($exitCode === 0) {
          $target = $package->getDirectory('vendor')->sub('liip/rmt')->create();
          $rmtFake->copy($target);
        }

        return $exitCode;
      });
  }

  protected function execute() {
    $this->initIO($this->cmd);

    return $this->executeCLI($this->cmd);
  }
}
