<?php

namespace Webforge\Framework\CLI;

class InfoTest extends CommandTestCase {

  public function setUp() {
    $this->chainClass = __NAMESPACE__.'\\Info';
    parent::setUp();

    $this->output = new \Webforge\Console\StringCommandOutput();
    $this->cmd = new Info($this->container);
  }

  public function testCLIDescriptionIsNotEmpty() {
    $this->assertNotEmpty($this->cmd->getDescription());
  }

  public function testThatCommandRuns() {
    $this->execute();

    $this->assertNotEmpty($infos = $this->output->toString());
    $this->assertContains('webforge is loaded from:', $infos);
  }

  protected function execute() {
    $this->initIO($this->cmd);

    return $this->executeCLI($this->cmd);
  }
}
