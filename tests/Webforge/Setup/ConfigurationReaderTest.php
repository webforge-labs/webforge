<?php

namespace Webforge\Setup;

use Webforge\Common\System\File;
use Webforge\Framework\Package\SimplePackage;

class ConfigurationReaderTest extends \Webforge\Code\Test\Base {

  protected $reader;
  protected $file;
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Setup\\ConfigurationReader';
    parent::setUp();

    $this->reader = new $this->chainClass;

    $this->package = new SimplePackage('superblog', 'acme', $this->getTestDirectory()->sub('packages/ACME/'));
    $this->file = $this->getFile('config.php');
  }

  public function testSimpleReadingFromValuesAcceptance() {
    $this->reader->setScope(array(
      'package'=>$this->package
    ));

    $configuration = $this->reader->fromPHPFile($this->file);

    $this->assertInstanceOf('Webforge\Setup\Configuration', $configuration);

    $this->assertEquals('ACME SuperBlog', $configuration->get('project.title'));
    $this->assertEquals('superblog', $configuration->get('db.default.user'));
    $this->assertEquals('superblog', $configuration->get('db.default.database'));

    $this->assertEquals('superblog', $configuration->get('db.tests.user'));
    $this->assertEquals('superblog_tests', $configuration->get('db.tests.database'));
  }

  public function testCannotReadEmptyFile() {
    $this->setExpectedException('Webforge\Setup\ConfigurationReadingException');

    $this->reader->fromPHPFile($this->getFile('empty.php'));
  }

  public function testReadFromEmptyArray() {
    $this->assertInstanceOf('Webforge\Setup\Configuration', $this->reader->fromArray(array()));
  }
}
