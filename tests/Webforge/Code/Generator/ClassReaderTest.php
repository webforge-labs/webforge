<?php

namespace Webforge\Code\Generator;

class ClassReaderTest extends ClassReaderBaseTest {
  
  public function testReaderReadsAnEmptyClass() {
    $this->expectFileHasContents(<<<'PHP'
<?php
namespace ACME;

class Console {
  
}
?>
PHP
    );
    
    $gClass = $this->read();
    
    $this->assertEquals('ACME\Console', $gClass->getFQN(), 'FQN of read class is wrong');
  }
  
  public function testPutsImportsForReadUses() {
    $this->expectFileHasContents(<<<'PHP'
<?php
namespace ACME;

use Psc\System\File;
use Webforge\Common\String AS S;

class Console {
  
}
?>
PHP
    );
    
    $gClass = $this->read();
    
    $this->assertCount(2, $imports = $gClass->getImports());
    $this->assertTrue($imports->have(new GClass('Psc\System\File')), 'imports do not have Psc\System\File');
    $this->assertTrue($imports->have('S'), 'imports do not have S as Alias. Parsed are: '.implode(',', array_keys($imports->toArray())));
    $this->assertEquals('Webforge\Common\String', $imports->get('S')->getFQN());
  }

  public function testReadIntoReturnsTheSameClassFromArgument() {
    $php = $this->inClass('public function export() {}');
    $this->expectFileHasContents($php);
    
    $gClass = new GClass('does not matter');
    $rGClass = $this->classReader->readInto($this->file, $gClass);
    
    $this->assertSame($rGClass, $gClass);
    $this->assertThatGClass($gClass)->hasMethod('export');
  }
  
  
  public function testClassReaderThrowsRuntimeExIfPHPIsMalformed() {
    $this->php = '<?php a parse error ?>';
    
    $this->setExpectedException('RuntimeException');
    $this->read();
  }
}
?>