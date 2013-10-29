<?php

namespace Webforge\Framework\Package;

use Webforge\Common\System\Dir;
use Webforge\Common\JS\JSONConverter;
use Webforge\Setup\AutoLoadInfo;
use Webforge\Common\Exception\MessageException;
use Webforge\Framework\DirectoryLocations;

class ComposerPackageReader {
  
  /**
   * @return Webforge\Framework\Package\Package
   */
  public function fromDirectory(Dir $directory) {
    try {
      $jsonFile = $this->findComposerJSON($directory);
      
      $converter = new JSONConverter();
      $json = $converter->parseFile($jsonFile);
      
      list($vendor, $slug) = explode('/', $json->name, 2);
      
      $package = new SimplePackage($slug, $vendor, $directory, $this->readAutoLoadInfo($json));
      $this->readDirectoryLocations($json, $package);

      return $package;
      
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

  protected function readDirectoryLocations(\stdClass $json, Package $package) {
    if (isset($json->extra) && isset($json->extra->{'directory-locations'})) {
      foreach ((array) $json->extra->{'directory-locations'} as $alias => $location) {
        $package->defineDirectory($alias, $location);
      }
    }
  }
  
  protected function findComposerJSON(Dir $directory) {
    $file = $directory->getFile('composer.json');
    
    if (!$file->exists()) {
      throw new \Webforge\Common\Exception('composer.json cannot be found in');
    }
    
    return $file;
  }
}
