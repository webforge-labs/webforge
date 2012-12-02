<?php

namespace Webforge\Setup\Package;

use Psc\System\Dir;
use Psc\System\File;
use Webforge\Setup\AutoLoadInfo;

/**
 * Package
 * 
 * see UB-Language for a Definition
 *
 */
interface Package {
  
  const ROOT = 'root';
  const TESTS = 'tests';
  
  /**
   * A nicename for the package
   *
   * @return string
   */
  public function getSlug();
  
  /**
   * The full title for the package
   *
   * @return string
   */
  public function getTitle();
  
  /**
   * @param string $slug
   */
  public function setSlug($slug);
  
  /**
   * @return Psc\System\File
   */
  public function getRootDirectory();
  
  /**
   * @param Dir $directory
   */
  public function setRootDirectory(Dir $directory);
  
  /**
   * @return Dir
   */
  public function getDirectory($type = self::ROOT);

  
  /**
   * Gives Information for the Paths the Projects loads it classes from
   * @return Psc\Setup\AutoLoadInfo
   */
  public function getAutoLoadInfo();

  /**
   * @param Psc\Setup\AutoLoadInfo $info
   */
  public function setAutoLoadInfo(AutoLoadInfo $info);
  
}
?>