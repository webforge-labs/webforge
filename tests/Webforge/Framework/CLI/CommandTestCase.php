<?php

namespace Webforge\Framework\CLI;

use Webforge\Framework\Container;
use Webforge\Setup\ApplicationStorage;
use org\bovigo\vfs\vfsStream;
use Webforge\Common\System\Dir;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class CommandTestCase extends \Webforge\Code\Test\Base {

  protected $container;

  public $testOs;

  public function setUp() {
    $this->container = new Container();

    $this->output = $this->getMockForAbstractClass('Webforge\Console\CommandOutput');
    $this->input = $this->getMockForAbstractClass('Webforge\Console\CommandInput');
    $this->interactionHelper = m::mock('Webforge\Console\InteractionHelper');

    $this->system = m::mock('Webforge\Common\System\ExecutionSystem');

    $this->testos = \Webforge\Common\System\ExecutionSystem::UNIX;
    $this->system->shouldReceive('getOperatingSystem')->andReturn($this->testOs);
    
    $this->application = new Application($this->getPackageDir('/'), $this->container);
    parent::setUp();
  }

  protected function mockContainerPackageRegistry($methods = array()) {
    $mock = $this->getMock('Webforge\Framework\Package\Registry', $methods, array($this->container->getComposerPackageReader()));

    $this->container->setPackageRegistry($mock);

    return $mock;
  }

  protected function mockApplicationStorage($methods = array()) {
    $appDir = vfsStream::setup('appstorage');

    $mock = new ApplicationStorage('webforge-test', Dir::factoryTS(vfsStream::url('appstorage')));

    $this->container->setApplicationStorage($mock);

    return $mock;
  }

  protected function runCommand($name, Array $args = array()) {
    $command = $this->application->find($name);

    $tester = new CommandTester($command);
    $tester->execute(array_merge(
      array('command'=>$name), $args
    ));

    return $tester->getDisplay();
  }

  /**
   */
  public function expectInteraction($type) {
    return $this->interactionHelper
      ->shouldReceive($type)
      ->once()
      ->ordered('interact');
  }

  public function expectQuestion() {
    return $this->expectInteraction('askDefault');
  }

  public function expectSimpleQuestion() {
    return $this->expectInteraction('ask');
  }

  public function expectConfirm() {
    return $this->expectInteraction('confirm');
  }

  public function expectQuestionWithDefault($constraint, $default, $answer, $type = 'all') {
    return $this->interactionHelper
     ->expects($this->once())
     ->method($type === 'all' 
       ? $this->logicalOr($this->equalTo('ask'), $this->equalTo('askAndValidate'), $this->equalTo('askDefault'), $this->equalTo('confirm')) 
       : $type
     )
     ->with($this->expandQuestionConstraint($constraint))
     ->will($this->returnValue($answer));
  }

  protected function getVirtualDirectory($name) {
    vfsStream::setup($name);

    return new Dir(vfsStream::url($name).'/');
  }
}
