<?php

namespace Webforge\CMS\Navigation;

class NestedSetConverter extends \Psc\SimpleObject {

  /**
   * Converts a NestedSet flat-Array into a nested HTML-List (ul, li)
   *
   * @param Webforge\CMS\Navigation\Node[] $tree
   * @return string
   */
  public function toHTMLList(Array $tree) {
    $depth = -1;
    $html = '';
    while (!empty($tree)) {
      $node = array_shift($tree);

      // Level down? (the node is in a new list of children)
      if ($node->getDepth() > $depth) {
        $html .= '<ul>';
      
      // Level up? (the node is on an upper level after a list of childen)
      } elseif ($node->getDepth() < $depth) {
        $html .= str_repeat("</li></ul>", $depth - $node->getDepth());
        $html .= '</li>';
      
      // same Level
      } else {
        $html .= '</li>'; // close sibling from before
      }
      
      // add the new node
      $html .= '<li>'.$node->getNodeHTML();
      
      $depth = $node->getDepth();
    }
    
    // close from last iteration
    $html .= str_repeat('</li></ul>', $depth + 1);
    return $html;
  }
  
  /**
   * Convert from parentPointerArray (every node should have a parent key) to the flat NestedSet array
   *
   * Every reference to its parent in $ppTree must be already defined correctly (getParent() must return NULL for no parent or object<Node>)
   * @param array Webforge\CMS\Navigation\Node[] $ppTree
   * @return array a nested Set flat-array, in the same form as the parameter for toHTMLList
   */
  public function fromParentPointer(Array $ppTree) {
    $tree = array();
    
    if (count($ppTree) === 0) return $tree;
    
    /*
     * We use a very intuitive algorithm here:
     *
     * for every node in the set, we try to find out, how our movement was from the
     * $prevNode to $node
     *
     * $prevNode and $node can be on different levels, siblings, (or the same? one node?)
     *
     * There can be 3 cases (1 trivial):
     * downwards: The $prevNode is $node's parent. So were moving into the subtree from $prevNode. This can only be a 1-step downward
     * sidewards: $prevNode is a sibling from $node
     * upwards:   the $prevNode is on another level than $node. This can be more than one or one step upward
     *
     * Runs in O(N*log[2](N)), non recursive
     */
    $cnt = 0;
    $depth = 0;
    $prevNode = NULL;
    foreach ($ppTree as $node) {
      // first root node?
      if (!isset($prevNode)) {
        $node->setLft(++$cnt);
        $node->setDepth($depth = 0);
      
      // downwards?
      } elseif ($prevNode->equalsNode($node->getParent())) {
        $node->setLft(++$cnt);
        $node->setDepth(++$depth);
        
      // sidwards?
      } elseif ($node->getParent() === NULL && $prevNode->getParent() === NULL ||  // both are a root node
                $node->getParent() !== NULL && $node->getParent()->equalsNode($prevNode->getParent())) {
        $prevNode->setRgt(++$cnt);
        $node->setLft(++$cnt);
        $node->setDepth($depth);
      
      // upwards? notice: this can be more than one step and: has to be checked after downwards
      } elseif($node->getParent() !== NULL && !$node->getParent()->equalsNode($prevNode->getParent())) {
        $prevNode->setRgt(++$cnt); // set right for last child
        
        // move up the path until we got to our parent (dont number that) and give right values for parent nodes on its way
        // we dont number our parent, because we dont know if we have other siblings on that level
        $walkupNode = $prevNode;
        while ($walkupNode->getParent() !== NULL && !$walkupNode->getParent()->equalsNode($node->getParent())) {
          // normally it would never hit root, because root is traversed only at the last iteration
          $walkupNode = $walkupNode->getParent();
          $walkupNode->setRgt(++$cnt);
          $depth--;
        }
        
        $node->setLft(++$cnt);
        $node->setDepth($depth);
        
      // upwards to new root?
      } elseif($node->getParent() === NULL && $prevNode->getParent() !== NULL) {
        // we are a root node, and the prevNode is in a root-tree before this tree
        
        $walkupNode = $prevNode;
        while($walkupNode->getParent() !== NULL) {
          $walkupNode->setRgt(++$cnt); // this is always the last child from an layer
          $walkupNode = $walkupNode->getParent();
        }
        $walkupNode->setRgt(++$cnt); // we number the root-sibling to us here, because there are no new nodes in that root
        
        $node->setDepth($depth = 0);
        $node->setLft(++$cnt); // our own numbering
      }
      
      
      $tree[] = $node;
      $prevNode = $node;
    }
    
    /* last iteration:
     *
     * $node === $prevNode and it can not have a rgt value, yet
     *
     * for the last node its only possible to have a movement upwards until root (it cannot be the root itself)
     */
    
    // move upwards until root and number (everyone)
    $walkupNode = $prevNode;
    do {
      $walkupNode->setRgt(++$cnt);
      $walkupNode = $walkupNode->getParent();
    } while ($walkupNode !== NULL);
    
    return $tree;
  }
  
  /**
   * Converts a NestedSet flat-Array into a human readable text format
   *
   * @param Webforge\CMS\Navigation\Node[] $tree
   * @return string
   */
  public function toString(Array $tree) {
    $text = '';
    while (!empty($tree)) {
      $node = array_shift($tree);

      $text .= str_repeat('  ',$node->getDepth()).(string) $node."\n";
    }
    
    return $text;
  }
}
?>