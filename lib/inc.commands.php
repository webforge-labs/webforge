<?php

use Webforge\Code\Generator\ClassCreater;
use Webforge\Code\Generator\ClassWriter;
use Webforge\Code\GlobalClassFileMapper;
use Webforge\Code\Generator\GClass;
use Webforge\Code\Generator\GInterface;
use Webforge\Framework\Container AS FrameworkContainer;

use \Psc\JS\JSONConverter;
use Psc\System\File;

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
    $arg('parent', 'The full qualified name of the parent class', FALSE),
    $arg('interface', 'The full qualified names of one or more interfaces', FALSE, $multiple = TRUE),
    $flag('overwrite', NULL, 'If set the class will be created, regardless if the file already exists')
  ),
  function ($input, $output, $command) {
    $container = new FrameworkContainer();
    
    $creater = new ClassCreater($container->getClassFileMapper(),
                                $container->getClassWriter(),
                                $container->getClassElevator()
                               );
    
    $gClass = new GClass($input->getArgument('fqn'));
    
    if (($parent = $input->getArgument('parent'))) {
      $gClass->setParent($parent = new GClass($parent));
    }
    
    foreach ($input->getArgument('interface') as $interface) {
      $gClass->addInterface(new GInterface($interface));
    }
    
    $file = $creater->create($gClass, $input->getOption('overwrite') ? ClassCreater::OVERWRITE : FALSE);
    
    $command->info('wrote Class '.$gClass.' to file: '.$file);
    return 0;
  },
  'Creates a new empty Class stub'
);

$createCommand('register-package',
  array(
    $arg('location', 'the path to the location of the product (relatives are resolved relative to current work directory)'),
    $arg('type', 'the type for the packageReader (only composer, yet)', FALSE)
  ),
  function ($input, $output, $command) {
    $location = $command->validateDirectory($input->getArgument('location'));
    $type = $command->validateEnum($input->getArgument('type') ?: 'composer', array('composer'));
    
    $container = new FrameworkContainer();
    
    if ($type === 'composer') {
      $package = $container->getPackageRegistry()->addComposerPackageFromDirectory($location);
      $command->info(sprintf("Found ComposerPackage: '%s'", $package->getSlug()));
      
      // write to packages.json in appDir
      $packagesFile = $container->getApplicationStorage()->getFile('packages.json');
      $jsonConverter = new JSONConverter();
      
      // read
      $packages = new \stdClass;
      if ($packagesFile->exists()) {
        $packages = $jsonConverter->parseFile($packagesFile);
      }
      
      // modify
      $packages->{$package->getSlug()} = (string) $package->getRootDirectory();      
      
      // write
      $packagesFile->writeContents($jsonConverter->stringify($packages, JSONConverter::PRETTY_PRINT));
      $command->info('updated packages-registry in: '.$packagesFile.'');
    }
  },
  'Registers a local package to be noticed by webforge'
)

?>