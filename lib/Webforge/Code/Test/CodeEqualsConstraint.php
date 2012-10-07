<?php

namespace Webforge\Code\Test;

use PHPUnit_Framework_Constraint;
use Psc\System\File;

class CodeEqualsConstraint extends PHPUnit_Framework_Constraint {
  
  const T_EOL = 'T_EOL'; 

  /**
   * Der PHP Code
   * 
   * @var string
   */
  protected $code;
  
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
    return $this->normalizeCode($other) === $this->normalizeCode($this->code);
  }
  
  /**
   * Normalizes the code (removes whitespace from code)
   *
   * @return array tokenstream
   */
  public function normalizeCode($code) {
    if (mb_strpos($code, '<?php') === FALSE) { // we will not constraint that <?php is at the start
      $code = '<?php '.$code;
    }
    
    // symfonys strip comments is really wrong here (because it hardly replaces \n+ with ' ', which is wrong in the inner of strings)
    
    // unfortunately php_strip_whitespace operates on files (sadly)
    $tmp = File::createTemporary();
    $tmp->writeContents($code);
    
    $code = php_strip_whitespace((string) $tmp);
    $tmp->delete();
    
    // unfortunately this does not clean everything:
    // ($argumentList) {
    // ($argumentList){
    // they should be considered equivalent
    
    $php = '';
    $prevTokenType = NULL;
    foreach (token_get_all($code) as $token) {
      if (is_array($token)) {
        if ($token[0] === T_WHITESPACE) {
          
          // around blocks and after statements, the whitespace is optional
          if ($prevTokenType === '}' || $prevTokenType === '{' || $prevTokenType === ';')
            continue;
          
        } else {
          $php .= $token[1];
        }
        
        $prevTokenType = $token[0];
      } else {
        $php .= $token;
        $prevTokenType = $token;
      }
    }
    
    return $php;
  }
  
  public function toString() {
    // @TODO more verbose find non equal line and display?
    return 'has equivalent PHP code with <expectedCode>';
  }
}
?>