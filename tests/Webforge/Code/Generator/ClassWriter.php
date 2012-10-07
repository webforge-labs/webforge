<?php

namespace Webforge\Code\Generator;

use Psc\System\File;

/**
 * Writes a Class in Code (PHP)
 *
 * This writer should change somehow, so that it does not use the GClass inner Functions from psc-cms and that its writing is own code
 */
class ClassWriter {
  
  /**
   * @var Webforge\Code\Generator\Imports
   */
  protected $imports;
  
  public function __construct() {
    $this->imports = new Imports();
  }
  
  public function write(GClass $gClass, File $file) {
    $file->writeContents($this->generatePHP($gClass));
  }
  
  protected function generatePHP(GClass $gClass) {
    $php = '<?php'.PHP_EOL;
    $php .= PHP_EOL;
    
    if ($gClass->getNamespace() != NULL) {
      $php .= 'namespace '.$gClass->getNamespace().';'.PHP_EOL;
      $php .= PHP_EOL;
    }
    
    $use = '';
    foreach ($this->getImports() as $import) {
      // does the class needs to be importet?
      if ($import->getNamespace() !== $gClass->getNamespace()) {
        $alias = $gClass->getAliasFor($import);
          
        $use .= 'use ';  
        if ($alias === $import->getClassName()) {
          $use .= $import->getFQN();
        } else {
          $use .= $import->getFQN().' AS '.$alias;
        }
  
        $use .= PHP_EOL;
      }
    }
    
    if ($use)
      $php .= $use.PHP_EOL;
    
    $php .= $gClass->php();
    
    $php .= '?>';
    return $php;
  }
  
  /**
   * @param Webforge\Code\Generator\Imports $imports
   * @chainable
   */
  public function setImports(Imports $imports) {
    $this->imports = $imports;
    return $this;
  }

  /**
   * @return Webforge\Code\Generator\Imports
   */
  public function getImports() {
    return $this->imports;
  }
}
?>