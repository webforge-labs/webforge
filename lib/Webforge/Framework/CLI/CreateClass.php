<?php

namespace Webforge\Framework\CLI;

use Webforge\Console\CommandOutput;
use Webforge\Console\CommandInput;
use Webforge\Console\CommandInteraction;

use Webforge\Code\Generator\CreateClassCommand;

class CreateClass extends ContainerCommand {

  public function getDescription() {
    return 'Creates a new empty Class stub';
  }

  public function defineArguments(Array $api) {
    extract($api);

    return array(
      $arg('fqn', 'The full qualified name of the class'),
      $arg('parent', 'The full qualified name of the parent class', FALSE),
      $arg('interface', 'The full qualified names of one or more interfaces', FALSE, $multiple = TRUE),
      $opt('implements', '', 'The full qualified name of one interface', TRUE),
      $flag('overwrite', NULL, 'If set the class will be created, regardless if the file already exists'),
      $opt('use-package', NULL, $withValue = TRUE, 'Specify a package to use. When none is given the FQN is used to determine the package')
    );
  }

  public function executeCLI(CommandInput $input, CommandOutput $output, CommandInteraction $interact) {
    $cmd = CreateClassCommand::fromContainer($this->container)
      ->fqn($fqn = $input->getValue('fqn'));
      
    if ($parent = $input->getValue('parent')) {
      $cmd->parent($parent);
    }

    if ($interface = $input->getValue('implements')) {
      $cmd->addInterface($interface);
    }
    
    foreach ($input->getValue('interface') as $interface) {
      $cmd->addInterface($interface);
    }

    if ($usePackage = $input->getValue('use-package')) {
      $cmd->setFileFromPackage(
        $container->getPackageRegistry()->findByIdentifier($usePackage)
      );
    }
    
    $file = $cmd->write($input->getFlag('overwrite'))->getFile();
    
    $output->ok('wrote Class '.$cmd->getGClass().' to file: '.$file);
    return 0;
  }
}
