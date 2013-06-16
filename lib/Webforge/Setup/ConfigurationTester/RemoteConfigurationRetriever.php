<?php

namespace Webforge\Setup\ConfigurationTester;

use Psc\URL\RequestDispatcher;
use Webforge\Common\JS\JSONConverter;
use Webforge\Common\JS\JSONParsingException;

/**
 * 
 * @TODO refactor to use guzzle instead of RequestDispatcher
 * (add Guzzle RequestDispatcher)
 */
class RemoteConfigurationRetriever extends \Webforge\Common\BaseObject implements ConfigurationRetriever {
  
  /**
   * @var Webforge\URL\RequestDispatcher
   */
  protected $dispatcher;
  
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
  public function __construct($url, RequestDispatcher $dispatcher = NULL, JSONConverter $jsonConverter = NULL) {
    $this->url = $url;
    $this->dispatcher = $dispatcher ?: new RequestDispatcher();
    $this->jsonConverter = $jsonConverter ?: new JSONConverter();
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
    $response = $this->dispatcher->dispatch(
      $this->dispatcher->createRequest('GET', $this->url)
    );
    
    try {
      $json = $this->jsonConverter->parse($response->getRaw());
    } catch (JSONParsingException $e) {
      throw new \RuntimeException(
        sprintf("RemoteConfigurationRetrieving failed. The returned JSON from URL %s was not valid:\n%s\n",
                $this->url, $response->getRaw()),
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
    $this->getDispatcher()->setAuthentication($user, $password);
    return $this;
  }
  
  /**
   * @return Psc\URL\RequestDispatcher
   */
  public function getDispatcher() {
    return $this->dispatcher;
  }
  
  public function __toString() {
    return 'Remote Configuration ('.$this->url.')';
  }
}
?>