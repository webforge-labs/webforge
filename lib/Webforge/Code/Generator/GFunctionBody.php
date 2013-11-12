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
   * @var array|NULL
   */
  protected $stmts;

  /**
   * @var array
   */
  protected $body;

  public function __construct(Array $lines = array()) {
    $this->body = $lines;
  }
  
  public static function create(Array $body) {
    $gBody = new GFunctionBody($body);
    
    return $gBody;
  }
  
  public function php($baseIndent = 0, $eol = "\n") {
    if (!isset($this->stmts)) {
      $parser = new PHPParser_Parser(new PHPParser_Lexer);
      $body = A::join($this->body, "%s\n");

      $this->stmts = $parser->parse('<?php '.$body);
    }

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
    $this->stmts = NULL;
    $this->body = array_merge($this->body, $codeLines);
    return $this;
  }
  
  public function beforeBody(Array $codeLines) {
    throw NotImplementedException('not yet');
    $this->stmts = NULL;
    $this->body = array_merge($codeLines, $this->body);
    return $this;
  }

  public function afterBody(Array $codeLines) {
    throw NotImplementedException('not yet');
    $this->stmts = NULL;
    $this->body = array_merge($this->body, $codeLines);
    return $this;
  }

  public function insertBody(Array $codeLines, $index) {
    $this->stmts = NULL;
    A::insertArray($this->body, $codeLines, $index);
    return $this;
  }
}
