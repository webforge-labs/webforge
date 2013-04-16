<?php

namespace Webforge\CMS\Navigation;

use Doctrine\ORM\EntityManager;
use Closure;

/**
 * The doctrine bridge helps to adjust the nestedset values a set of gathered navigation nodes
 *
 * to have a more exlicit and "not so magic" interface we use a transaction metapher for gathering the nodes
 *
 * basically its:
 *
 * $bridge = new DoctrineBridge(EntityManager $em)
 * $bridge->beginTransaction();
 *
 * $bridge->persist($node1);
 * $bridge->persist($node2);
 * $bridge->persist($node3);
 *
 * (...)
 *
 * $bridge->commit();
 * $em->flush();
 *
 * the bridge applies all values to the persisted nodes before the EntityManager flushes
 */
class DoctrineBridge {
  
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

  /**
   * @var array
   */
  protected $callbacks = array();
  
  public function __construct(EntityManager $em, NestedSetConverter $converter = NULL) {
    $this->em = $em;
    $this->converter = $converter;
  }
  
  /**
   * Starts listening for persisted Nodes
   *
   * to be concise with doctrine this is beginTransaction not startTransaction
   * this does not start a transaction on the entityManager!
   */
  public function beginTransaction() {
    $this->trxLevel++;
    $this->nodes = array();
    return $this;
  }
  
  /**
   * Stopps listening for persisted Nodes and updates the gathered ones
   *
   * this does not commit a transaction on the entityManager!
   */
  public function commit() {
    if ($this->trxLevel > 0) {
      $this->getConverter()->fromParentPointer($this->nodes);
      if (isset($this->callbacks['commitRootNode'])) {
        $this->callbacks['commitRootNode']($this->nodes[0]);
      }

      $this->trxLevel--;
    }
    return $this;
  }

  /**
   * @param Closure $callback function($rootNode)
   */
  public function onCommitRootNode(Closure $callback) {
    $this->callbacks['commitRootNode'] = $callback;
    return $this;
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
   * Adds a node and persists it in the EntityManager
   *
   * @chainable
   */
  public function persist(Node $node) {
    $this->em->persist($node);
    $this->addNode($node);
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