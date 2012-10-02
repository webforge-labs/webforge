<?php

namespace Webforge\CMS\Navigation;

class NestedSetConverter extends \Psc\SimpleObject {

  /**
   * Wandelt einen NestedSet flat-Array in eine verschachtelte HTML-Liste um
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
}
?>