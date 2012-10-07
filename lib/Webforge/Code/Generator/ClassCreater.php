<?php

namespace Webforge\Code\Generator;

class ClassCreater {
  
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
  
  public function __construct(ClassFileMapper $mapper, ClassWriter $writer) {
    $this->mapper = $mapper;
    $this->writer = $writer;
  }
  
  /**
   * Creates a new Class and writes it to a file
   *
   * @param GClass $gClass
   * @return Psc\System\File
   */
  public function create(GClass $gClass) {
    $file = $this->mapper->getFile($gClass->getFQN());
    
    $this->writer->write($gClass, $file);
    
    return $file;
  }
}
?>