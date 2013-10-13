<?php

namespace Webforge\Framework\CLI;

use Mockery as m;

class ReleaseTest extends CommandTestCase {
  
  public function setUp() {
    $this->chainClass = __NAMESPACE__ . '\\Release';
    parent::setUp();

    $this->package = $this->injectVirtualPackage('ACMESuperBlog');

    $this->container->setReleaseManager(
      $this->rmt = m::mock('Liip\RMT\Application')
    );
    $this->rmt->shouldReceive('run')->byDefault()->andReturn(0);

    $this->expectInputValue('do', 'release')->byDefault();

    $this->cmd = new Release($this->container);
  }

  public function testCLIDescriptionIsNotEmpty() {
    $this->assertNotEmpty($this->cmd->getDescription());
  }

  public function testReleaseInstallsRMTWhenExecutedAndRMTIsNotInstalled() {
    $this->expectAskForRMTInstall(TRUE);
    $this->expectComposerInstall(0);

    $this->execute();

    $this->assertFileExists((string) $rmt = $this->package->getRootDirectory()->getFile('rmt.json'));
    $config = json_decode($rmt->getContents());
    $this->assertObjectHasAttribute('version-generator', $config, 'version-generator should be defined in config');
    $this->assertObjectHasAttribute('version-persister', $config, 'version-persister should be defined in config');
  }

  public function testReleaseCurrentDoesOnlyDisplay() {
    $this->fakeInstall();

    $this->expectInputValue('do', 'current');

    $this->rmt
      ->shouldReceive('run')
      ->once()
      ->with(m::on(function ($input) {
        return $input->getParameterOption('command') === 'current';
      }))
      ->andReturn(0);

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
    $test = $this;

    return $this->system->shouldReceive('passthru')
      ->with('/composer require --dev liip\/rmt/i')
      ->once()
      ->andReturnUsing(function () use ($exitCode, $test) {
        if ($exitCode === 0) {
          $test->fakeInstall();
        }

        return $exitCode;
      });
  }

  public function fakeInstall() {
    $rmtFake = $this->getTestDirectory()->sub('packages/RMT/');
    $target = $this->package->getDirectory('vendor')->sub('liip/rmt')->create();
    $rmtFake->copy($target);
  }

  protected function execute() {
    $this->initIO($this->cmd);

    return $this->executeCLI($this->cmd);
  }
}
