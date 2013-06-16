<?php

namespace Webforge\Framework\Package;

use Webforge\Common\System\Dir;
use Webforge\Common\JS\JSONConverter;
use Webforge\Setup\AutoLoadInfo;
use Webforge\Common\Exception\MessageException;

class ComposerPackageReader {
  
  /**
   * @return Webforge\Framework\Package\Package
   */
  public function fromDirectory(Dir $directory) {
    try {
      $jsonFile = $this->findComposerJSON($directory);
      
      $converter = new JSONConverter();
      $json = $converter->parse($jsonFile->getContents());
      
      list($vendor, $slug) = explode('/', $json->name, 2);
      
      return new SimplePackage($slug, $vendor, $directory, $this->readAutoLoadInfo($json));
      
    } catch (MessageException $e) {
      $e->prependMessage(sprintf("Cannot read package from directory '%s' ", $directory));
      throw $e;
    }
  }
  
  protected function readAutoLoadInfo(\stdClass $json) {
    $definition = array();
    
    // i can do only psr-0, yet
    if (isset($json->autoload)) {
      $definition = $json->autoload;
    }
    
    return new AutoLoadInfo($definition);
  }
  
  protected function findComposerJSON(Dir $directory) {
    $file = $directory->getFile('composer.json');
    
    if (!$file->exists()) {
      throw new \Webforge\Common\Exception('composer.json cannot be found in');
    }
    
    return $file;
  }
}
