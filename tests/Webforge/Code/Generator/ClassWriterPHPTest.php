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
    
    $this->assertCodeEquals($phpCode, $this->classWriter->writeGClass($gClass, $namespace = 'ACME\Types',"\n"));
  }

  public function testWriteGClass_ExtendsIsWrittenAsFullIfNotSameCONTEXTNamespace() {
    $gClass = GClass::create('ACME\Console')->setParent(new GClass('Webforge\System\Console'));
    
    $phpCode =
<<<'PHP'
class Console extends \Webforge\System\Console {
}
PHP;
    
    $this->assertCodeEquals($phpCode, $this->classWriter->writeGClass($gClass, $namespace = 'ACME', "\n"));
  }
  
  public function testGClassHasModifiers() {
    $gClass = GClass::create('ACME\Console')->setAbstract(TRUE);
    
    $phpCode = 'abstract class Console {}';
    $this->assertCodeEquals($phpCode, $this->classWriter->writeGClass($gClass, $namespace = 'ACME'));
  }
  
  public function testWritesGMethodWithParameters() {
    $method = GMethod::create('someAction', array(GParameter::create('xValue', new GClass('PointValue')),
                                                  GParameter::create('yValue', new GClass('PointValue')),
                                                  GParameter::create('info', $this->getType('Array'))
                                                    ->setDefault(array('x','y'))
                                                  )
                              );
    $phpCode = <<<'PHP'
public function someAction(PointValue $xValue, PointValue $yValue, Array $info = array('x', 'y')) {
}
PHP;
    
    $this->assertCodeEquals($phpCode, $this->classWriter->writeMethod($method));
  }
  
  public function testWritesInterfaces() {
    $if = GInterface::create('ACME\Exportable');
    
    $phpCode = 'interface Exportable {}';
    $this->assertCodeEquals($phpCode, $this->classWriter->writeGClass($if, $namespace = 'ACME'));
  }

  public function testWritesInterfaceMethodsAsMethodWithoutBody() {
    $if = GInterface::create('ACME\Exportable');
    $if->createMethod('export');
    
    $phpCode =
<<<'PHP'
interface Exportable {

  public function export();
}
PHP;

    $this->assertCodeEquals($phpCode, $this->classWriter->writeGClass($if, $namespace = 'ACME'));
  }
}
?>