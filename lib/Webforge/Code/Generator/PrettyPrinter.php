<?php

namespace Webforge\Code\Generator;

use PHPParser_Node_Stmt_Switch;
use PHPParser_Node_Stmt_Case;
use PHPParser_Node_Expr_ClosureUse;
use PHPParser_Node_Expr_Closure;
use PHPParser_Node;
use Webforge\Common\String AS S;

class PrettyPrinter extends \PHPParser_PrettyPrinter_Zend {
  
  protected $baseIndent;
  
  protected $offsets, $lastOffset;
  public $inphp, $php;
  
  protected $stks;
  
  public function __construct($baseIndent = 0) {
    $this->baseIndent = $baseIndent;
    parent::__construct();
  }
  
  public function prettyPrint(array $nodes) {
    $this->stks = new StringTokenStream($this->inphp);
    
    $this->php = array();
    foreach ($this->stks as $offset=>$value) {
      $this->php[$offset] = $value;
    }

    
    $this->offsets = array();
    $this->lastOffset = -1;
    $ppPHP = parent::prettyPrint($nodes);
    

    return implode('', $this->php);
  }
  
  protected function p(PHPParser_Node $node) {
    $start = $node->getAttribute('startOffset');
    $end = $node->getAttribute('endOffset');
    
    //if ($start > $this->lastOffset) {
    //  printf("fülle start auf: %d:%d vor %s\n", $this->lastOffset, $start, get_class($node));
    //  $this->php .= implode('', $this->stks->slicep($this->lastOffset, $start));
    //}
    //
    $php = parent::p($node);
    
    if ($node instanceof \PHPParser_Node_Stmt_Use) {
      $this->php[$start] = $php;
      printf("\nreplace %d:%d with \n%s\n", $start, $end, $php);
      
      foreach(range($start+1, $end) as $offset) {
        $this->php[$offset] = NULL;
      }
    }
    
    return $php;
  }

  public function pStmt_Switch(PHPParser_Node_Stmt_Switch $node) {
    return 'switch (' . $this->p($node->cond) . ') {'
      . "\n" . $this->pStmts($node->cases) . "\n" .'}';
  }

  public function pStmt_Case(PHPParser_Node_Stmt_Case $node) {
    return (null !== $node->cond ? 'case ' . $this->p($node->cond) : 'default') . ':'
      . (count($node->stmts) > 0
         ? "\n" . $this->pStmts($node->stmts)
         : ''
        );
  }

  public function pExpr_Closure(PHPParser_Node_Expr_Closure $node) {
    return ($node->static ? 'static ' : '')
      . 'function ' . ($node->byRef ? '&' : '')
      . '(' . $this->pCommaSeparated($node->params) . ')'
      . (!empty($node->uses) ? ' use (' . $this->pCommaSeparated($node->uses) . ')': '')
      . ' {' . "\n" . $this->pStmts($node->stmts) . "\n" . '}';
  }

}
?>