<?php

namespace Webforge\Code\Generator;

use IteratorAggregate;
use ArrayIterator;

class StringTokenStream implements IteratorAggregate {
  
  protected $tokens = array();
  
  public function __construct($phpCode) {
    $this->parse($phpCode);
  }
  
  public function get($offset) {
    return $this->tokens[$offset];
  }
  
  public function slice($offset, $length) {
    return array_slice($this->tokens, $offset, $length);
  }

  public function slicep($offsetFrom, $offsetTo) {
    return array_slice($this->tokens, $offsetFrom, abs($offsetTo-$offsetFrom));
  }
  
  protected function parse($phpCode) {
    $this->tokens = array();
    
    foreach (token_get_all($phpCode) as $token) {
      if (is_string($token)) {
        $value = $token;
      } else {
        $value = $token[1];
      }
      
      $this->tokens[] = $value;
    }
    
    return $this;
  }
  
  public function getIterator() {
    return new ArrayIterator($this->tokens);
  }
}
?>