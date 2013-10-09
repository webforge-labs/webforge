<?php

namespace Webforge\Framework;

use Webforge\Framework\Package\Package;
use Webforge\Common\System\Dir;
use InvalidArgumentException;

class DirectoryLocations {

  /**
   * @var Webforge\Common\System\Dir
   */
  protected $root;

  /**
   * @var array
   */
  protected $locations;

  /**
   * @param array $locations key: identifier, value: the path with / slashes and / at the end relative to $root
   */
  public function __construct(Dir $root, Array $locations) {
    $this->root = $root;
    $this->locations = $locations;
    $this->locations['root'] = '/';
  }

  public function setRoot(Dir $root) {
    // remember to refresh cache, when cached
    $this->root = $root;
    return $this;
  }

  public static function createFromPackage(Package $package) {
    return new static(
      $package->getRootDirectory(),
      array(
        'test-files'=>'tests/files/',
        'cache'=>'files/cache/',
        'bin'=>'bin/',
        'etc'=>'etc/',
        'lib'=>'lib/',
        'vendor'=>'vendor/',
        'tests'=>'tests/',
        'cms-uploads'=>'files/uploads/',
        'cms-images'=>'files/images/',
        'cms-tpl'=>'resources/tpl/' // dont use this anymore
      )
    );
  }

  public function get($identifier) {
    if (array_key_exists($identifier, $this->locations)) {
      return $this->root->sub($this->locations[$identifier]);
    }

    throw new InvalidArgumentException(sprintf("The identifier '%s' for a directory location is not known. Avaible are: %s", $identifier, implode(', ', array_keys($this->locations))));
  }
}
