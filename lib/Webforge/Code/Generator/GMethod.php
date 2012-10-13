<?php

namespace Webforge\Code\Generator;

use Psc\A;

class GMethod extends GModifiersObject {

  const APPEND = GObjectCollection::END;
  const END = GObjectCollection::END;
  const PREPEND = 0;
  
  /**
   * @var Webforge\Code\Generate\GObjectCollection
   */
  protected $parameters;
  
  /**
   * @var string
   */
  protected $name;
  
  /**
   * The code from the method
   *
   * @var Webforge\Code\Generator\GFunctionBody
   */
  protected $body = NULL;
  
  /**
   * @var bool
   */
  protected $returnsReference;
  
  /**
   * @var Webforge\Code\Generator\GClass
   */
  protected $gClass;
  
  /**
   * @param string $name name of the method
   * @param GParameter[] $parameters
   * @param string|array body
   */
  public function __construct($name = NULL, Array $parameters = array(), GFunctionBody $body = NULL, $modifiers = self::MODIFIER_PUBLIC) {
    $this->name = $name;
    $this->modifiers = $modifiers;
    
    $this->parameters = new GObjectCollection(array());
    foreach ($parameters as $parameter) {
      $this->addParameter($parameter);
    }
    
    if(isset($body)) {
      $this->setBody($body);
    }
  }
  
  public static function create($name = NULL, Array $parameters = array(), GFunctionBody $body = NULL, $modifiers = self::MODIFIER_PUBLIC) {
    return new static($name, $parameters, $body, $modifiers);
  }

  /**
   * @chainable
   * @param int $position 0-based
   */
  public function addParameter(GParameter $parameter, $position = self::END) {
    $this->parameters->add($parameter, $position);
    return $this;
  }
  
  /**
   * @param GParam|string $nameOrParameter
   * @chainable
   */
  public function removeParameter($nameOrParameter) {
    $this->parameters->remove($nameOrParameter);
    return $this;
  }

  /**
   * @param GParam|string $nameOrParameter
   * @return bool
   */
  public function hasParameter($nameOrParameter) {
    return $this->parameters->has($nameOrParameter);
  }
  
  /**
   * @param int $order 0-based or self::END
   */
  public function setParameterOrder($nameOrParameter, $order) {
    $this->parameters->setOrder($nameOrParameter, $order);
    return $this;
  }

  /**
   * @return Webforge\Code\Generator\GParameter[]
   */
  public function getParameters() {
    return $this->parameters->toArray();
  }
  
  /**
   * @return Webforge\Code\Generator\GParameter
   */
  public function getParameterByIndex($index) {
    return $this->parameters->get($index);
  }

  /**
   * @return Webforge\Code\Generator\GParameter
   */
  public function getParameterByName($name) {
    return $this->parameters->get($name);
  }
  
  /**
   * @return Webforge\Code\Generator\GParameter
   */
  public function getParameter($nameOrIndex) {
    return $this->parameters->get($nameOrIndex);
  }
  
  /**
   * @return bool
   */
  public function returnsReference() {
    return $this->returnsReference;
  }

  
  public function createDocBlock($body = NULL) {
    if (!$this->hasDocBlock()) {
      $docBlock = parent::createDocBlock($body);
    } else {
      $docBlock = $this->getDocBlock();
    }
    return $docBlock;
  }
  
  public function getBody($indent = 2, $cr = "\n") {
    if (!array_key_exists($indent, $this->body)) {
      
      if (count($this->getBodyCode()) == 0) {
        $this->body[$indent] = NULL;
      } else {
        /*
          wir können hier nicht S::indent() nehmen da dies alle alle alle \n einrückt
          (auch diese, die im Code geschützt sind (z.b. in Strings))
        */
        $white = str_repeat(' ',$indent);
      
        $this->body[$indent] = $white.implode($cr.$white, $this->getBodyCode()).$cr;
      }
    }
    
    return $this->body[$indent];
  }
  
  public function setBody($string) {
    // hier müssen wir parsen
    $this->sourceCode = 'function () {'."\n".$string.'}'; // first make it work *hüstel*
    $this->body = array(); // cache reset
    $this->bodyCode = NULL;
    return $this;
  }
  
