<?php

namespace Webforge\Common;

interface Macro {
  
  /**
   * @return Command[]
   */
  public function getCommands();
  
  
  public function execute();
  
}
?>