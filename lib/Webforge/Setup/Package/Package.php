<?php

namespace Webforge\Setup\Package;

use Webforge\Common\System\Dir;
use Webforge\Common\System\File;
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
  const VENDOR = 'vendor';
  
  /**
   * A full identifier for the package
   *
   * @return string vendor/slug
   */
  public function getIdentifier();
  
  /**
   * A nicename for the package
   *
   * @return string its only the part packagename in vendor/packagename
   */
  public function getSlug();
  
  /**
   * @return string its only the part vendor in vendor/packagename
   */
  public function getVendor();
  
  
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
   * @return Webforge\Common\System\File
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