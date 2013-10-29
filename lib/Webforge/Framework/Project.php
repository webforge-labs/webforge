<?php

namespace Webforge\Framework;

interface Project {

  // meta
  public function getName();

  /**
   * Returns the (main) Autoload Namespace for the Project
   * 
   * should be in 90% the getName()
   */
  public function getNamespace();

  /**
   * Returns the directory of the (main-)Namespace of the package
   *
   * for example: if the namespace is "Webforge" this is %root%/lib/Webforge
   * @return Webforge\Common\System\Dir
   */
  public function getNamespaceDirectory();

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

  /**
   * Should be called if configuration was changed and the object should sync the new values
   */
  public function configurationUpdate();

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

  /**
   * Set new Locations for dir() from array
   */
  public function updateDirectoryLocations(Array $locations);

  /**
   * Sets a semantic location for a directory
   * 
   * @param string $type a name for the location lowercase only dashes and a-z 0-9
   * @param string $location the path to the location from root (with trailing slash)
   * @chainable
   */
  public function defineDirectory($alias, $location);

  // project status and env
  /**
   * Returns the slug-name of the host the project is running on
   * 
   * @return string
   */
  public function getHost();

  /**
   * @return string
   */
  public function getHostUrl($type);

  /**
   * @return bool
   */
  public function isStaging();

  /**
   * @return bool
   */
  public function isBuilt();

  /**
   * @return bool
   */
  public function isDevelopment();

  /**
   * @return string
   */
  public function getStatus();

}
