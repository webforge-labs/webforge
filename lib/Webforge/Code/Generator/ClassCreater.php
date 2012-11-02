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
  public function create(GClass $gClass, $overwrite = FALSE) {
    $file = $this->mapper->getFile($gClass->getFQN());
    
    $file->getDirectory()->create();
    
    $gClass->createAbstractMethodStubs();
    
    $this->writer->write($gClass, $file, $overwrite);
    
    return $file;
  }
}
?>