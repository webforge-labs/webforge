<?php

namespace Webforge\Framework\CLI;

use Psc\System\Console\CommandsIncluder;
use Webforge\Common\System\Dir;
use Webforge\Framework\Container;
use Webforge\Framework\LocalPackageInitException;
use Webforge\Common\ClassUtil;

class Application extends \Webforge\Console\Application {

  public function __construct(Dir $root, Container $container = NULL) {

    $this->container = $container;

    if (!isset($this->container)) {
      $this->container = new Container();

      try {
        $this->container->initLocalPackageFromDirectory(Dir::factoryTS(getcwd()));
      } catch (LocalPackageInitException $e) {
        print $e->getMessage()."\n";
      }
    }

    parent::__construct('webforge-console');

    $this->loadCommands($root);
  }

  protected function loadCommands(Dir $root) {
    $includer = new CommandsIncluder($root->sub('lib/')->getFile('inc.commands.php'), array('container'=>$this->container));
    $this->addCommands($includer->getCommands());

    $api = $includer->buildArgsAPI();

    foreach (array('RegisterPackage') as $commandName) {
      $fqn = ClassUtil::expandNamespace($commandName, __NAMESPACE__);
      $cliCommand = new $fqn($this->container);

      $consoleCommand = new SymfonyCommand($cliCommand->getCLIName(), $cliCommand);
      $consoleCommand->setDefinition($cliCommand->defineArguments($api));
      $consoleCommand->setDescription($cliCommand->getDescription());

      $this->add($consoleCommand);
    }
  }
}
