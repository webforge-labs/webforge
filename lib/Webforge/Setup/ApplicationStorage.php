<?php

namespace Webforge\Setup;

use Webforge\Common\System\Dir;
use Webforge\Common\Preg;
use InvalidArgumentException;
use Webforge\Common\System\File;
use RuntimeException;

class ApplicationStorage {
  
  const PATTERN_NAME = '/^[a-zA-Z0-9-_]+$/';
  
  /**
   * @var Webforge\Common\System\Dir
   */
  protected $directory;
  
  /**
   * A nicename for the application storage
   *
   * @var string only file-safe characters (recently: [0-9A-Za-z-_] no . allowed)
   */
  protected $name;
  
  public function __construct($appName, Dir $directory = NULL) {
    $this->setName($appName);
    $this->directory = $directory;
  }
  
  protected function initDirectory() {
    $app = $this->getApplicationDirectory();
    
    
    // create if not exists
    $app->create();

    // Protect directory against web access
    $htaccess = $app->getFile('.htaccess');
    if ($htaccess->exists()) {
      $htaccess->writeContents('Deny from all');
    }
    
    return $app;
  }

  protected function getApplicationDirectory() {
    // prefer a environment variable for the specific app
    if ($appPath = getenv($this->getEnvName())) {
      return Dir::factoryTS($appPath);
    }

    $home = $this->getHomeDirectory();
    return $home->sub($this->getDirName().'/');
  }
  
  protected function getHomeDirectory() {
    // prefer the envrionment variable HOME for the home dir
    if (!$home = getenv('HOME')) {
      if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
        $home = getenv('APPDATA');
      }
    }
    
    // try to find home
    $home = Dir::factoryTS($home);
    
    if (!$home->exists()) {
      throw new RuntimeException(
        sprintf("Cannot find your existing storage Path ('%2\$s') in HOME for %1\$s.\n".
                "On Windows %%APPDATA%% should be existing.\n".
                "On Unix/Windows you can set \$HOME to your home path.\n".
                "If you dont want to set your homepath to HOME you can set %2\$s instead (will be created).",
                
                $home,
                $this->getEnvName()
               )
      );
    }
    
    return $home;
  }
  
  protected function getEnvName() {
    return mb_strtoupper($this->name);
  }
  
  protected function getDirName() {
    return sprintf('.%s', $this->name);
  }

  /**
   * @return string
   */
  public function getDirectory($subDir = '/') {
    if (!isset($this->directory)) {
      $this->directory = $this->initDirectory();
    }
    
    return $this->directory->sub($subDir);
  }
  
  /**
   * @return File
   */
  public function getFile($url) {
    return File::createFromURL($url, $this->getDirectory());
  }

  /**
   * Its not allowed to change the name of the app
   * 
   * @param string $name
   * @chainable
   */
  protected function setName($name) {
    if (!Preg::match($name, self::PATTERN_NAME)) {
      throw new InvalidArgumentException(sprintf("name can only be of '%s'. '%s' given", self::PATTERN_NAME, $name));
    }
    $this->name = $name;
    return $this;
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }
}
