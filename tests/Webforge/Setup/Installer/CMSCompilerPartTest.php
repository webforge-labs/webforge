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

    $namespacePath = str_replace('\\', '/', $this->package->getNamespace());
    
    $this->assertArrayEquals(
      array(
        '/lib/'.$namespacePath.'/Entities/Compiler.php',
        '/lib/'.$namespacePath.'/CMS/CompileCommand.php',
      ),
      $this->getWrittenFiles($this->macro)
    );

    $this->assertHasAddedCLICommand($this->macro, $this->package->getNamespace().'\\CMS\\CompileCommand');
  }
}
