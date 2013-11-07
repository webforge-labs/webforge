<?php

namespace Webforge\Setup;

use Webforge\Common\System\File;
use Webforge\Common\System\Dir;
use Webforge\Common\ArrayUtil as A;

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
    
    $mappedFiles = $this->info->getFilesInfos('Webforge\Setup\Something', $root);
    $this->assertCount(1, $mappedFiles);
    $mappedFileInfo = $mappedFiles[0];
    $this->assertInstanceOf('Webforge\Common\System\File', $mappedFileInfo->file);
    $this->assertEquals('Webforge', $mappedFileInfo->prefix, 'prefix from fileinfo');
    
    $this->assertEquals(
      (string) $root->sub('lib/Webforge/Setup/')->getFile('Something.php'),
      (string) $mappedFileInfo->file,
      'relative autoloading path is wrong'
    );
  }
  
  public function testGetFileMapsABSPaths() {
    $resolveRelativesTo = $this->getTestDirectory();
    
    $mappedFiles = $this->absoluteInfo->getFilesInfos('Webforge\Setup\Something', $resolveRelativesTo);
    $this->assertCount(1, $mappedFiles);
    $mappedFileInfo = $mappedFiles[0];
    $this->assertInstanceOf('Webforge\Common\System\File', $mappedFileInfo->file);

    $this->assertEquals(
      (string) $this->absoluteLibraryLocation->sub('Webforge/Setup/')->getFile('Something.php'),
      (string) $mappedFileInfo->file,
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
    $this->setExpectedException('Webforge\Setup\NoAutoLoadPrefixException');
    
    $root = new Dir(__DIR__.DIRECTORY_SEPARATOR);
    $this->emptyInfo->getMainPrefixAndPath($root);
  }
  
  public function testAmbInfoReturnsAllFiles() {
    $mappedFileInfos = $this->ambiguousInfo->getFilesInfos('Webforge\Setup\Something', $root = $this->getTestDirectory());

    $this->assertCount(2, $mappedFileInfos);

    $mappedFiles= array();
    foreach ($mappedFileInfos as $fileInfo) {
      $mappedFiles[] = (string) $fileInfo->file;
    }
    
    $this->assertArrayEquals(
      $mappedFiles,
      Array(
        (string) $root->sub('tests/Webforge/Setup/')->getFile('Something.php'),
        (string) $root->sub('lib/Webforge/Setup/')->getFile('Something.php')
      ),
      $mappedFiles
    );
  }
}
