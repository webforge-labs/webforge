<?php

namespace Webforge\Code\Generator;

/**
 *
 */
class ClassWriterTest extends \Webforge\Code\Test\Base {
  
  protected $classWriter;
  
  public function setUp() {
    $this->classWriter = new ClassWriter();
    
    $this->file = $this->getMock('Psc\System\File', array('writeContents'), array('tmp'));
    $this->classWithImports = new GClass('Webforge\Code\Generator\Fixtures\MyGPSLocateableClass');
    $this->classWithImports->addImport(new GClass('Other\UsedClass'));
    
    $this->classWithoutImports = new GClass('Webforge\Code\Generator\Fixtures\MyEmptyClass');
  }
  
  public function testThatNamespaceAndTagsAreWritten() {
    $this->expectThatWrittenCode(
      $this->logicalAnd(
        $this->stringContains('namespace Webforge\Code\Generator\Fixtures;'),
        $this->stringStartsWith('<?php'),
        $this->stringEndsWith('?>')
      )
    );
    
    $this->classWriter->write($this->classWithoutImports, $this->file);
  }
  
  public function testGClassOwnImportsAreWrittenToFile() {
    // the extraction from GClass is tested in import, we use little acceptance here, to ensure classWriter calls merge
    $this->expectThatWrittenCode($this->stringContains('use'));
    
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
  
  protected function expectThatWrittenCode($constraint, $times = NULL) {
    $this->file->expects($times ?: $this->once())->method('writeContents')
              ->with($constraint)->will($this->returnValue(233));
  }
}
?>