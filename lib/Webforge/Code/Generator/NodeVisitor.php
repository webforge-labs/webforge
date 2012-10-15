<?php

namespace Webforge\Code\Generator;

use PHPParser_Node_Stmt_Class;
use PHPParser_Node_Stmt_Use;
use PHPParser_Node_Stmt_Namespace;
use PHPParser_Node;
use PHPParser_Node_Stmt_ClassMethod;
use PHPParser_Node_Param;
use PHPParser_Node_Name_FullyQualified;
use PHPParser_Node_Expr_Array;
use PHPParser_Node_Scalar_String;
use PHPParser_Node_Scalar_LNumber;
use PHPParser_Node_Expr_ConstFetch;
use PHPParser_Node_Expr_ClassConstFetch;

use Psc\Data\Type\ObjectType;
use Psc\Data\Type\ArrayType;
use Psc\Data\Type\StringType;

class NodeVisitor extends \PHPParser_NodeVisitorAbstract {
  
  protected $gClass;
  
  public function __construct(GClass $gClass = NULL) {
    $this->gClass = $gClass ?: new GClass;
  }
  
  public function leaveNode(PHPParser_Node $node) {
    if ($node instanceof PHPParser_Node_Stmt_Namespace) {
      //$this->gClass->setNamespace($node->name->toString());
    } elseif ($node instanceof PHPParser_Node_Stmt_Class) {
      $this->visitClass($node);
    } elseif ($node instanceof PHPParser_Node_Stmt_Use) {
      $this->visitUse($node);
    } elseif ($node instanceof PHPParser_Node_Stmt_ClassMethod) {
      $this->visitClassMethod($node);
    }
    
    //throw $this->nodeTypeError($node, __FUNCTION__);
  }
  
  protected function visitClass(PHPParser_Node_Stmt_Class $class) {
    $this->gClass->setFQN($class->namespacedName);
  }
  
  protected function visitUse(PHPParser_Node_Stmt_Use $useNode) {
    foreach ($useNode->uses as $use) {
      $this->gClass->addImport(new GClass($use->name->toString()), $use->alias);
    }
  }
  
  protected function visitClassMethod(PHPParser_Node_Stmt_ClassMethod $method) {
    $this->gClass->createMethod(
      $method->name,
      $this->createParameters($method->params),
      $this->createBody($method->stmts),
      $this->createModifiers($method),
      $method->byref
    );
  }

  protected function createParameters(Array $parameterNodes) {
    $parameters = array();
    foreach ($parameterNodes as $node) {
      $parameters[] = $this->visitParameter($node);
    }
    return $parameters;
  }
  
  protected function visitParameter(PHPParser_Node_Param $param) {
    return GParameter::create(
      $param->name,
      $this->createType($param->type, $param->default),
      $param->default === NULL ? GParameter::UNDEFINED : $this->visitExpression($param->default),
      $param->byRef
    );
  }

  protected function visitExpression($node) {
    if ($node instanceof PHPParser_Node_Expr_Array) {
      return $this->visitArray($node);
    } elseif ($node instanceof PHPParser_Node_Scalar_String) {
      return $node->value;
    } elseif ($node instanceof PHPParser_Node_Scalar_LNumber) {
      return $node->value;
    } elseif ($node instanceof PHPParser_Node_Expr_ConstFetch) {
      // @TODO wie setzen wir das hier?
      $constant = $node->name->toString();
      
      if ($constant === 'NULL') {
        return NULL;
      }
      
      return new GConstant($node->name);
    } elseif ($node instanceof PHPParser_Node_Expr_ClassConstFetch) {
      $constant = new GConstant($node->name);
      $constant->setGClass(new GClass($node->class->toString()));
      return $constant;
    }
    
    throw $this->nodeTypeError($node, __FUNCTION__);
  }
  
  protected function visitArray(PHPParser_Node_Expr_Array $node) {
    $items = array();
    foreach ($node->items as $key => $arrayItem) {
      $key = $arrayItem->key ?: $key;
      $items[$key] = $this->visitExpression($arrayItem->value);
    }
    return $items;
  }
  
  protected function createBody($stmts) {
    return new GFunctionBody();
  }
  
  protected function createType($type, $nodeValue) {
    if ($type === NULL) {
      if ($nodeValue instanceof PHPParser_Node_Scalar_String) {
        return new StringType();
      }
      
      return NULL;
    } elseif ($type instanceof PHPParser_Node_Name_FullyQualified) {
      return new ObjectType(new \Psc\Code\Generate\GClass($type->toString()));
    } elseif ($type == 'array') {
      return new ArrayType();
    }
    
    throw $this->nodeTypeError($type, __FUNCTION__);
  }
  
  protected function createModifiers($object) {
    $modifiers = 0x000000;
    
    if ($object->isPublic()) {
      $modifiers |= GModifiersObject::MODIFIER_PUBLIC;
    }

    if ($object->isProtected()) {
      $modifiers |= GModifiersObject::MODIFIER_PROTECTED;
    }

    if ($object->isPrivate()) {
      $modifiers |= GModifiersObject::MODIFIER_PRIVATE;
    }

    if ($object->isFinal()) {
      $modifiers |= GModifiersObject::MODIFIER_FINAL;
    }

    if ($object->isAbstract()) {
      $modifiers |= GModifiersObject::MODIFIER_ABSTRACT;
    }

    if ($object->isStatic()) {
      $modifiers |= GModifiersObject::MODIFIER_STATIC;
    }
    
    return $modifiers;
  }
  
  
  protected function nodeTypeError($node, $function) {
    return new \InvalidArgumentException('Unknown NodeType: '.get_class($node).' in Branch: '.$function);
  }
  
  public function getGClass() {
    return $this->gClass;
  }
}
?>