  /**
   * Setzt den Body Code als Array (jede Zeile ein Eintrag im Array)
   *
   * anders als setBody ist dies hier schneller, da bei setBody immer der Body-Code erneut geparsed werden muss (wegen indentation)
   * es ist aber zu gewährleisten, dass wirklich jede Zeile ein Eintag im Array ist
   */
  public function setBodyCode(Array $lines) {
    $this->sourceCode = implode("\n",$lines); // indent egal, wird eh nicht geparsed, da wir ja schon bodyCode fertig haben
    $this->body = array(); // cache reset
    $this->bodyCode = $lines;
    return $this;
  }
  
  
  /**
   *
   * parsed $this->sourceCode in einen Array von Zeilen
   * ist sourceCode nicht gesetzt wird srcFileName nach getStartLine und getEndLine ausgeschnitten
   *
   */
  public function getBodyCode() {
    if (!isset($this->bodyCode)) { // parse von reflection PHP-Code
      $this->debug = NULL;
      /* Hier ist die einzige Möglichkeit, wo wir die Chance haben den Indent richtig zu setzen (wenn wir von der Reflection parsen)
        den sonst wissen wir nie ob wir innerhalb eines Strings sind und \n einrücken dürfen
      */
      
      if (!isset($this->sourceCode)) {
        if (isset($this->srcFileName)) {
          $this->sourceCode = $this->getSourceCode(new \Psc\System\File($this->srcFileName), $this->getStartLine(), $this->getEndLine());
          $this->debug = "\n".$this->srcFileName.' '.$this->getStartLine().'-'.$this->getEndLine().' : "'.$this->sourceCode.'"'."\n";
        } else {
          $this->sourceCode = NULL;
          return $this->bodyCode = array();
        }
      }
    
      $extracter = new Extracter();
      try {
        $body = $extracter->extractFunctionBody($this->sourceCode);
        if (count($body) === 0) {
          $this->bodyCode = array();
          //throw new \Psc\Exception('Es konnte kein Body aus '.Code::varInfo($this->sourceCode).' extrahiert werden');
        } else {
          if ($body[0][0] === -1) { // inline comment wie dieser
            $this->cbraceComment = $body[0][1];
            array_shift($body);
        }
          
          $baseIndent = max(0,$body[0][0]);
          foreach ($body as $key=>$list) {
            list ($indent, $line) = $list;
            $this->bodyCode[] = str_repeat(' ',max(0,$indent-$baseIndent)).$line;
          }
        }
      } catch (\Psc\Code\ExtracterException $e) {
        throw new SyntaxErrorException('Kann BodyCode nicht aus SourceCode erzeugen wegen eines Parse-Errors in: '.$e->context, 0, $e);
      }
    }
    
    return $this->bodyCode;
  }
  
  /**
   * Fügt dem Code der Funktion neue Zeilen am Ende hinzu
   *
   * @param array $codeLines
   */
  public function appendBodyLines(Array $codeLines) {
    $this->bodyCode = array_merge($this->getBodyCode(), $codeLines);
    return $this;
  }
  
  public function beforeBody(Array $codeLines) {
    $this->bodyCode = array_merge($codeLines, $this->getBodyCode());
    return $this;
  }

  public function afterBody(Array $codeLines) {
    $this->bodyCode = array_merge($this->getBodyCode(), $codeLines);
    return $this;
  }

  public function insertBody(Array $codeLines, $index) {
    $this->getBodyCode();
    \Psc\A::insertArray($this->bodyCode, $codeLines, $index);
    return $this;
  }
  
  /**
   * @param string $cbraceComment
   * @chainable
   */
  public function setCbraceComment($cbraceComment) {
    $this->cbraceComment = $cbraceComment;
    return $this;
  }

  /**
   * Der CBrace Comment kann an der öffnenden klammer { der function sein bevor das EOL kommt
   * 
   * @return string
   */
  public function getCbraceComment() {
    return $this->cbraceComment;
  }
  
  // @codeCoverageIgnoreStart
  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }
  
  /**
   * @chainable
   */
  public function setName($name) {
    $this->name = $name;
    return $this;
  }
  
  /**
   * @return string
   */
  public function getKey() {
    return $this->name;
  }
  
  /**
   * @param Webforge\Code\Generator\GClass $gClass
   * @chainable
   */
  public function setGClass(GClass $gClass) {
    $this->gClass = $gClass;
    return $this;
  }

  /**
   * @return Webforge\Code\Generator\GClass
   */
  public function getGClass() {
    return $this->gClass;
  }
  // @codeCoverageIgnoreEnd
}
?>