<?php

namespace Webforge\Console;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;

class ApplicationTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Console\\Application';
    parent::setUp();

    $this->application = Application::fromDirectory($this->getPackageDir('/'));
  }

  /**
   * @dataProvider provideCommandNames
   */
  public function testCommandNamesAreDefined($commandName) {
    $this->assertTrue($this->application->has($commandName), 'application has command('.$commandName.')');
  }
  
  public static function provideCommandNames() {
    $tests = array();
  
    $test = function() use (&$tests) {
      $tests[] = func_get_args();
    };
  
    $test('create-class');
    $test('create-test');
    $test('register-package');

    $test('install:part');
    $test('install:list-parts');

    return $tests;
  }

  public function testListPartsCommand() {
    $out = $this->runCommand('install:list-parts');

    $this->assertContains("parts avaible:", $out);

    // samples
    $this->assertContains('CreateCLI', $out);
    $this->assertContains('CreateBootstrap', $out);
    $this->assertContains('InitConfiguration', $out);
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
