<?php

namespace Webforge\Code\Generator;

use PHPParser_Lexer;

class LexerWithTokenOffsets extends PHPParser_Lexer {
    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null) {
        $tokenId = parent::getNextToken($value, $startAttributes, $endAttributes);
        $startAttributes['startOffset'] = $endAttributes['endOffset'] = $this->pos;
        return $tokenId;
    }
}
?>