<?php

namespace Webforge\Code\Generator;

class ClassWriterTest extends \Webforge\Code\Test\Base {
  
  protected $classWriter;
  
  public function setUp() {
    $this->classWriter = new ClassWriter();
    
    $this->file = $this->getMock('Webforge\Common\System\File', array('writeContents','exists'), array('tmp'));
    $this->classWithImports = new GClass('Webforge\Code\Generator\Fixtures\MyGPSLocateableClass');
    $this->classWithImports->addImport(new GClass('Other\UsedClass'));
    
    $this->classWithoutImports = new GClass('Webforge\Code\Generator\Fixtures\MyEmptyClass');
  }
  
  public function testThatNamespaceAndTagsAreWritten() {
    $this->expectThatWrittenCode(
      $this->logicalAnd(
        $this->stringContains('namespace Webforge\Code\Generator\Fixtures;'),
        $this->stringStartsWith('<?php'),
        $this->stringEndsWith("}\n"),
        $this->logicalNot($this->stringEndsWith("}\n\n"))
      )
    );
    
    $this->classWriter->write($this->classWithoutImports, $this->file);
  }
  
  public function testGClassOwnImportsAreWrittenToFile() {
    // the extraction from GClass is tested in import, we use little acceptance here, to ensure classWriter calls merge
    $this->expectThatWrittenCode($this->stringContains('use Other\UsedClass;'));
    
    $this->classWriter->write($this->classWithImports, $this->file);
  }
  
  public function testIfGClassWithoutImportsIsWritten_NoUseIsInFile() {
    $this->expectThatWrittenCode($this->logicalNot($this->stringContains('use')));
    
    $this->classWriter->write($this->classWithoutImports, $this->file);
  }
  
  public function testGClassOwnImportsDontGetMergedWithTheClassWriterImports() {
    $this->classWriter->addImport(new GClass('Doctrine\ORM\Mapping', 'ORM'));
    
    $expectedImports = $this->classWriter->getImports()->toArray();
    
    $this->classWriter->write($this->classWithImports, $this->file);
    
    $this->assertEquals($expectedImports, $this->classWriter->getImports()->toArray());
  }
  
  public function testClassWriterDoesNotOverwriteExistingFiles() {
    $this->expectFileExists(TRUE);
    
    $this->setExpectedException('Webforge\Code\Generator\ClassWritingException');
    
    $this->classWriter->write($this->classWithImports, $this->file);
  }

  public function testPropertiesCanHaveLiteralDefaultValuesThatGetWrittnByTheWritterLiterally() {
    $gProperty = new GProperty(
      'propWithDefault',
      \Webforge\Types\Type::create('Float'),
      '0.5'
    );
    $gProperty->interpretDefaultValueLiterally();

    $php = $this->classWriter->writeProperty($gProperty, 0);
    $this->assertContains('$propWithDefault = 0.5', $php);
  }
  
  protected function expectThatWrittenCode($constraint, $times = NULL) {
    $this->expectFileExists(FALSE);
    $this->file->expects($times ?: $this->once())->method('writeContents')
              ->with($constraint)->will($this->returnValue(233));
  }

  protected function expectFileExists($bool = FALSE, $times = NULL) {
    $this->file->expects($times ?: $this->once())->method('exists')
              ->will($this->returnValue($bool));
  }
}
