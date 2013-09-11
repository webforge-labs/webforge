<?php

namespace Webforge\Setup\Installer;

class CMSCompilerPartTest extends \Webforge\Code\Test\InstallerPartTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\Installer\\CompilerPart';
    parent::setUp();
    
    $this->part = new CMSCompilerPart();
  }
  
  public function testWritesExpectedFilesAndAddsCommand() {
    $this->macro = $this->installer->dryInstall($this->part, $this->target);
    
    $this->assertArrayEquals(
      array(
        '/lib/'.$this->package->getNamespace().'/Entities/Compiler.php',
        '/lib/'.$this->package->getNamespace().'/CMS/CompileCommand.php',
      ),
      $this->getWrittenFiles($this->macro)
    );

    $this->assertHasAddedCLICommand($this->macro, $this->package->getNamespace().'\\CMS\\CompileCommand');
  }
}
