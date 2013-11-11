<?php

namespace Webforge\Code\Generator;

use PHPParser_Parser;
use PHPParser_Lexer;
use Webforge\Common\ArrayUtil as A;
use Webforge\Common\Exception\NotImplementedException;

class GFunctionBody {
  
  /**
   * PHPParser stmts
   * 
   * @var array
   */
  protected $stmts;

  public function __construct(Array $stmts = array()) {
    $this->stmts = $stmts;
  }
  
  public static function create($body) {
    $stmts = array();
    
    if (is_array($body)) {
      $body = A::join($body, "%s\n");
    }
    
    if (is_string($body)) {
      $parser = new PHPParser_Parser(new PHPParser_Lexer);
      $stmts = $parser->parse('<?php '.$body);
    }
    
    $gBody = new GFunctionBody($stmts);
    
    return $gBody;
  }
  
  public function php($baseIndent = 0, $eol = "\n") {
    $printer = new PrettyPrinter($baseIndent, $eol);
    
    return $printer->prettyPrint($this->stmts);
  }

  /**
   * Fügt dem Code der Funktion neue Zeilen am Ende hinzu
   *
   * @param array $codeLines
   */
  public function appendBodyLines(Array $codeLines) {
    throw NotImplementedException('not yet');
    $this->bodyCode = array_merge($this->getBodyCode(), $codeLines);
    return $this;
  }
  
  public function beforeBody(Array $codeLines) {
    throw NotImplementedException('not yet');
    $this->bodyCode = array_merge($codeLines, $this->getBodyCode());
    return $this;
  }

  public function afterBody(Array $codeLines) {
    throw NotImplementedException('not yet');
    $this->bodyCode = array_merge($this->getBodyCode(), $codeLines);
    return $this;
  }

  public function insertBody(Array $codeLines, $index) {
    throw NotImplementedException('not yet');
    $this->getBodyCode();
    A::insertArray($this->bodyCode, $codeLines, $index);
    return $this;
  }
}
