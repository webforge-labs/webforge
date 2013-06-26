<?php

namespace Webforge\Console;

/**
 * Abstraction of input for commands or other classes that have inputs from a string source
 * 
 * use this in your classes to not limit the usage of your command in a symfony console (@see SymfonyCommandInput)
 */
interface CommandInput {

  const MUST_EXIST = 0x000001;
}
