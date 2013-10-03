<?php

namespace Webforge\Console;

/**
 * Abstraction of output for commands or other classes the write out a stream of infos and messages
 * 
 * use this in your classes to not limit the usage of your command in a symfony console (@see SymfonyCommandOutput)
 */
interface CommandOutput extends \Webforge\Common\CommandOutput {
}
