<?php

namespace Webforge\Console;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Webforge\Common\System\Dir;

class SymfonyCommandInputAdapterTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Console\\SymfonyCommandInputAdapter';
    parent::setUp();

    $this->inputDefinition = new InputDefinition(
      array(
        new InputArgument('location'),
        new InputArgument('type')
      )
    );

    $this->input = $this->createInput(array(
      'location'=>__DIR__,
      'type'=>'npm'
    ));
  }

  protected function createInput(array $arguments) {
    return new SymfonyCommandInputAdapter(
      new ArrayInput(
        $arguments,
        $this->inputDefinition
      )
    );
  }

  public function testHasAGetDirectoryMethod() {
    $this->assertInstanceOf('Webforge\Common\System\Dir', $location = $this->input->getDirectory('location'));

    $this->assertEquals(
      __DIR__.DIRECTORY_SEPARATOR,
      (string) $location
    );
  }

  public function testGetDirectoryThrowsExceptionIfDirDoesNotExistAndFlagIsset() {
    $this->setExpectedException('InvalidArgumentException');

    $input = $this->createInput(array('location'=>__DIR__.DIRECTORY_SEPARATOR.'doesnotexists'.DIRECTORY_SEPARATOR));

    $input->getDirectory('location', SymfonyCommandInputAdapter::MUST_EXIST);
  }

  public function testHasGetEnumValidatesValuesFromArrayPositive() {
    $input = $this->createInput(array(
      'type'=>'composer'
    ));

    $this->assertEquals(
      'composer',
      $input->getEnum('type', array('composer', 'npm'), 'composer')
    );

    $input = $this->createInput(array(
      'type'=>'npm'
    ));

    $this->assertEquals(
      'npm',
      $input->getEnum('type', array('composer', 'npm'), 'composer')
    );
  }

  public function testGetEnumReturnsDefaultIfEmpty() {
    $this->input = $this->createInput(array(
      'type'=>NULL
    )); 

    $this->assertEquals(
      'composer',
      $this->input->getEnum('type', array('composer', 'npm'), 'composer')
    );
  }

  public function testGetEnumThrowsExceptionIfWrongValue() {
    $this->input = $this->createInput(array(
      'type'=>'undefined'
    )); 

    $this->setExpectedException('InvalidArgumentException');

    $this->input->getEnum('type', array('composer', 'npm'), 'composer');
  }
}
