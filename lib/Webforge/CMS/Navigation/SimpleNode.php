<?php

namespace Webforge\CMS\Navigation;

/**
 * A simple Implementation of the node interface, mostly used for tests
 * 
 */
class SimpleNode implements Node {
  
  protected $lft, $rgt, $depth, $title, $parent;

  protected $children;
  
  public function __construct(Array $node) {
    $this->title = $node['title'];
    
    if (isset($node['depth']))
      $this->depth = $node['depth'];
    
    if (isset($node['lft']))
      $this->lft = $node['lft'];
      
    if (isset($node['rgt']))
      $this->rgt = $node['rgt'];
      
    if (isset($node['parent'])) {
      $this->parent = $node['parent'];
    }
  }
  
  public function __toString() {
    return $this->title;
  }
  
  public function unwrap($type) {
    if ($type === 'parentPointer') {
      return array (
        'title' => $this->title,
        'parent' => isset($this->parent) ? $this->parent->title : NULL,
        'depth' => $this->depth
        );
    } else {
      return array (
        'title' => $this->title,
        'rgt' => $this->rgt,
        'lft' => $this->lft,
        'depth' => $this->depth
        );
    }
  }
  
  public function getNodeHTML() {
    return '<a>'.$this->title.'</a>';
  }
  
  public function equalsNode(Node $other = NULL) {
    return isset($other) && $other->getTitle() === $this->getTitle();
  }
  
  // @codeCoverageIgnoreStart
  
  /**
   * @param TestNode $parent
   * @chainable
   */
  public function setParent(SimpleNode $parent = NULL) {
    $this->parent = $parent;
    return $this;
  }

  /**
   * @return TestNode
   */
  public function getParent() {
    return $this->parent;
  }
  
  /**
   * @param string $title
   * @chainable
   */
  public function setTitle($title) {
    $this->title = $title;
    return $this;
  }

  /**
   * @return string
   */
  public function getTitle() {
    return $this->title;
  }
  
  /**
   * @param integer $depth
   * @chainable
   */
  public function setDepth($depth) {
    $this->depth = $depth;
    return $this;
  }

  /**
   * @return integer
   */
  public function getDepth() {
    return $this->depth;
  }


  /**
   * @param int $rgt
   * @chainable
   */
  public function setRgt($rgt) {
    $this->rgt = $rgt;
    return $this;
  }

  /**
   * @return int
   */
  public function getRgt() {
    return $this->rgt;
  }

  /**
   * @param int $lft
   * @chainable
   */
  public function setLft($lft) {
    $this->lft = $lft;
    return $this;
  }

  /**
   * @return int
   */
  public function getLft() {
    return $this->lft;
  }

  public function getChildren() {
    return $this->children;
  }

  public function setChildren($children) {
    $this->children = $children;
  }

  // @codeCoverageIgnoreEnd
}
?>