<?php

namespace Webforge\Framework\CLI;

use mockery as m;

class InstallPartTest extends CommandTestCase {
  
  public function setUp() {
    parent::setUp();

    $this->container->setLocalPackage($this->package = $this->createVirtualPackage('ACMESuperBlog'));
    $this->partsInstaller = m::mock('Webforge\Setup\Installer\PartsInstaller');  
    $this->container->setPartsInstaller($this->partsInstaller);

    $this->cmd = new InstallPart($this->container);
  }

  public function testCLIDescriptionIsNotEmpty() {
    $this->assertNotEmpty($this->cmd->getDescription());
  }

  public function testInstallPartAskForPartIfNonProvided() {
    $testPart = m::mock('Webforge\Setup\Installer\Part')->shouldIgnoreMissing();

    $this->partsInstaller
      ->shouldReceive('getParts')
      ->once()
      ->andReturn(array($testPart));

    $this->partsInstaller
      ->shouldReceive('getPart')
      ->once()
      ->with('TestPart')
      ->andReturn($testPart);

    $this->partsInstaller
      ->shouldReceive('install')
      ->with($testPart, m::any())
      ->andReturn('macro - not used');

    $this->expectPartQuestion('TestPart');

    $this->execute($part = NULL);
  }

  public function testInstallPartWithArgument() {
    $testPart = m::mock('Webforge\Setup\Installer\Part')->shouldIgnoreMissing();

    $this->partsInstaller
      ->shouldReceive('getPart')
      ->once()
      ->with('TestPart')
      ->andReturn($testPart);

    $this->partsInstaller
      ->shouldReceive('install')
      ->with($testPart, m::any())
      ->andReturn('macro - not used');

    $this->execute($part = 'TestPart');
  }

  protected function expectPartQuestion($answer) {
    $this->expectSimpleQuestion()
      ->with('/which part/i')
      ->andReturn($answer);
  }

  protected function execute($part) {
    $this->input->shouldReceive('getValue')
      ->with('part')
      ->once()
      ->andReturn($part);

    $this->input
      ->shouldReceive('getDirectory')
      ->with('location')
      ->once()
      ->andReturn($this->package->getRootDirectory());

    $this->initIO($this->cmd);      

    return $this->executeCLI($this->cmd);
  }
}
