<?php

namespace Webforge\Code\Generator;

use Psc\Code\Generate\DocBlock;

/**
 * A Base Class of the G*Model
 *
 */
abstract class GObject {
  
  const UNDEFINED = '::.WebforgeCodeGeneratorDefaultIsUndefined.::';
  
  /**
   * @var Psc\Code\Generate\DocBlock
   */
  protected $docBlock;
  
  /**
   * Returns a unique key for the index in a GObjectCollection in a GClass
   * 
   * @return string
   */
  abstract public function getKey();

  /**
   * @param Psc\Code\Generate\DocBlock $docBlock
   * @chainable
   */
  public function setDocBlock(DocBlock $docBlock) {
    $this->docBlock = $docBlock;
    return $this;
  }
  
  /**
   * Creates a new DocBlock for the class
   *
   * overwrites previous ones
   */
  public function createDocBlock($body = NULL) {
    $block = new DocBlock($body);
    $this->setDocBlock($block);
    return $block;
  }
  
  /**
   * Returns the DocBlock
   *
   * if no DocBlock is there, it will be created
   * @return Psc\Code\Generate\DocBlock|NULL
   */
  public function getDocBlock() {
    if (!$this->hasDocBlock())
      $this->createDocBlock();
    
    return $this->docBlock;
  }
  
  /**
   * @return bool
   */
  public function hasDocBlock() {
    return $this->docBlock != NULL;
  }  
}
?>