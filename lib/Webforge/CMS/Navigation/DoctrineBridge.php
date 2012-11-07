<?php

namespace Webforge\CMS\Navigation;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber AS DoctrineEventSubscriber;
use Doctrine\ORM\EntityManager;

/**
 * The doctrine bridge helps to adjust the nestedset values a set of gathered navigation nodes
 *
 * the nodes are gathered while persisting into the EntityManager
 * to have a more exlicit and "not so magic" interface we use a transaction metapher for gathering the nodes
 *
 * basically its:
 *
 * $bridge = new DoctrineBridge(EntityManager $em)
 * $bridge->startTransaction();
 *
 * $em->persist($node1);
 * $em->persist($node2);
 * $em->persist($node3);
 *
 * (...)
 *
 * $bridge->commit();
 * $em->flush();
 *
 * the bridge applies all values to the persisted nodes before the EntityManager flushes
 */
class DoctrineBridge implements DoctrineEventSubscriber {
  
  /**
   * @var Doctrine\ORM\EntityManager
   */
  protected $em;
  
  /**
   * The current set of gathered nodes
   */
  protected $nodes;
  
  /**
   * @var int
   */
  protected $trxLevel = 0;
  
  /**
   * @var Webforge\CMS\Navigation\NestedSetConverter
   */
  protected $converter;
  
  public function __construct(EntityManager $em, NestedSetConverter $converter = NULL) {
    $this->em = $em;
    $this->converter = $converter;
  }
  
  /**
   * deprecated: use beginTransaction
   */
  public function startTransaction() {
    return $this->beginTransaction();
  }
  
  /**
   * Starts listening for persisted Nodes
   *
   * to be concise with doctrine this is beginTransaction not startTransaction
   */
  public function beginTransaction() {
    $this->em->getEventManager()->addEventSubscriber($this);
    $this->trxLevel++;
    $this->nodes = array();
    return $this;
  }
  
  /**
   * Stopps listening for persisted Nodes and updates the gathered ones
   */
  public function commit() {
    if ($this->trxLevel > 0) {
      $this->getConverter()->fromParentPointer($this->nodes);
      $this->trxLevel--;
    }
    return $this;
  }
  
  /**
   */
  public function getSubscribedEvents() {
    return array('prePersist');
  }

  /**
   * Is called from doctrine on persist
   *
   * @param LifecycleEventArgs $args
   */
  public function prePersist(LifecycleEventArgs $args) {
    if ($this->trxLevel > 0) {
      $node = $args->getEntity();
      if ($node instanceof Node) {
        $this->addNode($node);
      }
    }
  }
  
  /**
   * Adds a single node to the current transaction
   *
   * if not transaction is active nothing is happening
   * @chainable
   */
  public function addNode(Node $node) {
    if ($this->trxLevel > 0) {
      $this->nodes[] = $node;
      
    }
    return $this;
  }
  
  /**
   * @return array
   */
  public function getNodes() {
    return $this->nodes;
  }
  
  /**
   * @return Webforge\CMS\Navigation\NestedSetConverter
   */
  public function getConverter() {
    if (!isset($this->converter)) {
      $this->converter = new NestedSetConverter();
    }
    
    return $this->converter;
  }
  
  /**
   * @param Webforge\CMS\Navigation\NestedSetConverter $converter
   */
  public function setConverter(NestedSetConverter $converter) {
    $this->converter = $converter;
    return $this;
  }
}
?>