<?php

namespace Webforge\Setup;

use Webforge\Common\System\File;

class ApplicationStorageTest extends \Webforge\Code\Test\Base {
  
  protected $storage;
  
  public function setUp() {
    $this->storage = new ApplicationStorage('webforge-test');
  }

  public function testImmplementsFileStorage() {
    $this->assertInstanceOf('Webforge\Common\System\FileStorage', $this->storage);
  }
  
  public function testApplicationStorageMaintainsADirectoryToReadAndWriteTo() {
    $dir = $this->storage->getDirectory();
    
    $this->assertInstanceOf('Webforge\Common\System\Dir', $dir);
    $this->assertTrue($dir->exists(), $dir.' from application storage does not exist');
    $this->assertTrue($dir->isReadable(), $dir.' from application storage cannot be read');
    $this->assertTrue($dir->isWriteable(), $dir.' from application storage cannot be written');
  }
  
  public function testApplicationStorageGetsAfile() {
    $file = $this->storage->getFile('configs/webforge.json');
    
    $this->assertInstanceOf('Webforge\Common\System\File', $file);
    $this->assertEquals(
      (string) $this->storage->getDirectory()->sub('configs/')->getFile('webforge.json'),
      (string) $file
    );    
  }
  
  public function testGetDirectoryCanReturnSubs() {
    $this->assertEquals(
      (string) $this->storage->getDirectory()->sub('configs/'),
      (string) $this->storage->getDirectory('configs/')
    );
  }
  
  /**
   * @dataProvider badNames
   * @expectedException InvalidArgumentException
   */
  public function testApplicationStorageMustHaveANiceNameForCons($name) {
    new ApplicationStorage($name);
  }
  
  public static function badNames() {
    return Array (
      array('with spaces is not okay'),
      array('with.dots.is.not.okay'),
      array('with-Otherä-letters'),
      array('with-/-letters')
    );
  }
}
