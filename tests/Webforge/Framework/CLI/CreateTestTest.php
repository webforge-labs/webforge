<?php

namespace Webforge\Framework\CLI;

class CreateTestTest extends CommandTestCase {
  
  public function setUp() {
    $this->chainClass = __NAMESPACE__ . '\\CreateTest';
    parent::setUp();

    $this->package = $this->injectVirtualPackage('ACMESuperBlog');
    $this->cmd = new CreateTest($this->container);
  }

  public function testCLIDescriptionIsNotEmpty() {
    $this->assertNotEmpty($this->cmd->getDescription());
  }

  public function testThatCreateTestCreatesATestForAFile_WithAPackageThatHastestsAsAutoLoadingDirectory() {
    $this->assertSame(0, $this->execute(
      'ACME\SuperBlog\Main',
      $overwrite = FALSE
    ));

    $this->assertFileExists($this->package->getDirectory('tests')->getFile('ACME/SuperBlog/MainTest.php'));
  }

  /*
  protected function expectCustomConfirmation($answer) {
    $this->expectConfirm()
    ->with('/continue/i', $default = TRUE)
    ->andReturn($answer);
  }

  protected function expectCustomQuestion1() {
    $this->expectQuestion()
    ->with('/fqn of namespace/i', $default = 'Acme\Superblog')
    ->andReturn('ACME\SuperBlog');
  }
  */

  protected function execute($fqn, $overwrite) {
    $this->initIO($this->cmd);

    return $this->cmd->execute($fqn, $overwrite);
  }
}
