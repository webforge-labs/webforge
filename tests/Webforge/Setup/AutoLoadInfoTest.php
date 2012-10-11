<?php

namespace Webforge\Setup;

use Psc\System\File;
use Psc\System\Dir;

class AutoLoadInfoTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->absLib = new Dir(
      (DIRECTORY_SEPARATOR === '\\' ? 'C:\programmes\cygwin\\' : '/')
      .'var/local/www/lib/'
    );
    
    $this->info = new AutoLoadInfo(json_decode('{"psr-0": {"Webforge": ["lib/"]}}'));
    $this->ambinfo = new AutoLoadInfo(json_decode('{"psr-0": {"Webforge": ["lib/", "tests/"]}}'));
    $this->absinfo = new AutoLoadInfo(Array(
      'psr-0'=>(object) array(
        'Webforge'=> array((string) $this->absLib)
      )
    ));
  }
  
  public function testGetFileMapsAFQNToAFileRegardlessIfItExists() {
    $root = $this->getTestDirectory(); // can be any, never mind
    
    $mappedFile = $this->info->getFile('Webforge\Setup\Something', $root);
    
    $this->assertEquals(
      (string) $root->sub('lib/Webforge/Setup/')->getFile('Something.php'),
      (string) $mappedFile,
      'relative autoloading path is wrong'
    );
  }
  
  public function testGetFileMapsABSPaths() {
    $root = $this->getTestDirectory(); // can be any, never mind
    
    $mappedFile = $this->absinfo->getFile('Webforge\Setup\Something', $root);
    $this->assertInstanceOf('Psc\System\File', $mappedFile);

    $this->assertEquals(
      (string) $this->absLib->sub('Webforge/Setup/')->getFile('Something.php'), // this has nothing in common with $root
      (string) $mappedFile,
      'absolute autoloading path is wrong'
    );
  }
}
?>