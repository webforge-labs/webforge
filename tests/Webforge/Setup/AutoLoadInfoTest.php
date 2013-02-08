<?php

namespace Webforge\Setup;

use Webforge\Common\System\File;
use Webforge\Common\System\Dir;
use Psc\A;

class AutoLoadInfoTest extends \Webforge\Code\Test\Base {
  
  public function setUp() {
    $this->absoluteLibraryLocation = new Dir(
      (DIRECTORY_SEPARATOR === '\\' ? 'C:\programmes\cygwin\\' : '/')
      .'var/local/www/lib/'
    );
    
    $this->info = new AutoLoadInfo(json_decode('{"psr-0": {"Webforge": ["lib/"]}}'));
    $this->emptyInfo = new AutoLoadInfo(array());
    $this->ambiguousInfo = new AutoLoadInfo(json_decode('{"psr-0": {"Webforge": ["lib/", "tests/"]}}'));
    $this->absoluteInfo = new AutoLoadInfo(Array(
      'psr-0'=>(object) array(
        'Webforge'=> array((string) $this->absoluteLibraryLocation)
      )
    ));
  }
  
  public function testGetFileMapsAFQNToAFileRegardlessIfItExists() {
    $root = $this->getTestDirectory();
    
    $mappedFiles = $this->info->getFiles('Webforge\Setup\Something', $root);
    $this->assertCount(1, $mappedFiles);
    $mappedFile = $mappedFiles[0];
    $this->assertInstanceOf('Webforge\Common\System\File', $mappedFile);
    
    $this->assertEquals(
      (string) $root->sub('lib/Webforge/Setup/')->getFile('Something.php'),
      (string) $mappedFile,
      'relative autoloading path is wrong'
    );
  }
  
  public function testGetFileMapsABSPaths() {
    $resolveRelativesTo = $this->getTestDirectory();
    
    $mappedFiles = $this->absoluteInfo->getFiles('Webforge\Setup\Something', $resolveRelativesTo);
    $this->assertCount(1, $mappedFiles);
    $mappedFile = $mappedFiles[0];
    $this->assertInstanceOf('Webforge\Common\System\File', $mappedFile);

    $this->assertEquals(
      (string) $this->absoluteLibraryLocation->sub('Webforge/Setup/')->getFile('Something.php'),
      (string) $mappedFile,
      'absolute autoloading path is wrong'
    );
  }
  
  public function testGetMainPrefixAndPathReturnsTheFirstPathAndPrefixFromFirstAutoLoad() {
    $root = new Dir(__DIR__.DIRECTORY_SEPARATOR);
    $this->assertEquals(array('Webforge', $root->sub('lib/')), $this->ambiguousInfo->getMainPrefixAndPath($root));
    
    $root = $this->absoluteLibraryLocation->up();
    $this->assertEquals(array('Webforge', $this->absoluteLibraryLocation), $this->absoluteInfo->getMainPrefixAndPath($root));
  }
  
  public function testGetMainPrefixAndPathThrowsExceptionWhenNotAutoloadPrefixesAreDefined() {
    $this->setExpectedException('RuntimeException');
    
    $root = new Dir(__DIR__.DIRECTORY_SEPARATOR);
    $this->emptyInfo->getMainPrefixAndPath($root);
  }
  
  public function testAmbInfoReturnsAllFiles() {
    $mappedFiles = $this->ambiguousInfo->getFiles('Webforge\Setup\Something', $root = $this->getTestDirectory());

    $this->assertCount(2, $mappedFiles);
    
    $this->assertArrayEquals(
      A::stringify($mappedFiles),
      A::stringify(Array(
        $root->sub('tests/Webforge/Setup/')->getFile('Something.php'),
        $root->sub('lib/Webforge/Setup/')->getFile('Something.php')
      )),
      $mappedFiles
    );
  }
}
?>