<?php

namespace Webforge\Code\Generator;

class GFunctionBody {
  
  public function php($baseIndent = 0, $eol = "\n") {
    
  }

  /**
   * Fügt dem Code der Funktion neue Zeilen am Ende hinzu
   *
   * @param array $codeLines
   */
  public function appendBodyLines(Array $codeLines) {
    throw \Psc\Code\NotImplementedException('not yet');
    $this->bodyCode = array_merge($this->getBodyCode(), $codeLines);
    return $this;
  }
  
  public function beforeBody(Array $codeLines) {
    throw \Psc\Code\NotImplementedException('not yet');
    $this->bodyCode = array_merge($codeLines, $this->getBodyCode());
    return $this;
  }

  public function afterBody(Array $codeLines) {
    throw \Psc\Code\NotImplementedException('not yet');
    $this->bodyCode = array_merge($this->getBodyCode(), $codeLines);
    return $this;
  }

  public function insertBody(Array $codeLines, $index) {
    throw \Psc\Code\NotImplementedException('not yet');
    $this->getBodyCode();
    \Psc\A::insertArray($this->bodyCode, $codeLines, $index);
    return $this;
  }
}
?>