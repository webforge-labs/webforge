<?php

namespace Webforge\CMS\Navigation;

class NestedSetConverter extends \Psc\SimpleObject {

  /**
   * Convertes a NestedSet flat-Array into a nested HTML-List (ul, li)
   * 
   * @return string
   */
  public function toHTMLList(Array $tree) {
    $depth = -1; 
    $html = '';
    while (!empty($tree)) {
      $node = array_shift($tree);

      // Level down? (the node is in a new list of children)
      if ($node['depth'] > $depth) {
        $html .= '<ul>';
      
      // Level up? (the node is on an upper level after a list of childen)
      } elseif ($node['depth'] < $depth) {
        $html .= str_repeat("</li></ul>", $depth - $node['depth']);
        $html .= '</li>';
      
      // same Level
      } else {
        $html .= '</li>'; // close sibling from before
      }
      
      // add the new node
      $html .= '<li><a>'.$node['title'].'</a>';
      
      $depth = $node['depth'];
    }
    
    // close from last iteration
    $html .= str_repeat('</li></ul>', $depth + 1);
    return $html;
  }
  
  /**
   * Convert from parentPointerArray (every node should have a parent key) to the flat NestedSet array
   * 
   * @return array ein nested Set flat-array, so wie er parameter für toHTMLList ist
   */
  public function fromParentPointer(Array $ppTree) {
    $tree = array();
    
    $nodes = array();
    // cast objects to id (to have object references, not arrays without references), and index with title
    foreach ($ppTree as $key=>$node) {
      $node = (object) $node;
      $nodes[$node->title] = $node;
      $ppTree[$key] = $node;
    }

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
    $prevNode = NULL;
    foreach ($ppTree as $node) {
      $node->parent = $node->parent !== NULL ? $nodes[$node->parent] : NULL;
      
      // root?
      if (!isset($prevNode)) {
        $node->lft = ++$cnt;
      
      // downwards?
      } elseif ($node->parent->title === $prevNode->title) {
        $node->lft = ++$cnt;
        
      // sidwards?
      } elseif ($node->parent->title === $prevNode->parent->title) {
        $prevNode->rgt = ++$cnt;
        $node->lft = ++$cnt;
      
      // upwards? notice: this can be more than one step and: has to be checked after downwards
      } elseif($node->parent->title !== $prevNode->parent->title) {
        $prevNode->rgt = ++$cnt; // set right for last sibling
        
        // move up the path until we got to our parent (dont number that) and give right values for parent nodes on its way
        // we dont number our parent, because we dont know if we have other siblings on that level
        $walkupNode = $prevNode;
        while ($walkupNode->parent !== NULL && $walkupNode->parent->title !== $node->parent->title) {
          // normally it would never hit root, because root is traversed only at the last iteration
          $walkupNode = $walkupNode->parent;
          $walkupNode->rgt = ++$cnt;
        }
        
        $node->lft = ++$cnt;
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
      $walkupNode->rgt = ++$cnt;
      $walkupNode = $walkupNode->parent;
    } while ($walkupNode !== NULL);
    
    // convert back ...
    foreach ($tree as $key=>$node) {
      $tree[$key] = array(
        'title'=>$node->title,
        'lft'=>$node->lft,
        'rgt'=>isset($node->rgt) ? $node->rgt : NULL // this is of course not allowed, but otherwise tests wont fail        
      );
      
      if (isset($node->depth)) {
        $tree[$key]['depth'] = $node->depth;
      }
    }
    
    return $tree;
  }
}
?>