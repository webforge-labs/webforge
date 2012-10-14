<?php

namespace Webforge\Code\Generator;

use Psc\System\File;
use PHPParser_Parser;
use PHPParser_Lexer;
use PHPParser_Error;
use PHPParser_NodeTraverser;
use PHPParser_NodeVisitor_NameResolver;

class ClassReader {
  
  public $stmts;
  
  /**
   * @return GClass
   */
  public function read(File $file) {
    $code = $file->getContents();
    
    $parser = new PHPParser_Parser(new PHPParser_Lexer);
    
    $traverser = new PHPParser_NodeTraverser;
    $traverser->addVisitor(new PHPParser_NodeVisitor_NameResolver); // we will need resolved names
    $traverser->addVisitor($visitor = new NodeVisitor());

    try {
      $this->stmts = $parser->parse($code);
      
      $traverser->traverse($this->stmts);
      
    } catch (PHPParser_Error $e) {
      throw new \RuntimeException(sprintf("File '%s' cannot be read correctly from ClassReader.", $file), 0, $e);
    }
    
    return $visitor->getGClass();
  }
}
?>