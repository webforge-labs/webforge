<?php

namespace Webforge\Framework\CLI;

use Mockery as m;
use Webforge\Common\System\Util as SystemUtil;
use Webforge\Common\String as S;
use Webforge\Common\JS\JSONConverter;

class InitTest extends CommandTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\CLI\\Init';
    parent::setUp();

    $this->packageRoot = $this->getVirtualDirectory('acm-superblogg');

    $this->init = new Init($this->container);

    $this->extraConfiguration = array(
      'extra.branch-alias.dev-master'=>'1.0.x-dev'
    );

    $this->autoloadAnswers = FALSE;
  }

  protected function executeTest(Array $initConfiguration, Array $additionalConfiguration) {
    // all the questions and confirmations
    $this->expectVendorAndSlugQuestions();
    $this->expectComposerQuestion(TRUE);

    // what is given to the composer init command?
    $this->expectComposerInit($initConfiguration);

    $this->expectAutoloadQuestions($this->autoloadAnswers);

    $this->expectRegisterPackage(TRUE);

    $this->execute();

    // additional written info into the composer.json manually made
    $this->assertAdditionalConfiguration($additionalConfiguration);
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

  public function testAsksForAutoloadAndWritesIt() {
    $this->autoloadAnswers = TRUE;
    
    $this->extraConfiguration['autoload.psr-0.ACME\\SuperBlog'] = array("src/", "tests/");

    $this->executeTest(array('stability'=>'dev'), $this->extraConfiguration);
  }

  public function testArgumentsWillBeDefined() {
    // how to do that?
  }

  public function testCLIDescriptionIsNotEmpty() {
    $this->assertNotEmpty($this->init->getDescription());
  }


  /* custom expectations */
  public function assertAdditionalConfiguration(Array $configs) {
    $composerConfig = JSONConverter::create()->parseFile($this->packageRoot->getFile('composer.json'));

    foreach ($configs as $pathString => $expectedValue) {
      $path = explode('.', $pathString);

      $value = $composerConfig;
      foreach ($path as $key) {
        $this->assertObjectHasAttribute($key, $value, 'cannot retreive path: '.$pathString.' keys on this level: '.implode(', ',array_keys((array) $value)));
        $value = $value->$key;
      }

      $this->assertEquals($expectedValue, $value, 'Value from path: '.$pathString);
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

  protected function expectAutoloadQuestions($questions) {
    if ($questions === FALSE) {
      $this->expectConfirm()
        ->with('/autoload/i', TRUE)
        ->andReturn(FALSE);
    } else {
      $this->expectConfirm()
        ->with('/autoload/i', TRUE)
        ->andReturn(TRUE);

      $this->expectQuestion()
        ->with('/namespace/i', 'Acme\Superblog')
        ->andReturn('ACME\SuperBlog');

      $this->expectQuestion()
        ->with('/library[-\s]path/i', 'lib/')
        ->andReturn('src/');

      $this->expectQuestion()
        ->with('/tests[-\s]path/i', 'tests/')
        ->andReturn('tests/');
    }
  }

  protected function expectComposerInit(array $params) {
    $test = $this;
    $root = $this->packageRoot;
    return $this->system
      ->shouldReceive('passthru')
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
      }))
     ->andReturnUsing(function () use ($root) {
       // we fake the behaviour from composer init here

       $json = <<<'JSON'
{
    "name": "acme/superblog",
    "license": "MIT",
    "authors": [
        {
            "name": "Philipp Scheit",
            "email": "p.scheit@ps-webforge.com"
        }
    ],
    "minimum-stability": "dev",
    "require": {

    }
}
JSON;

       $root->getFile('composer.json')->writeContents($json);

       return 0;
     });
  }

  protected function expectRegisterPackage($answer) {
    $this->expectConfirm()
      ->with('/register this package/i', TRUE)
      ->andReturn($answer);

    if ($answer) {
      $this->system
        ->shouldReceive('passthru')
        ->ordered('execs')
        ->once()
        ->with(m::on(function ($commandline) {
          if (!S::startsWith($commandline, 'webforge register-package')) {
            return FALSE;
          }

          return TRUE;
        }));
    }
  }

  protected function execute() {
    $this->initIO($this->init);

    return $this->init->execute($this->packageRoot);
  }
}
