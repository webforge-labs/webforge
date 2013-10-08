<?php

namespace Webforge\Framework;

interface Project {

  // meta
  public function getName();

  /**
   * Gets a slug lower name for the project
   * 
   * this is not a title and is alphanumeric with - 
   * 
   * e.g:
   * SerienLoader
   * becomes
   * serien-loader
   */
  public function getLowerName();

  /**
   * @return Webforge\Setup\Configuraton
   */
  public function getConfiguration();


  // i18n
  /**
   * @return array
   */
  public function getLanguages();

  /**
   * @return strring
   */
  public function getDefaultLanguage();

  /**
   * @return Webforge\Common\System\Dir
   */
  public function getRootDirectory();

  /**
   * Returns a specific directory for the project
   * 
   * @param string $directoryIdentifier might be something like "test-files" or "resources" or "templates", "cache", whatever your project supports
   * @return Webforge\Common\System\Dir
   */
  public function dir($identifier);

  // project status and env
  /**
   * Returns the slug-name of the host the project is running on
   * 
   * @return string
   */
  public function getHost();

  /**
   * @return bool
   */
  public function isStaging();

  /**
   * @return bool
   */
  public function isDevelopment();

  /**
   * @return string
   */
  public function getStatus();
}
