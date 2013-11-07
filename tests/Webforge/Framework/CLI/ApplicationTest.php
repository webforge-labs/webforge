<?php

namespace Webforge\Framework\CLI;

use Symfony\Component\Console\Tester\CommandTester;

class ApplicationTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\CLI\\Application';
    parent::setUp();

    $this->application = new Application($this->getPackageDir('/'));
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
    $test('git:sync-packages');
    $test('info');
    
    $test('install:part');
    $test('install:list-parts');

    $test('release');

    return $tests;
  }

  public function testListPartsCommand() {
    $out = $this->runCommand('install:list-parts');

    $this->assertContains("parts available:", $out);

    // samples
    $this->assertContains('CreateCLI', $out);
    $this->assertContains('Bootstrap', $out);
    $this->assertContains('InitConfiguration', $out);
    $this->assertContains('TestSuite', $out);
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
