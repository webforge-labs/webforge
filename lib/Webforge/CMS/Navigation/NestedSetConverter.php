<?php

namespace Webforge\CMS\Navigation;

use Webforge\Common\ArrayUtil as A;

class NestedSetConverter {

  /**
   * Converts a NestedSet flat-Array into a nested HTML-List (ul, li)
   *
   * htmlSnippets is an array to inject html (or others) into this function
   * the keys/values are:
   *
   * 'rootOpen' => function ($root) @returns string just the root-open-tag (e.g. <ul id="navigation">)
   * 'listOpen' => function ($parentNode) @returns string just the open tag for a list of childen (e.g. <ul>)
   * 'node'     => function ($node) @returns string the open tag of the node (e.g. <li>) AND the represenation of the node (e.g. <a>$node->getTitle()</a>)
   *
   * 'rootClose' => string (e.g. </ul>)
   * 'listClose' => string (e.g. </ul>)
   * 'nodeClose' => string (e.g. </li>)
   *
   * @param Webforge\CMS\Navigation\Node[] $tree
   * @param array $htmlSnippets an array of snippets to inject
   * @return string
   */
  public function toHTMLList(Array $tree, array $htmlSnippets = array()) {
    // TODO: whats faster: this or a implementation as a class with 6 methods?
    // this is of course more flexible than a decorator class
    $htmlSnippets = array_merge(
      array(
        'rootOpen'=>function ($root) { return '<ul>'; },
        'listOpen'=>function ($parentNode) { return '<ul>'; },
        'nodeDecorator'=>function ($node) { return '<li>'.$node->getNodeHTML(); },
        
        'rootClose'=>'</ul>',
        'listClose'=>'</ul>',
        'nodeClose'=>'</li>',
      ),
      $htmlSnippets
    );
    extract($htmlSnippets); // unfortunately we cannot use in code: $htmlSnippets->rootOpen()

    if (empty($tree)) {
      return $rootOpen(NULL).$rootClose;
    }
    
    $depth = -1;
    $indent = -1;
    $prevNode = NULL;
    $html = '';
    while (!empty($tree)) {
      $node = array_shift($tree);

      // open new Level (go down) (the node is the first node of a new list of children)
      if ($node->getDepth() > $depth) {
        $indent++;
        if ($depth === -1) {
          $html .= $rootOpen($node)."\n";
        } else {
          $html .= "\n".str_repeat('  ', $indent).$listOpen($prevNode)."\n";
        }
        
        $indent++;
      
      // Level up? (the node is on an upper level after a list of childen)
      } elseif ($node->getDepth() < $depth) {
        $html .= $nodeClose."\n"; // close last node of layer
        
        // close all levels inbetween current node and previous node
        for ($d = $node->getDepth(); $d < $depth; $d++) {
          $indent--;
          $html .= str_repeat('  ', $indent).$listClose."\n";
          $indent--;
          $html .= str_repeat('  ', $indent).$nodeClose."\n"; 
        }
      
      // same Level
      } else {
        $html .= $nodeClose."\n"; // close sibling from before
      }
      
      // add the new node
      $html .= str_repeat('  ', $indent).$nodeDecorator($node);
      
      $depth = $node->getDepth();
      $prevNode = $node;
    }

    // close from last iteration
    $html .= $nodeClose."\n";
    for ($i = 1; $i<=$depth; $i++) {
      $indent--;
      $html .= str_repeat('  ', $indent).$listClose."\n"; 
      $indent--;
      $html .= str_repeat('  ', $indent).$nodeClose."\n";
    }
    $html .= $rootClose;
    
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
   * @param Webforge\CMS\Navigation\Node[] $tree
   * @param array $events
   */
  public function toParentPointer(Array $tree, Array $events = array()) {
    $ppTree = array();

    if (count($tree) > 0) {
      $stack = array(NULL);
      $depth = 0;
      $prevNode = NULL;

      foreach ($tree as $node) {

         if ($node->getDepth() > $depth) {
           $stack[] = $prevNode;
           $depth = $node->getDepth();

         } else if ($node->getDepth() < $depth) {
           for ($s = 1; $s <= abs($depth - $node->getDepth()); $s++) {
             array_pop($stack);
           }
           $depth = $node->getDepth();
        }

        $node->setParent(A::peek($stack)); 
        $ppTree[] = $prevNode = $node;
      }
    }

    return $ppTree;
  }

  /**
   * @param Webforge\CMS\Navigation\Node[] $tree
   * @param array $events
   */
  public function toStructure(Array $tree, Array $events = array()) {
    $ppTree = array();

    if (!isset($events['onNodeComplete'])) {
      $events['onNodeComplete'] = function (Node $node, Node $parent = NULL, Array $stack) {

      };
    }

    if (count($tree) > 0) {
      $stack = array(NULL);
      $depth = 0;
      $prevNode = NULL;

      foreach ($tree as $node) {
        $node->setChildren($this->emptyCollection());

         if ($node->getDepth() > $depth) {
           // new level
           $stack[] = $prevNode;
           $depth = $node->getDepth();

         } else if ($node->getDepth() < $depth) {
           // level end
           for ($s = 1; $s <= abs($depth - $node->getDepth()); $s++) {
             array_pop($stack);
           }
           $depth = $node->getDepth();
        }

        $node->setParent($parent = A::peek($stack)); 

        if ($parent)
          $this->appendChild($parent, $node);
        else // node is a root node
          $ppTree[] = $node;

        $events['onNodeComplete']($node, $parent, $stack);

        $prevNode = $node;
      }
    }

    return $ppTree;
  }

  protected function appendChild(Node $parentNode, Node $child) {
    $children = $parentNode->getChildren();
    $children[] = $child;
    $parentNode->setChildren($children);
    return $children;
  }

  protected function emptyCollection() {
    return new \Psc\Data\ArrayCollection(array());
    return array();
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
