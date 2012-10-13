<?php

namespace Webforge\Code\Generator;

use Psc\Code\Generate\ClassWritingException;
use Psc\System\File;
use Webforge\Common\String as S;
use Psc\Code\Generate\DocBlock;
use Psc\A;

/**
 * Writes a Class in Code (PHP)
 *
 * The ClassWriter writes the Stream in the gClass to a given File
 * 
 * This writer should change somehow, so that it does not use the GClass inner
 * Functions to generate the PHP from psc-cms and that its writing is own code
 */
class ClassWriter {
  
  const OVERWRITE = TRUE;
  
  /**
   * @var Webforge\Code\Generator\Imports
   */
  protected $imports;
  
  public function __construct() {
    $this->imports = new Imports();
  }
  
  public function write(GClass $gClass, File $file, $overwrite = FALSE) {
    if ($file->exists() && $overwrite !== self::OVERWRITE) {
      throw new ClassWritingException(
        sprintf('The file %s already exists. To overwrite set the overwrite parameter.', $file),
        ClassWritingException::OVERWRITE_NOT_SET
      );
    }
    
    $file->writeContents($this->writeGClassFile($gClass));
    return $this;
  }
  
  public function writeGClassFile(GClass $gClass, $eol = "\n") {
    $php = '<?php'.$eol;
    $php .= $eol;
    
    if (($namespace = $gClass->getNamespace()) != NULL) {
      $php .= 'namespace '.$namespace.';'.$eol;
      $php .= $eol;
    }
    
    $imports = clone $this->imports;
    $imports->mergeFromClass($gClass);
    
    if ($use = $imports->php($namespace)) {
      $php .= $use;
      $php .= $eol;
    }
    
    $php .= $this->writeGClass($gClass, $namespace, $eol);
    
    $php .= $eol;
    $php .= '?>';
    return $php;
  }
  
  /**
   * returns the Class as PHP Code (without imports (use), without namespace decl)
   *
   * indentation is fixed: 2 whitespaces
   * @return string the code with docblock from class { to }
   */
  public function writeGClass(GClass $gClass, $namespace, $eol = "\n") {
    $that = $this;
    
    $php = NULL;
    
    /* DocBlock */
    if ($gClass->hasDocBlock())
      $php .= $this->writeDocBlock($gClass->getDocBlock(), 0);
    
    /* Modifiers */
    $php .= $this->writeModifiers($gClass->getModifiers());
    
    /* Class */
    $php .= 'class '.$gClass->getName().' ';
    
    /* Extends */
    if (($parent = $gClass->getParent()) != NULL) {
      // its important to use the contextNamespace here, because $namespace can be !== $gClass->getNamespace()
      if ($parent->getNamespace() === $namespace) {
        $php .= 'extends '.$parent->getName(); // don't prefix with namespace
      } else {
        // should it add to use, or use \FQN in extends?
        $php .= 'extends '.'\\'.$parent->getFQN();
      }
      $php .= ' ';
    }
    
    /* Interfaces */
    if (count($gClass->getInterfaces()) > 0) {
      $php .= 'implements ';
      $php .= A::implode($this->getInterfaces(), ', ', function (GClass $iClass) use ($namespace) {
        if ($iClass->getNamespace() === $namespace) {
          return $iClass->getName();
        } else {
          return '\\'.$iClass->getFQN();
        }
      });
      $php .= ' ';
    }
    
    $php .= '{'.$eol;
    
    /* those other methods make the margin with line breaks to top and to their left.*/
    
    /* Constants */
    $php .= A::joinc($gClass->getConstants(), '  '.$eol.'%s;'.$eol, function ($constant) use ($that) {
      return $that->writeConstant($constant, 2);
    });
    
    /* Properties */
    $php .= A::joinc($gClass->getProperties(), '  '.$eol.'%s;'.$eol, function ($property) use ($that) {
      return $that->writeProperty($property, 2);
    });

    /* Methods */
    $php .= A::joinc($gClass->getMethods(), '  '.$eol.'%s'.$eol, function ($method) use ($that) {
      return $that->writeMethod($method, 2); 
    });
    
    $php .= '}';
    
    return $php;
  }

  /**
   * returns the PHP Code for a GMethod
   *
   * after } is no LF
   * @return string
   */
  public function writeMethod(GMethod $method, $baseIndent = 0) {
    $cr = "\n";
    
    $php = $this->phpDocBlock($baseIndent);
    
    // vor die modifier muss das indent
    $php .= str_repeat(' ',$baseIndent);
    
    
    $php .= parent::php($baseIndent); 
    
    return $php;
  }
  //public function phpSignature($baseIndent = 0) {
  //  return parent::phpSignature(0); // damit hier vor function nicht indent eingefügt wird (der muss vor unsere modifier)
  //}
  //
  //public function phpBody($baseIndent = 0) {
  //  if ($this->isAbstract()) {
  //    return ';';
  //  } else {
  //    return parent::phpBody($baseIndent);
  //  }
  //}

