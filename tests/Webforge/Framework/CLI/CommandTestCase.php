<?php

namespace Webforge\Framework\CLI;

use Webforge\Framework\Container;
use Webforge\Setup\ApplicationStorage;
use org\bovigo\vfs\vfsStream;
use Webforge\Common\System\Dir;
use Symfony\Component\Console\Tester\CommandTester;

class CommandTestCase extends \Webforge\Code\Test\Base {

  protected $container;

  public function setUp() {
    $this->container = new Container();

    $this->output = $this->getMockForAbstractClass('Webforge\Console\CommandOutput');
    $this->interactionHelper = $this->getMockBuilder('Webforge\Console\InteractionHelper')->disableOriginalConstructor()->getMock();
    
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
}
