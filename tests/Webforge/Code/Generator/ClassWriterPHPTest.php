<?php

namespace Webforge\Code\Generator;

/**
 *
 */
class ClassWriterPHPTest extends \Webforge\Code\Test\Base {
  
  protected $classWriter;
  
  public function setUp() {
    $this->classWriter = new ClassWriter();
  }
  
  public function testWriteGClass_ContainsDocBlock() {
    $gClass = new GClass('WithDocBlock');
    $gClass->createDocBlock('The comment');
    
    $phpCode = 
<<<'PHP'
/**
 * The comment
 */
class WithDocBlock {
}
PHP;

    $this->assertEquals($phpCode, $this->classWriter->writeGClass($gClass, $namespace = NULL, "\n"));
  }
  
  
  public function testWriteGClass_ExtendsIsWrittenAsClassNameWhenInSameCONTEXTNamespace() {
    $gClass = GClass::create('ACME\Types\Type')->setParent(new GClass('ACME\Types\BaseType'));
    
    $phpCode =
<<<'PHP'
class Type extends BaseType {
}
PHP;
    
    $this->assertEquals($phpCode, $this->classWriter->writeGClass($gClass, $namespace = 'ACME\Types',"\n"));
  }

  public function testWriteGClass_ExtendsIsWrittenAsFullIfNotSameCONTEXTNamespace() {
    $gClass = GClass::create('ACME\Console')->setParent(new GClass('Webforge\System\Console'));
    
    $phpCode =
<<<'PHP'
class Console extends \Webforge\System\Console {
}
PHP;
    
    $this->assertEquals($phpCode, $this->classWriter->writeGClass($gClass, $namespace = 'ACME', "\n"));
  }
}
?>