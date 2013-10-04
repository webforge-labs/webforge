<?php

namespace Webforge\Console;

/**
 * Abstraction of input for commands or other classes that have inputs from a string source
 * 
 * use this in your classes to not limit the usage of your command in a symfony console (@see SymfonyCommandInput)
 */
interface CommandInput {

  const MUST_EXIST = 0x000001;

  public function getValue($argumentOrOptionName);

  public function getFlag($optionName);

  public function getEnum($var, Array $allowedValues, $default = NULL);

  public function getDirectory($var, $flags = self::MUST_EXIST);
}
