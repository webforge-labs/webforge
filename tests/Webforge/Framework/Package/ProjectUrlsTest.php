<?php

namespace Webforge\Framework\Package;

use Webforge\Configuration\Configuration;

class ProjectUrlsTest extends \Webforge\Framework\Package\PackagesTestCase {
  
  public function setUp() {
    $this->chainClass = 'Webforge\\Framework\\ProjectURLs';
    parent::setUp();

    $this->hostConfig = new Configuration(array());

    $container = $this->injectRegistry();
    $container->setHostConfiguration($this->hostConfig);

    $this->projectPackage = $container->getProjectsFactory()->fromPackage($this->configPackage);
    //$this->projectPackage = new ProjectPackage($this->configPackage, 'ACMESuperBlog', 'super-blog', 0, 'psc', $this->urls);
  }

  public function testThatgetURLMightThrowExceptionForEmptyHostConfiguration() {
    $this->setExpectedException('Webforge\Common\Exception');
    $this->projectPackage->getHostURL('base');
  }

  public function testThatTheProjectReturnsURLsWhenConfigParameterForProjectIsset() {
    $givenUrl = 'webforge.ps-webforge.net';

    $this->projectPackage->getConfiguration()->set(array('url', 'base'), $givenUrl);

    $this->assertEquals(
      'http://'.$givenUrl.'/',
      (string) $this->projectPackage->getHostURL('base')
    );
  }

  public function testThatTheProjectReturnsCMSURLsWhenConfigParameterForProjectIsset() {
    $givenUrl = 'webforge.ps-webforge.net';

    $this->projectPackage->getConfiguration()->set(array('url', 'base'), $givenUrl);

    $this->assertEquals(
      'http://cms.'.$givenUrl.'/',
      (string) $this->projectPackage->getHostURL('cms')
    );
  }
}
