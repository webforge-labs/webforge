<?php

namespace Webforge\Setup;

use stdClass;
use Webforge\Common\String as S;
use Webforge\Common\System\Dir;
use Webforge\Code\Generator\GClass;
use RuntimeException;

/**
 * This class encapsulates types of autoloads (mainly psr-0)
 *
 * see Composer for the format (here in JSON)
 * 
 *  {"psr-0": {"Webforge": ["lib/", "tests/"]}}
 *
 * Trailingslashs are optional
 * 
 * means:
 *   loaded with psr-0 standard for the prefix Webforge in $libDirectory->sub('lib/') and $libDirectory->sub('tests/')
 */
class AutoLoadInfo {
  
  protected $prefixes = array();
  
  /**
   * @param Traversable $definition
   */
  public function __construct($definition = array()) {
    $this->parseDefinition((array) $definition);
  }
  
  protected function parseDefinition(array $definition) {
    if (isset($definition['psr-0'])) {
      $normalizePath = function ($path) {
        $path = S::expand($path, '/', S::END);
        if (Dir::isAbsolutePath($path)) {
          return new Dir($path);
        } else {
          return $path;
        }
      };
        
      foreach ((array) $definition['psr-0'] as $prefix => $paths) {
        $paths = array_map($normalizePath, (array) $paths);
        $prefix = trim($prefix, '\\');
        
        if (!isset($this->prefixes[$prefix])) {
          $this->prefixes[$prefix] = $paths;
        } else {
          $this->prefixes[$prefix] = array_merge(
                                      $this->prefixes[$prefix],
                                      $paths
                                    );
        }
      }
    }
  }
  
  /**
   * @return array string $prefix => array $paths
   */
  public function getPrefixes() {
    return $this->prefixes;
  }
  
  /**
   * Returns the first to find prefix and path in autoloadInfo
   *
   * normally this is the package main Prefix and the main package-library-path
   * @return list(string $prefix, $dir)
   * @throws NoAutoLoadPrefixException if autoload info is not defined
   */
  public function getMainPrefixAndPath(Dir $rootDir) {
    $prefixesPaths = $this->getPrefixes();
    $prefixes = array_keys($prefixesPaths);
    
    if (count($prefixes) > 0) {
      $firstPrefixPaths = array_shift($prefixesPaths);

      $path = array_shift($firstPrefixPaths);
      $dir = $path instanceof Dir ? $path : $rootDir->sub($path);
    
      return array(array_shift($prefixes), $dir);
    }
    
    throw new NoAutoLoadPrefixException('Cannot retrieve the main Prefix and Library Path from AutoLoadInfo. AutoLoadInfo is defined: '.print_r($prefixesPaths, true)."\nPlease insert autoload informations into your composer.json");
  }

  
  /**
   * @param Dir $rootDir die relativen Pfade (wenn es welche gibt) werden auf dieses Verzeichnis bezogen
   * @return Files[]|array()
   */
  public function getFiles($fqn, Dir $rootDir) {
    $gClass = new GClass($fqn);
    $fqn = $gClass->getFQN();
    
    $files = array();
    foreach ($this->prefixes as $prefix => $paths) {
      if (mb_strpos($fqn, $prefix) === 0) {
        foreach ($paths as $path) {
          $dir = $path instanceof Dir ? $path : $rootDir->sub($path);
        
          $files[] = $dir->sub(str_replace('\\', '/', $gClass->getNamespace()).'/')->getFile($gClass->getName().'.php');
        }
      }
    }
    
    return $files;
  }
}
?>