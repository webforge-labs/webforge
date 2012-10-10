<?php

use Psc\System\File;
use Webforge\Code\Generator\ClassCreater;
use Webforge\Code\Generator\ClassWriter;
use Webforge\Code\GlobalClassFileMapper;
use Webforge\Code\Generator\GClass;

/**
 *
 * $createCommand = function ($name, array|closure $configure, closure $execute, $help = NULL)
 * 
 * $arg = function ($name, $description = NULL, $required = TRUE, $multiple = FALSE) // default: required
 * $opt = function($name, $short = NULL, $withValue = TRUE, $description = NULL) // default: mit value required
 * $flag = function($name, $short = NULL, $description) // ohne value
 */

$createCommand('create-class',
  array(
    $arg('fqn', 'The full qualified name of the class'),
    $arg('parent', 'The full qualified name of the parent class'),
    $arg('interface', 'The full qualified names of one or more interfaces', FALSE, $multiple = TRUE),
    $flag('overwrite', NULL, 'If set the class will be created, regardless if the file already exists')
  ),
  function ($input, $output, $command) {
    $creater = new ClassCreater(new GlobalClassFileMapper(),
                                new ClassWriter()
                               );
    
    $gClass = new GClass($input->getArgument('fqn'));
    
    if (($parent = $input->getArgument('parent'))) {
      $gClass->setParent($parent = new GClass($parent));
    }
    
    foreach ($input->getArgument('interface') as $interface) {
      $gClass->addInterface(new GClass($interface));
    }
    
    $file = $creater->create($gClass);
    
    $command->info('wrote Class '.$gClass.' to file: '.$file);
    return 0;
  },
  'Creates a new empty Class stub'
);

?>