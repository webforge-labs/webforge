<?php

namespace Webforge\Framework\Package;

use Webforge\Common\System\Dir;
use Webforge\Common\System\File;
use Webforge\Setup\AutoLoadInfo;
use Webforge\Framework\Inflector;

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
  const BIN = 'bin';
  const CACHE = 'cache';
  const TEST_FILES = 'test-files';
  
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
   * @return Webforge\Setup\AutoLoadInfo
   */
  public function getAutoLoadInfo();

  /**
   * @param Webforge\Setup\AutoLoadInfo $info
   */
  public function setAutoLoadInfo(AutoLoadInfo $info);
  
  /**
   * Returns the (main-)Namespace of the package
   *
   * @return string
   */
  public function getNamespace(Inflector $inflector = NULL);

  /**
   * Returns the directory of the (main-)Namespace of the package
   *
   * for example: if the namespace is "Webforge" this is package-root/lib/Webforge
   * @return Webforge\Common\System\Dir
   */
  public function getNamespaceDirectory();
  
}