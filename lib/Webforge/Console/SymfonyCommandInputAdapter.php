<?php

namespace Webforge\Console;

use Symfony\Component\Console\Input\InputInterface;
use Webforge\Common\System\Dir;
use InvalidArgumentException;

/**
 * Adapter for Symfony Input to the Webforge\Console\CommandInput Interface
 * 
 * this adapter does validate a lot of inputs
 */
class SymfonyCommandInputAdapter implements CommandInput {

  protected $consoleInput;

  public function __construct(InputInterface $input) {
    $this->consoleInput = $input;
  }

  public function getDirectory($var, $flags = self::MUST_EXIST) {
    $path = $this->getValue($var);

    $errorDetail = NULL;

    if (!empty($path)) {
      $dir = Dir::factoryTS($path);

      $dir->resolvePath();
    
      if (!($flags & self::MUST_EXIST) || $dir->exists()) {
        
        return $dir;
      
      } else {
        $errorDetail = ' It have to exist!';
      }
    }
    
    throw new InvalidArgumentException(sprintf("Directory from path: '%s' cannot be found.%s", $path, $errorDetail));
  }

  public function getEnum($var, Array $allowedValues, $default = NULL) {
    $value = $this->getValue($var);

    if ($value === NULL) {
      return $default;
    }

    if (!in_array($value, $allowedValues)) {
      throw new InvalidArgumentException(sprintf("the value '%s' is not allowed. Allowed are only: %s", $value, implode(',', $allowedValues)));
    }

    return $value;
  }

  public function getFlag($var) {
    return $this->consoleInput->getOption($var);
  }

  public function getValue($var) {
    return $this->consoleInput->hasArgument($var) ? $this->consoleInput->getArgument($var) : $this->consoleInput->getOption($var);
  }
}
