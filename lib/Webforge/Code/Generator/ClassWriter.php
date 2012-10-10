<?php

namespace Webforge\Code\Generator;

use Psc\Code\Generate\ClassWritingException;
use Psc\System\File;

/**
 * Writes a Class in Code (PHP)
 *
 * The ClassWriter writes the Stream in the gClass to a given File
 * 
 * This writer should change somehow, so that it does not use the GClass inner
 * Functions to generate the PHP from psc-cms and that its writing is own code
 */
class ClassWriter {
  
  const OVERWRITE = TRUE;
  
  /**
   * @var Webforge\Code\Generator\Imports
   */
  protected $imports;
  
  public function __construct() {
    $this->imports = new Imports();
  }
  
  public function write(GClass $gClass, File $file, $overwrite = FALSE) {
    if ($file->exists() && $overwrite !== self::OVERWRITE) {
      throw new ClassWritingException(
        sprintf('The file %s already exists. To overwrite set the overwrite parameter.', $file),
        ClassWritingException::OVERWRITE_NOT_SET
      );
    }
    
    $file->writeContents($this->generatePHP($gClass));
    return $this;
  }
  
  protected function generatePHP(GClass $gClass) {
    $php = '<?php'.PHP_EOL;
    $php .= PHP_EOL;
    
    if (($namespace = $gClass->getNamespace()) != NULL) {
      $php .= 'namespace '.$namespace.';'.PHP_EOL;
      $php .= PHP_EOL;
    }
    
    $imports = clone $this->imports;
    $imports->mergeFromClass($gClass);
    
    if ($use = $imports->php($namespace)) {
      $php .= $use;
      $php .= PHP_EOL;
    }
    
    $php .= $gClass->php();
    
    $php .= '?>';
    return $php;
  }
  
  /**
   * Adds an Import, that should be added to every written file
   * 
   */
  public function addImport(GClass $gClass, $alias = NULL) {
    $this->imports->add($gClass, $alias);
    return $this;
  }

  // @codeCoverageIgnoreStart
  /**
   * Removes an Import, that should be added to every written file
   *
   * @param string $alias case insensitive
   */
  public function removeImport($alias) {
    $this->imports->remove($alias);
    return $this;
  }
  
  /**
   * @param Webforge\Code\Generator\Imports $imports
   * @chainable
   */
  public function setImports(Imports $imports) {
    $this->imports = $imports;
    return $this;
  }
  // @codeCoverageIgnoreEnd  
  
  /**
   * @return Webforge\Code\Generator\Imports
   */
  public function getImports() {
    return $this->imports;
  }
  
}
?>