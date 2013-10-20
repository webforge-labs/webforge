<?php

namespace Webforge\Setup\ConfigurationTester;

use Webforge\Common\System\File;
use Webforge\Common\JS\JSONConverter;
use Guzzle\Http\Client as GuzzleClient;

/**
 * Reads the INI-Configuration out of a file and tests this values
 *
 * an example .json:
 *

{
  "post_max_size": ">= 20M",
  "upload_max_filesize": ">= 20M",
  
  "date.timezone": "Europe/Berlin",
  
  "error_reporting": "E_ALL | E_STRICT",
  
  "mbstring.internal_encoding": "UTF-8",

  "suhosin.log.syslog": true,
  "suhosin.log.syslog.facility": 9,
  "suhosin.log.syslog.priority": 1
}

 */
class ConfigurationFileTester {
  
  const REMOTE = 'remote';
  const LOCAL = 'local';
  
  /**
   * @var Webforge\Setup\ConfigurationTester\ConfigurationTester
   */
  protected $tester;
  
  /**
   * @var Webforge\Common\System\File
   */
  protected $jsonFile;

  /**
   * @var Webforge\Common\JS\JSONConverter
   */
  protected $converter;
  
  public function __construct(File $jsonFile, ConfigurationTester $tester = NULL, JSONConverter $converter = NULL) {
    $this->jsonFile = $jsonFile;
    $this->tester = $tester ?: new ConfigurationTester();
    $this->converter = $converter ?: new JSONConverter();
  }
  
  /**
   * @param string $type local remote
   */
  public static function create(File $jsonFile, $type = 'local', $remoteUrl = NULL) {
    if ($type === self::REMOTE) {
      if (!isset($remoteUrl)) {
        // @TOOD get global?
        throw new \InvalidArgumentException('if type is remote, it is expected to give the remoteUrl to create as #3 argument');
      }
      
      $retriever = new RemoteConfigurationRetriever($remoteUrl, new GuzzleClient());
    } else {
      $retriever = new LocalConfigurationRetriever();
    }
    
    $tester = new ConfigurationTester($retriever);
    
    return new static($jsonFile, $tester);
  }
  
  /**
   * @return Webforge\Setup\ConfigurationTester\ConfigurationTester
   */
  public function process() {
    $json = $this->converter->parse($this->jsonFile->getContents());
    
    $this->tester->INIs((array) $json);
    return $this->tester;
  }
  
  /**
   * Sets the Credentials for Authentication when a remote ConfigurationRetriever is used
   */
  public function setAuthentication($user, $password) {
    if ($this->tester->getRetriever() instanceof RemoteConfigurationRetriever) {
      $this->tester->getRetriever()->setAuthentication($user, $password);
    }
    return $this;
  }
  
  public function getConfigurationTester() {
    return $this->tester;
  }
  
  // @codeCoverageIgnoreStart
  public function skipExtension($name) {
    $this->getConfigurationTester()->skipExtension($name);
    return $this;
  }
  // @codeCoverageIgnoreEnd
}
