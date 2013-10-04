<?php

namespace Webforge\Framework\CLI;

class CreateClassTest extends CommandTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\CLI\\CreateClass';
    parent::setUp();

    $this->container->setLocalPackage($this->package = $this->createVirtualPackage('ACMESuperBlog'));

    $this->cmd = new CreateClass($this->container);
  }

  public function testCLIDescriptionIsNotEmpty() {
    $this->assertNotEmpty($this->cmd->getDescription());
  }

  public function testItCreatesANewClassInTheNamespaceOfTheRightPackage() {
    $this->executeTest(array(
      'fqn'=>'ACME\SuperBlog\Entities\Post'
      //'parent'=>'ACME\SuperBlog\Entities\Entity'
    ));

    $this->assertFileExists((string) $this->package->getRootDirectory()->sub('lib/ACME\SuperBlog\Entities\Post.php'));
  }

  protected function executeTest(Array $args) {
    $args = array_replace(array(
      'fqn'=>NULL,
      'parent'=>NULL,
      'interface'=>array(),
      'implements'=>NULL
    ), $args);

    $this->expectInputValue('fqn', $args['fqn']);
    $this->expectInputValue('parent', $args['parent']);

    // single interface
    $this->expectInputValue('implements', $args['implements']);
    $this->expectInputValue('interface', $args['interface']);

    $this->expectInputFlag('overwrite', isset($args['overwrite']) ? $args['overwrite'] : FALSE);
    $this->expectInputValue('use-package', isset($args['use-package']) ? $args['use-package'] : FALSE);

    $this->execute();
  }

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

  protected function execute() {
    $this->initIO($this->cmd);
    $this->executeCLI($this->cmd);
  }
}
