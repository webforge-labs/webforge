<?php

namespace Webforge\Code\Generator;

use IteratorAggregate;
use ArrayIterator;
use Countable;

class Imports implements IteratorAggregate, Countable {
  
  /**
   * @var array
   */
  protected $classes = array();
  
  /**
   * @var array the keys from $classes but lowercased
   */
  protected $aliases = array();
  
  /**
   * you can use numeric keys for imports without an specific alias (sets the alias to the className of the class)
   * @param array $alias => GClass $importClass
   */
  public function __construct(Array $importClasses = array()) {
    foreach ($importClasses as $alias => $gClass) {
      if (is_numeric($alias)) $alias = NULL;
      
      $this->add($gClass, $alias);
    }
  }
  
  /**
   * Adds an Import
   *
   * its not allowed to set an already used alias.
   * You have to remove the alias first
   * @param string $alias sets an explicit alias. (the implicit is always the classname)
   */
  public function add(GClass $import, $alias = NULL) {
    if (!isset($alias)) $alias = $import->getName();
    
    if (array_key_exists($lowerAlias = mb_strtolower($alias), $this->aliases)) {
      throw new \Psc\Exception('Alias: '.$alias.' is already used by Class '.$this->classes[ $this->aliases[$lowerAlias] ]);
    }
    
    $this->classes[$alias] = $import;
    $this->aliases[$lowerAlias] = $alias;
    return $this;
  }
  
  /**
   * Removes an Import
   * 
   * @param string|GClass $aliasOrGClass
   * @chainable
   */
  public function remove($aliasOrGClass) {
    $alias = $aliasOrGClass instanceof GClass ? $aliasOrGClass->getName() : $aliasOrGClass;
    
    if (array_key_exists($alias, $this->classes)) {
      unset($this->classes[$alias]);
      unset($this->aliases[mb_strtolower($alias)]);
    }
    
    return $this;
  }

  /**
   * Gets an iterator for iterating over the elements in the collection.
   *
   * @return ArrayIterator
   */
  public function getIterator() {
    return new ArrayIterator($this->classes);
  }
  
  /**
   * @return int
   */
  public function count() {
    return count($this->classes);
  }  
}
?>