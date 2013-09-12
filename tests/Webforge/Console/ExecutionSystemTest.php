<?php

namespace Webforge\Console;

use Webforge\Common\System\Util as SystemUtil;

/**
 * 
 * most of the stuff is not needed to be tested because we relie on symfony process to handle the details
 * 
 * but we'll do some simple acceptance tests anyway
 */
class ExecutionSystemTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Console\\ExecutionSystem';
    parent::setUp();

    $this->system = new ExecutionSystem();

    if (SystemUtil::isWindows()) {
      $this->ls = 'dir';
    } else {
      $this->ls = 'ls -la';
    }
  }

  public function testInterfaceImplementation() {
    $this->assertInstanceOf('Webforge\Common\System\ExecutionSystem', $this->system);
  }

  public function testExecReturnsTheCorrectExitCode() {
    $exitCode = $this->system->exec($this->ls);

    $this->assertSame(0, $exitCode);
  }

  public function testExecReturnsTheCorrectExitCodeWithOptions() {
    $exitCode = $this->system->exec($this->ls, array());

    $this->assertSame(0, $exitCode);
  }

  public function testExecPassesTheRunCallbackToProcess() {
    $output = '';

    $exitCode = $this->system->exec($this->ls, function($type, $out) use(&$output) {
      $output .= $out;
    });

    $this->assertNotEmpty($output);
  }

  public function testExecPassesTheRunCallbackToProcessWithOptions() {
    $output = '';

    $exitCode = $this->system->exec($this->ls, array(), function($type, $out) use(&$output) {
      $output .= $out;
    });

    $this->assertNotEmpty($output);
  }

  public function testProcessWillReturnASymfonyProcess() {
    $this->assertInstanceOf('Symfony\Component\Process\Process', $this->system->process($this->ls));
  }
}
