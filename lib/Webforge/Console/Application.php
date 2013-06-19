<?php

namespace Webforge\Console;

use Webforge\Common\System\Dir;
use Psc\System\Console\CommandsIncluder;

class Application extends \Symfony\Component\Console\Application {

  /**
   * Creates a new Application from the base Directory
   * 
   * Searches in lib for inc.commands.php for some commands
   */
  public static function fromDirectory(Dir $base) {
    return self::fromIncluder(
      new CommandsIncluder($base->sub('lib/')->getFile('inc.commands.php'))
    );
  }

  public static function fromIncluder(CommandsIncluder $includer) {
    $application = new static();
    $application->addCommands($includer->getCommands());

    return $application;
  }
}
