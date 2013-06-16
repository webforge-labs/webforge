<?php

namespace Webforge\CMS;

use Webforge\Common\Session;
use Psc\Session\Session as SessionImplementation;
use Psc\PHP\CookieManager;

class EnvironmentContainer {

  protected $session;
  protected $cookieManager;

  protected $options = array();

  public function __construct(Array $options = array()) {
    $this->options = array_replace(
      array(
        'cookies.domain' => @$_SERVER['HTTP_HOST'],
        'session.init' => TRUE
      ),
      $options
    );
  }

  public function getSession() {
    if (!isset($this->session)) {
      $this->session = new SessionImplementation();

      if ($this->getOption('session.init')) {
        $this->session->init();
      }
    }

    return $this->session;
  }

  public function setSession(Session $session) {
    $this->session = $session;
    return $this;
  }

  public function getCookieManager() {
    if (!isset($this->cookieManager)) {
      $this->cookieManager = new CookieManager();
      $this->cookieManager->setDomain($this->getOption('cookies.domain'));

    }
    return $this->cookieManager;
  }

  public function setCookieManager(CookieManager $cookieManager) {
    $this->cookieManager = $cookieManager;
    return $this;
  }

  public function getOption($key) {
    if (array_key_exists($key, $this->options)) {
      return $this->options[$key];
    }

    return NULL;
  }

  public function setOption($key, $value) {
    $this->options[$key] = $value;
  }
}
