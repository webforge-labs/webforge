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
    
    $this->offsets = array();
    $this->lastOffset = -1;
    $this->php = '';
    print "\n";
    $ppPHP = parent::prettyPrint($nodes);
    
    //$missingOffsets = array_diff(range(0, $this->lastOffset), $this->offsets);
    
    return $this->php;
  }
  
  protected function p(PHPParser_Node $node) {
    $start = $node->getAttribute('startOffset');
    $end = $node->getAttribute('endOffset');
    
    $this->lastOffset++;
    
    if ($start > $this->lastOffset) {
      printf("fülle start auf: %d:%d vor %s\n", $this->lastOffset, $start, get_class($node));
      $this->php .= implode('', $this->stks->slicep($this->lastOffset, $start));
    }
    
    printf("fülle offsets: %d:%d %s\n", $start,$end, get_class($node));
    foreach(range($start, $end) as $offset) {
      $this->offsets[] = $offset;
      $this->php .= $this->stks->get($offset);
    }
    
    $this->lastOffset = $end;
    
    parent::p($node);
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