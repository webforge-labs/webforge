<?php

namespace Webforge\Setup\ConfigurationTester;

use Webforge\Common\JS\JSONConverter;
use Webforge\Common\JS\JSONParsingException;
use Guzzle\Http\Client as GuzzleClient;

/**
 */
class RemoteConfigurationRetriever implements ConfigurationRetriever {
  
  /**
   * @var Guzzle\Http\Client
   */
  protected $client;
  
  /**
   * @var array
   */
  protected $inis;
  
  /**
   * @var string
   */
  protected $url;
  
  /**
   * @var Webforge\JS\JSONConverter
   */
  protected $jsonConverter;
  
  /**
   * The URL to the php dump-script
   *
   * the script shoud run the following code:
   * <?php
   * print json_encode(ini_get_all());
   * ?>
   */
  public function __construct($url, GuzzleClient $client, JSONConverter $jsonConverter = NULL) {
    $this->url = $url;
    $this->client = $client;
    $this->jsonConverter = $jsonConverter ?: new JSONConverter();
    $this->inis = NULL;
  }
  
  /**
   * 
   */
  public function retrieveIni($iniName) {
    if (!isset($this->inis)) {
      $this->retrieveInis();
    }
    
    return array_key_exists($iniName, $this->inis) ? $this->inis[$iniName] : NULL;
  }
  
  public function retrieveInis() {
    $request = $this->client->get($this->url);
    $jsonString = (string) $request->send()->getBody();

    try {
      $json = $this->jsonConverter->parse($jsonString);

    } catch (JSONParsingException $e) {
      throw new \RuntimeException(
        sprintf(
          "RemoteConfigurationRetrieving failed. The returned JSON from URL %s was not valid:\n%s\n",
          $this->url, $jsonString
        ),
        0,
        $e
      );
    }
    
    // handles true and not true second parameter from ini_get_all
    $this->inis = array();
    foreach ($json as $iniName => $iniInfo) {
      if (is_object($iniInfo)) {
        $this->inis[$iniName] = $iniInfo->local_value;
      } else {
        $this->inis[$iniName] = $iniInfo;
      }
    }

  }
  
  // @codeCoverageIgnoreStart
  /**
   * @return array
   */
  public function getInis() {
    return $this->inis;
  }
  
  /**
   * @return string
   */
  public function getUrl() {
    return $this->url;
  }
  // @codeCoverageIgnoreEnd
  
  /**
   * @param string $user
   * @param string $password
   */
  public function setAuthentication($user, $password) {
    $this->client->setDefaultOption('auth', array($user, $password));
    return $this;
  }
  
  public function __toString() {
    return 'Remote Configuration ('.$this->url.')';
  }
}
