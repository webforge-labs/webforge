<?php

use Webforge\Code\Generator\ClassCreater;
use Webforge\Code\Generator\CreateClassCommand;
use Webforge\Code\Generator\ClassWriter;
use Webforge\Code\Generator\GFunctionBody;
use Webforge\Code\GlobalClassFileMapper;
use Webforge\Code\Generator\GClass;
use Webforge\Code\Generator\GInterface;
use Webforge\Setup\Installer\PartsInstaller;
use Webforge\Framework\Container AS FrameworkContainer;
use Webforge\Framework\Package\Package;

use Psc\JS\JSONConverter;
use Webforge\Common\System\File;
use Webforge\Common\System\Dir;
use Webforge\Common\String;

$container = new FrameworkContainer();

try {
  $container->initLocalPackageFromDirectory($cwd = Dir::factoryTS(getcwd()));
} catch (\Webforge\Framework\LocalPackageInitException $e) {
  print $e->getMessage()."\n";
}

/**
 *
 * $createCommand = function ($name, array|closure $configure, closure $execute, $help = NULL)
 *
 * // argument
 * $arg = function ($name, $description = NULL, $required = TRUE, $multiple = FALSE) // default: required
 *
 * // option
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
  function ($input, $output, $command) use ($container) {
    $cmd = CreateClassCommand::fromContainer($container)
      ->fqn($input->getArgument('fqn'));
      
    if ($parent = $input->getArgument('parent')) {
      $cmd->parent($parent);
    }
    
    foreach ($input->getArgument('interface') as $interface) {
      $cmd->addInterface($interface);
    }
    
    $file = $cmd->write($input->getOption('overwrite'))->getFile();
    
    $command->info('wrote Class '.$cmd->getGClass().' to file: '.$file);
    return 0;
  },
  'Creates a new empty Class stub'
);

$createCommand('create-test',
  array(
    $arg('fqn', 'The full qualified name of the class under test'),
    $flag('overwrite', NULL, 'If set the test will be created, regardless if the file already exists')
  ),
  function ($input, $output, $command) use ($container) {
    $creater = new ClassCreater($container->getClassFileMapper(),
                                $container->getClassWriter(),
                                $container->getClassElevator()
                               );
    
    $gClass = new GClass($input->getArgument('fqn'));
    $testClass = new GClass($gClass->getFQN().'Test');
    $testClass->setParent(
      new GClass('Webforge\Code\Test\Base')
    );
    
    $testClass->createMethod(
      'setUp',
      array(),
      GFunctionBody::create(
        array(
          '$this->chainClass = \''.$gClass->getFQN().'\';',
          'parent::setUp();'
        )
      )
    );
    
    $file = $creater->create($testClass, $input->getOption('overwrite') ? ClassCreater::OVERWRITE : FALSE);
    
    $command->info('wrote Test '.$gClass.' to file: '.$file);
    return 0;
  },
  'Creates a new empty Unit-Test stub'
);

$createCommand('register-package',
  array(
    $arg('location', 'the path to the location of the product (relatives are resolved relative to current work directory)'),
    $arg('type', 'the type for the packageReader (only composer, yet)', FALSE)
  ),
  function ($input, $output, $command) use ($container) {
    $location = $command->validateDirectory($input->getArgument('location'));
    $type = $command->validateEnum($input->getArgument('type') ?: 'composer', array('composer'));
    
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
);

$createCommand('install:part',
  array(
    $arg('part', 'the name of the part. You can see a list of part names in install:list-parts'),
    $arg('location', 'the path to the location of the product (relatives are resolved relative to current work directory). If not set the current work directory is used', FALSE)
  ),
  function ($input, $output, $command) use ($container) {
    $partName = $input->getArgument('part');
    $location = $command->validateDirectory($input->getArgument('location') ?: '.');
    
    $partsInstaller = $container->getPartsInstaller($output);
    
    $part = $partsInstaller->getPart($partName);
    $command->out('installing '.$part->getName());
    
    $partsInstaller->install($part, $location);
  },
  'Installs a part in the current project. Parts are a small snippet without many options. Mostly it just copies a template to the project'
);

$createCommand('install:list-parts',
  array(),
  function ($input, $output, $command) use ($container) {
    $partsInstaller = $container->getPartsInstaller();
    
    $command->info('parts avaible:');
    
    foreach ($partsInstaller->getParts() as $part) {
      $command->info('  '.$part->getName());
    }
    
    return 0;
  },
  'Lists all avaible parts to install'
);

$createCommand('install:create-part',
  array(
    $arg('partName', 'the name of the part (without \'Part\' as suffix)'),
    $flag('overwrite', NULL, 'If set the part will be created, regardless if the file already exists')
  ),
  function ($input, $output, $command) use ($container) {
    $partName = $input->getArgument('partName');
    
    if (String::endsWith($partName, 'Part')) {
      $partName = mb_substr($partName, 0, -4);
    }
    
    $cmd = CreateClassCommand::fromContainer($container)
      ->fqn('Webforge\Setup\Installer\\'.$partName.'Part')
      ->parent('Webforge\Setup\Installer\Part')
      ->withGClass(
        function ($gClass) use($partName) {
          $gClass->createMethod(
            '__construct',
            array(),
            GFunctionBody::create(
              sprintf("    parent::__construct('%s');\n", $partName)
            )
          );
       })
      ->write($input->getOption('overwrite'));
    
    $command->info('wrote Part '.$partName.' to file: '.$cmd->getFile());
    $command->comment('you need to add '.$cmd->getGClass()->getFQN().' to Webforge\Framework\Container::getPartsInstaller() !');
    
    return 0;
  },
  'Creates a new part in the Installer'
);


$createCommand('composer',
  array(
    $arg('composerArguments', 'all parameters passed to composer without --working-dir', $required = TRUE, $multiple = TRUE)
  ),
  function ($input, $output, $command) use ($container) {
    $args = $input->getArgument('composerArguments');
    
    $package = $container->getLocalPackage();
    $vendorDir = $package->getDirectory(Package::VENDOR);
    $composer = 'composer';
    
    system($composer.' --working-dir="'.$vendorDir->getQuotedString().'" '.implode(' ',$args));
    
    $command->info('written '.$source);
  },
  "Calls composer for the current package (in the right directory)"
);

$createCommand('windows:batch-link',
  array(
    $arg('source', 'the name of the file you want to link from. in 99% of all cases you want to pass .bat with it'),
    $arg('destination', 'the name of the bin you want to link to. (with extension)')
  ),
  function ($input, $output, $command) use ($container) {
    $destination = $command->validateFile($input->getArgument('destination'));
    $source = $command->validateFile($input->getArgument('source'), 0);
    
    if ($source->exists()) {
      throw $command->exitException('Source: '.$source.' does exist. Will not overwride..', 1);
    }
    
    
    try {
      $relativeDestination = clone $destination->getDirectory();
      $relativeDestination->makeRelativeTo($source->getDirectory());
      
      $path = '%~dp0'.((string) $relativeDestination);
      
    } catch (\Exception $e) {
      // use absolute path
      $path = (string) $destination->getDirectory()->resolvePath();
    }
    
    
    $source->writeContents(
      "@echo off\r\n".
      $path.$destination->getName()." %*\r\n"
    );
    
    $command->info('written '.$source);
  },
  "Creates a link to another batch/binary file from source to destination"
);
?>