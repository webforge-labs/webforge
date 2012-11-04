<?php

use Psc\System\Dir;
use Psc\System\File;

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

$createCommand('example-command-name',
  array(
    $arg('firstArgument', 'this describes argument one'),
    $flag('some-flag', NULL, 'description of flag')
  ),
  function ($input, $output, $command) {
    $command->out('im processing nothing yet. change me in lib/inc.commands.php!');
    
    return 0;
  },
  'a brief description of the command (see lib/inc.commands.php to change chis command)'
);
?>