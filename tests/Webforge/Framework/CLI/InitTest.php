<?php

namespace Webforge\Framework\CLI;

use Mockery as m;
use Webforge\Common\System\Util as SystemUtil;
use Webforge\Common\String as S;

class InitTest extends CommandTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\CLI\\Init';
    parent::setUp();

    $this->packageRoot = $this->getVirtualDirectory('acm-superblogg');

    $this->init = new Init($this->container);

    $this->extraConfiguration = array(
      'extra.branch-alias.dev-master'=>'1.0.x-dev'
    );
  }

  protected function executeTest(Array $initConfiguration, Array $additionalConfiguration) {
    // all the questions and confirmations
    $this->expectVendorAndSlugQuestions();
    $this->expectComposerQuestion(TRUE);

    // what is given to the composer init command?
    $this->expectComposerInit($initConfiguration);

    // what is given to the composer config setkey setvalue
    $this->expectAdditionalConfiguration($additionalConfiguration);

    $this->execute();
  }

  public function testInitAsksForTheVendorAndTheSlugofThePackageAndGuessesItFromTheRootDirectoryName() {
    $this->executeTest(array('name'=>'acme/superblog'), $this->extraConfiguration);
  }

  public function testReadsTheDescriptionFromTheGithubReadme() {
    $this->packageRoot->getFile('README.md')->writeContents(<<<'MARKDOWN'
acme-superblog
================

The coolest super blog on the planet
MARKDOWN
    );

    $this->executeTest(array('description'=>'The coolest super blog on the planet'), $this->extraConfiguration);
  }

  public function testWritesOtherValuesWithInit() {
    $this->executeTest(array('stability'=>'dev'), $this->extraConfiguration);
  }

  public function testArgumentsWillBeDefined() {
    // how to do that?
  }

  public function testCLIDescriptionIsNotEmpty() {
    $this->assertNotEmpty($this->init->getDescription());
  }


  /* custom expectations */
  public function expectAdditionalConfiguration(Array $configs) {
    $test = $this;
    foreach ($configs as $key =>$value) {
      $this->system
        ->shouldReceive('exec')
        ->ordered('execs')
        ->once()
        ->with(m::on(function($commandline) use ($test, $key, $value) {
          $test->assertContains('composer config', $commandline);

          $test->assertContains(
            sprintf('%s %s', $key, SystemUtil::escapeShellArg($value, $this->testOs)), 
            $commandline,
            'composer config should be called with '.$key.' set to '.$value
          );

          return TRUE;
        }))
        ->andReturn(0);
    }
  }

  protected function expectVendorAndSlugQuestions() {
    $this->expectQuestion()
      ->with('/vendor/i', 'acm')
      ->andReturn('acme');

    $this->expectQuestion()
      ->with('/package/i', 'superblogg')
      ->andReturn('superblog');
  }

  protected function expectComposerQuestion($answer) {
    return $this->expectConfirm()
      ->with('/composer/i', TRUE)
      ->andReturn($answer);
  }

  protected function expectComposerInit(array $params) {
    $test = $this;
    return $this->system
      ->shouldReceive('exec')
      ->ordered('execs')
      ->once()
      ->with(m::on(function ($commandline) use ($params, $test) {
        if (!S::startsWith($commandline, 'composer init')) {
          return FALSE;
        }

        foreach ($params as $param=>$value) {
          $test->assertContains(
            sprintf('--%s=%s', $param, SystemUtil::escapeShellArg($value, $test->testOs)),
            $commandline,
            'parameter '.$param.' is not correctly set for composer-init'
          );
        }

        return TRUE;
      }),
        m::any()
      )
     ->andReturn(0);
  }

  protected function execute() {
    // execute directly to inject the mocked interaction helper
    // if we use the command tester from symfony this is not possible todo
    $this->init->initIO($this->input, $this->output, $this->interactionHelper, $this->system);

    return $this->init->execute($this->packageRoot);
  }
}
