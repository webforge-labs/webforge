<?php

namespace Webforge\Code\Generator;

class ClassCreater {
  
  const OVERWRITE = ClassWriter::OVERWRITE;
  
  /**
   * @var Webforge\Code\Generator\ClassFileMapper
   */
  protected $mapper;
  
  /**
   * @var GClass
   */
  protected $gClass;
  
  /**
   * @var Webforge\Code\Generator\ClassWriter
   */
  protected $writer;

  /**
   * @var Webforge\Code\Generator\ClassElevator
   */
  protected $elevator;
  
  public function __construct(ClassFileMapper $mapper, ClassWriter $writer, ClassElevator $elevator) {
    $this->mapper = $mapper;
    $this->writer = $writer;
    $this->elevator = $elevator;
  }
  
  /**
   * Creates a new Class and writes it to a file
   *
   * @param GClass $gClass
   * @return Psc\System\File
   */
  public function create(GClass $gClass, $overwrite = FALSE) {
    $file = $this->mapper->getFile($gClass->getFQN());
    
    $this->elevator->elevateParent($gClass);
    $this->elevator->elevateInterfaces($gClass);
    
    $gClass->createAbstractMethodStubs();
    
    $this->writer->write($gClass, $file, $overwrite);
    
    return $file;
  }
  
  /**
   * @chainable
   */
  public function setClassElevator(ClassElevator $elevator) {
    $this->elevator = $elevator;
    return $this;
  }
}
?>