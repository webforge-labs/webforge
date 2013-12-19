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
    $this->expectRMTConfigNotDefined();
    $this->expectAskForRMTInstall(TRUE);

    $this->execute();

    $this->assertFileExists((string) $rmt = $this->package->getRootDirectory()->getFile('rmt.json'));
    $config = json_decode($rmt->getContents());
    $this->assertObjectHasAttribute('version-generator', $config, 'version-generator should be defined in config');
    $this->assertObjectHasAttribute('version-persister', $config, 'version-persister should be defined in config');
  }

  protected function expectRMTConfigNotDefined() {
    $this->rmt->shouldReceive('find')->with('release')->andThrow(new \InvalidArgumentException('release is not defined'));
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

  public function testIfNotInstalledAndShouldNotBeInstalledItJustExits() {
    $this->expectRMTConfigNotDefined();
    $this->expectAskForRMTInstall(FALSE);

    $this->assertSame(1, $this->execute());
  }

  protected function expectAskForRMTInstall($answer) {
    $this->expectConfirm()
    ->with('/install RMT now/i', $default = TRUE)
    ->andReturn($answer);
  }

  protected function expectComposerInstall($exitCode = 0) {
    // not used because we do not install composer locally
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

    $this->rmt->shouldReceive('find')->with('release')->andReturn(new \stdClass);
  }

  protected function execute() {
    $this->initIO($this->cmd);

    return $this->executeCLI($this->cmd);
  }
}
