<?php

namespace Webforge\Setup;

use Psc\DataInput;
use Psc\DataInputException;

/**
 *
 * PscCMSBridge relies that Psc\CMS\Configuration is parent from this class!
 */
class Configuration extends \Psc\CMS\Configuration {
  
  /**
   * @param $values $keys can be any arbitrary name with . for namespaces in it the values can be mixed but mainly scalar should be used
   */
  public function __construct(Array $values) {
    parent::__construct($conf = array());
    
    foreach ($values as $key=>$value) {
      if (mb_strpos($key,'.')) {
        $key = explode('.', $key);
      } else {
        $key = array($key);
      }
      
      $this->set($key, $value);  // translate dot paths correctly through constructor
    }
  }
  
  public function req($keys, $default = NULL) {
    try {
      return $this->conf->get($keys, DataInput::THROW_EXCEPTION, $default);
    } catch (DataInputException $e) {
      throw MissingConfigVariableException::fromKeys($e->keys);
    }
  }
}
?>