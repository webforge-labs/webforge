<?php

namespace Webforge\Console;

/**
 * Abstraction of output for commands or other classes the write out a stream of infos and messages
 * 
 * use this in your classes to not limit the usage of your command in a symfony console (@see SymfonyCommandOutput)
 */
interface CommandOutput {

  /**
   * A success message that is printed highlighted and should indicate a successful event
   */
  public function ok($msg);

}