  /**
   * Gibt den PHPCode für die Funktion zurück
   *
   * nach der } ist kein LF
   */
  public function writeFunction($baseIndent = 0) {
    $php = NULL;
    $cr = "\n";
    
    $php .= $this->phpSignature($baseIndent);
    $php .= $this->phpBody($baseIndent);
  
    return $php;
  }
  
  protected function phpSignature($baseIndent = 0) {
    $php = NULL;
    $cr = "\n";
    
    $php .= 'function '.$this->getName().'(';
    $php .= A::implode($this->getParameters(), ', ', function ($parameter) {
      return $parameter->php();
    });
    $php .= ')';

    return S::indent($php,$baseIndent,$cr); // sollte eigentlich ein Einzeiler sein, aber wer weiß
  }
  
  public function getParametersString() {
    $php = '(';
    $php .= A::implode($this->getParameters(), ', ', function ($parameter) {
      return $parameter->php();
    });
    $php .= ')';
    return $php;
  }
  
  protected function phpBody($baseIndent = 0) {
    $php = NULL;
    $cr = "\n";
    
    // das hier zuerst ausführen, damit möglicherweise cbraceComment noch durchrs extracten gesetzt wird
    $body = $this->getBody($baseIndent+2,$cr); // Body direkt + 2 einrücken

    $php .= ' {'; // das nicht einrücken, weil das direkt hinter der signatur steht
    if ($this->cbraceComment != NULL) { // inline comment wie dieser
      $php .= ' '.$this->cbraceComment;
    }
    $php .= $cr;  // jetzt beginnt der eigentlich body
    
    $php .= $body; 
    $php .= S::indent('}',$baseIndent,$cr); // das auf Base einrücken
    
    return $php;
  }
  
  public function writeGParameter($useFQNHints = FALSE) {
    $php = NULL;
    
    // Type Hint oder Array
    if ($this->isArray()) {
      $php .= 'Array ';
    } elseif (($c = $this->getHint()) != NULL) {
      if ($useFQNHints) {
        $php .= $c->getName().' ';
      } else {
        $php .= $c->getClassName().' ';
        // @TODO das hier macht aus den FQNs immer was ohne \ am Anfang,
        // und hat nie den Namespace dabei
        // wir müssen dann die imports so anpassen, dass das auch hinhaut ...
      }
    }
    
    // name
    $php .= ($this->isReference() ? '&' : NULL).'$'.$this->name;
    
    // optional
    if ($this->isOptional()) {
      $php .= ' = ';
      if (is_array($this->default) && count($this->default) == 0) {
        $php .= 'array()';
      } else {
        $php .= $this->exportArgumentValue($this->default); // das sieht scheise aus
      }
    }
    
    return $php;
  }
  
  /**
   * @return string
   */
  public function writeDocBlock(DocBlock $docBlock, $baseIndent = 0) {
    return S::indent($docBlock->toString(), $baseIndent);
  }
  
  /**
   * @return string with whitespace after the last modifier
   */
  public function writeModifiers($bitmap) {
    $ms = array(GModifiersObject::MODIFIER_ABSTRACT => 'abstract',
                GModifiersObject::MODIFIER_PUBLIC => 'public',
                GModifiersObject::MODIFIER_PRIVATE => 'private',
                GModifiersObject::MODIFIER_PROTECTED => 'protected',
                GModifiersObject::MODIFIER_STATIC => 'static',
                GModifiersObject::MODIFIER_FINAL => 'final'
               );
    
    $php = NULL;
    foreach ($ms as $const => $modifier) {
      if (($const & $bitmap) == $const)
        $php .= $modifier.' ';
    }
    return $php;
  }
  
  /**
   * Adds an Import, that should be added to every written file
   * 
   */
  public function addImport(GClass $gClass, $alias = NULL) {
    $this->imports->add($gClass, $alias);
    return $this;
  }

  // @codeCoverageIgnoreStart
  /**
   * Removes an Import, that should be added to every written file
   *
   * @param string $alias case insensitive
   */
  public function removeImport($alias) {
    $this->imports->remove($alias);
    return $this;
  }
  
  /**
   * @param Webforge\Code\Generator\Imports $imports
   * @chainable
   */
  public function setImports(Imports $imports) {
    $this->imports = $imports;
    return $this;
  }
  // @codeCoverageIgnoreEnd  
  
  /**
   * @return Webforge\Code\Generator\Imports
   */
  public function getImports() {
    return $this->imports;
  }
}
?>