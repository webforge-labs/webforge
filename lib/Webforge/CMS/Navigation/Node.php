<?php

namespace Webforge\CMS\Navigation;

interface Node {
  
  public function equalsNode(Node $other = NULL);
  
  /**
   * Returns the Label or identifier of the node as HTML
   * 
   * @return string
   */
  public function getNodeHTML();
  
  public function getDepth();
  
  public function setDepth($depth);
  
  public function getRgt();
  
  public function setRgt($right);
  
  public function getLft();
  
  public function setLft($left);

  //public function setChildren($children);

  //public function setParent(Node $parent = NULL);
}
