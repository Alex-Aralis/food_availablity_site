<?php
/**
 * BracketProcessor.php
 *
 * This file implements the processor for the parentheses around the statements.
 * 
 * PHP version 5
 *
 * LICENSE:
 * Copyright (c) 2010-2014 Justin Swanhart and André Rothe
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 * NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author    André Rothe <andre.rothe@phosco.info>
 * @copyright 2010-2014 Justin Swanhart and André Rothe
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version   SVN: $Id: BracketProcessor.php 1317 2014-04-15 08:26:14Z phosco@gmx.de $
 *
 */

namespace PHPSQLParser\processors;
use PHPSQLParser\utils\ExpressionType;

require_once dirname(__FILE__) . '/../utils/ExpressionType.php';
require_once dirname(__FILE__) . '/DefaultProcessor.php';
require_once dirname(__FILE__) . '/AbstractProcessor.php';

/**
 * This class processes the parentheses around the statement.
 * 
 * @author  André Rothe <andre.rothe@phosco.info>
 * @license http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * 
 */
class BracketProcessor extends AbstractProcessor {

    protected function processTopLevel($sql) {
        $processor = new DefaultProcessor();
        return $processor->process($sql);
    }

    public function process($tokens) {

        $token = $this->removeParenthesisFromStart($tokens[0]);
        $subtree = $this->processTopLevel($token);

        if (isset($subtree['BRACKET'])) {
            // TODO: here we lose some other parts of a statement like ORDER BY
            $subtree = $subtree['BRACKET'];
        }

        if (isset($subtree['SELECT'])) {
            $subtree = array(
                    array('expr_type' => ExpressionType::QUERY, 'base_expr' => $token, 'sub_tree' => $subtree));
        }

        return array(
                array('expr_type' => ExpressionType::BRACKET_EXPRESSION, 'base_expr' => trim($tokens[0]),
                        'sub_tree' => $subtree));
    }

}

?>
