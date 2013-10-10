<?php

namespace Webforge\Framework\CLI;

use Webforge\Console\CommandInput;
use Webforge\Console\CommandOutput;
use Webforge\Console\CommandInteraction;

use Webforge\Code\Generator\ClassCreater;
use Webforge\Code\Generator\GFunctionBody;
use Webforge\Code\Generator\GClass;

class CreateTest extends ContainerCommand {

  const OVERWRITE = ClassCreater::OVERWRITE;
  
  public function defineArguments(Array $api) {
    extract($api);

    return array(
      $arg('fqn', 'The full qualified name of the class under test'),
      $flag('overwrite', NULL, 'If set the test will be created, regardless if the file already exists')
    );
  }

  public function getDescription() {
    return 'Creates a new empty Unit-Test stub for an existing class';
  }
  
  public function executeCLI(CommandInput $input, CommandOutput $output, CommandInteraction $interact) {
    return $this->execute(
      $input->getValue('fqn'),
      $input->getFlag('overwrite') ? self::OVERWRITE : FALSE
    );
  }

  public function execute($fqn, $overwrite = FALSE) {
    $creater = new ClassCreater(
      $this->container->getClassFileMapper(),
      $this->container->getClassWriter(),
      $this->container->getClassElevator()
    );
    
    $gClass = new GClass($fqn);
    $testClass = new GClass($gClass->getFQN().'Test');

    $testClass->setParent(
      new GClass('Webforge\Code\Test\Base')
    );
    
    $testClass->createMethod(
      'setUp',
      array(),
      GFunctionBody::create(
        array(
          '$this->chainClass = __NAMESPACE__.\'\\'.$gClass->getName().'\';',
          'parent::setUp();'
        )
      )
    );
    
    $file = $creater->create($testClass, $overwrite);
    
    $this->output->ok('wrote Test '.$gClass.' to file: '.$file);
    return 0;
  }
}
