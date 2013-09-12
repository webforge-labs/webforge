<?php

namespace Webforge\Console;

/**
 * Abstraction of interaction with the user for commands or other clases
 * 
 * use this in your classes to not limit the usage of your command in a symfony console (@see InteractionHelper)
 * 
 */
interface CommandInteraction {

  /**
   * @return string
   */
  public function ask($question, $default = NULL);

  /**
   * Like ask() but it formats the question nicer
   * 
   * @return string
   */
  public function askDefault($question, $default);


  /**
   * @return bool
   */
  public function confirm($question, $default = TRUE);


  /**
   * Like ask() but the return value is validated with $validator
   * 
   * if $validator throws an Exception the question is reasked x $attempts (or none)
   * validator can return a modified value of the given from ask()
   * @return mixed
   */
  public function askAndValidate($question, \Closure $validator, $attempts = FALSE);
}
