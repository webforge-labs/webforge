<?php

namespace Webforge\Code\Test;

use PHPUnit_Framework_Constraint;
use Psc\System\File;

class CodeEqualsConstraint extends PHPUnit_Framework_Constraint {
  
  /**
   * Der PHP Code
   * 
   * @var string
   */
  protected $code;
  
  /**
   * Caches for the toString()-Method to debug
   * @var string
   */
  protected $normalizedCode, $normalizedOtherCode;
  
  public function __construct($code) {
    $this->code = $code;
  }
  
  /**
    * Evaluates the constraint for parameter $other. Returns TRUE if the
    * constraint is met, FALSE otherwise.
    *
    * Code is Equal, when the Code without non-relevant whitespace is equal
    * 
    * @param mixed $other Value or object to evaluate.
    * @return bool
    */
  public function matches($other) {
    return ($this->normalizedCode = $this->normalizeCode($other)) === ($this->normalizedOtherCode = $this->normalizeCode($this->code));
  }
  
  /**
   * Normalizes the code (removes whitespace from code)
   *
   * for better operability: ensure that $code is syntax checked
   * @return array tokenstream
   */
  public function normalizeCode($code) {
    $code = trim($code);
    $code = \Psc\String::fixEOL($code);
    
    if (mb_strpos($code, '<?php') === FALSE) { // we will not constraint that <?php is at the start
      $code = '<?php '.$code;
    }
    
    // symfonys strip comments is really wrong here (because it hardly replaces \n+ with ' ', which is wrong in the inner of strings)
    /*
      this loop removes ALL whitespace and adds the mandatory (where mandatory is defined as code style)
      for better diffs here should be rules added for every token
       
      because ALL whitespace is stripped, this method does not detect some missing-whitespace-edge cases (does it?)
    */
    
    $php = '';
    $indent = 0;
    $newline = FALSE;
    $prevTokenType = NULL;
    foreach (token_get_all($code) as $token) {
      if (is_array($token)) {
        list($tokenType, $tokenValue) = $token;
      } else {
        $tokenValue = $tokenType = $token;
      }

      switch ($tokenType) {
        // skip all whitespace
        case T_WHITESPACE:
        case T_COMMENT:
          continue(2);
          break;

        // inc indent
        case '{':
          $indent++;
          break;
        
        // dec indent
        case '}':
          $indent = max(0, $indent-1);
          break;
      }
      
      if ($newline) {
        $php .= str_repeat('  ', $indent);
        $newline = FALSE;
      }
      
      switch ($tokenType) {
        // space before, line end
        case '{':
          $php .= ' '.$tokenValue."\n";
          $newline = TRUE;
          break;
        
        // line end
        case T_OPEN_TAG:
        case '}':
        case ';':
          $php .= $tokenValue."\n";
          $newline = TRUE;
          break;
        
        // space after
        case T_NEW:
        case T_OPEN_TAG:
        case T_NAMESPACE:
        case T_OPEN_TAG:
        case T_IF:
        case T_CLASS:
        case T_INTERFACE:
        case T_PUBLIC:
        case T_ABSTRACT:
        case T_PROTECTED:
        case T_FUNCTION:
        case T_BOOL_CAST:
        case T_CASE:
        case T_USE;
          $php .= $tokenValue.' ';
          break;
        
        // space after & before
        case T_CONCAT_EQUAL:
        case T_IMPLEMENTS:
        case T_BOOLEAN_AND:
        case T_BOOLEAN_OR:
        case T_DOUBLE_ARROW:
        case T_DIV_EQUAL:
          $php .= ' '.$tokenValue.' ';
          break;
          
        // no addidtional whitespace
        default:
          $php .= $tokenValue;
          break;
      }      
    }
    
    return $php;
  }
  
  public function toString() {
    // @TODO more verbose find non equal line and display?
    $string = 'is equivalent PHP code to expected Code';
    
    if (isset($this->normalizedCode)) {
      $string .= "\n";
      $string .= \PHPUnit_Util_Diff::diff(
        $this->normalizedCode,
        $this->normalizedOtherCode
      );
    }
    
    return $string;
  }
}
?>