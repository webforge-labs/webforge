<?php

namespace Webforge\Setup;

use Mockery as m;
use Webforge\Configuration\ConfigurationReader;
use Webforge\Common\System\File;

class HostConfigurationReaderTest extends \Webforge\Code\Test\Base {

  protected $emptyHostConfigFile;
  
  public function setUp() {
    $this->chainClass = __NAMESPACE__ . '\\HostConfigurationReader';
    parent::setUp();

    $this->rollbacks = array(
      'PSC_CMS'=>getenv('PSC_CMS'),
      'WEBFORGE'=>getenv('WEBFORGE'),
    );

    putenv('PSC_CMS=');
    putenv('WEBFORGE=');

    $this->hostConfigPhpFile = $this->getPackageDir('etc/')->getFile('host-config.php.dist');

    $this->applicationStorage = m::mock('Webforge\Setup\ApplicationStorage');

    $this->reader = new HostConfigurationReader(new ConfigurationReader, $this->applicationStorage);
  }

  public function testPrev_NoEnvIsDefined() {
    $this->assertEmpty(getenv('PSC_CMS'));
    $this->assertEmpty(getenv('WEBFORGE'));
  }

  public function testReturnsADefaultConfigWithHostDefinedWhenNoEnvsAreSet() {
    $this->applicationStorage->shouldReceive('getFile')->with(m::any())->andReturn(new File('notdefined.txt'));

    $this->assertInstanceOf('Webforge\Configuration\Configuration', $config = $this->reader->read());
    $this->assertNotEmpty($config->get('host'), 'host should be at least defined');
  }

  public function testTHrowsExceptionIfExistingHostConfigHasNotHostDefined() {
    $this->emptyHostConfigFile = File::createTemporary()->writeContents('<?php $conf["something"] = "isdefined but not host"; ?>');

    $this->applicationStorage->shouldReceive('getFile')->with(m::any())->andReturn($this->emptyHostConfigFile);
    $this->assertInstanceOf('Webforge\Configuration\Configuration', $config = $this->reader->read());
  }

  public function tearDown() {
    foreach ($this->rollbacks as $env => $value) {
      putenv($env.'='.$value);
    }

    if ($this->emptyHostConfigFile) {
      $this->emptyHostConfigFile->delete();
    }
    parent::tearDown();
  }
}
