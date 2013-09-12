<?php

namespace Webforge\Console;

/**
 * Abstraction of output for commands or other classes the write out a stream of infos and messages
 * 
 * use this in your classes to not limit the usage of your command in a symfony console (@see SymfonyCommandOutput)
 */
interface CommandOutput {

  /**
   * Some normal printed message (a linebreak will be appended)
   * 
   */
  public function msg($msg);

  /**
   * A success message that is printed highlighted and should indicate a successful event
   */
  public function ok($msg);

  /**
   * A warning message that is printed highlighted and should indicate some information that is more important than a normal message, but not an error, yet
   */
  public function warn($msg);

}
