<?php

namespace Webforge\Setup\Installer;

use Psc\Code\Event\Dispatcher as EventDispatcher;
use Psc\Code\Event\Manager as EventManager;
use Psc\Code\Event\Subscriber as EventSubscriber;

abstract class Command implements \Webforge\Common\Command, EventDispatcher {
  
  const IF_NOT_EXISTS = Installer::IF_NOT_EXISTS;
  
  protected function warn($msg) {
    $this->getManager()->dispatchEvent(
      self::WARNING,
      (object) array('msg'=>$msg),
      $this
    );
  }
  
  public function getManager() {
    if (!isset($this->manager)) {
      $this->manager = new EventManager();
    }
    
    return $this->manager;
  }
  
  public function subscribe(EventSubscriber $subscriber, $eventType = NULL) {
    $this->getManager()->bind(
      $subscriber,
      $eventType
    );
  }
}
?>