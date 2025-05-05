<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsStringUtilities_CleanPHPCode
{
    /**
     * @param string $code
     *
     * @return string
     */
    public function stripComments($code)
    {
        $newStr = '';

        $commentTokens = [T_COMMENT];

        if (defined('T_DOC_COMMENT')) {
            $commentTokens[] = T_DOC_COMMENT; // PHP 5
        }
        if (defined('T_ML_COMMENT')) {
            $commentTokens[] = T_ML_COMMENT;  // PHP 4
        }

        $tokens = token_get_all($code);

        foreach ($tokens as $token) {
            if (is_array($token)) {
                if (in_array($token[0], $commentTokens)) {
                    continue;
                }

                $token = $token[1];
            }

            $newStr .= $token;
        }

        return $newStr;
    }
}
