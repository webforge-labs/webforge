<?php

namespace Webforge\Doctrine;

use PDO, Doctrine\DBAL\Connection, Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Hydrates like an array Hydrator, but uses (object) not (array)
 *
 * its not that easy ....
 */
class StdClassHydrator extends \Doctrine\ORM\Internal\Hydration\ArrayHydrator {
  
  /**
   * {@inheritdoc}
   */
  protected function hydrateRowData(array $row, array &$cache, array &$result) {
    
    
  }
}
?>