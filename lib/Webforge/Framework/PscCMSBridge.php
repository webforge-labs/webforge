<?php

namespace Webforge\Framework;

use Webforge\Setup\Package\Package;
use Psc\CMS\Project;
use Psc\PSC;

class PscCMSBridge {
  
  /**
   * @return Psc\CMS\Project
   */
  public function createProjectFromPackage(Package $package) {
    $paths = array();

    $paths[PSC::PATH_SRC] = './lib/';
    $paths[PSC::PATH_HTDOCS] = './www/';
    $paths[PSC::PATH_BASE] = './';
    $paths[PSC::PATH_CACHE] = './files/cache/';
    $paths[PSC::PATH_BIN] = './bin/';
    $paths[PSC::PATH_TPL] = './resources/tpl/';
    $paths[PSC::PATH_TESTDATA] = './tests/files/';
    $paths[PSC::PATH_TESTS] = './tests';
    $paths[PSC::PATH_CLASS] = './base/src/'.$package->getSlug().'/';
    $paths[PSC::PATH_FILES] = './files/';
    $paths[PSC::PATH_BUILD] = './build/';
    
    $project = new Project(
      $package->getSlug(),
      $package->getRootDirectory(),
      $hostConfig = new \Webforge\Setup\Configuration(array()),
      $paths,
      $mode = Project::MODE_SRC,
      $staging = FALSE
    );
    
    return $project;
  }
}
?>