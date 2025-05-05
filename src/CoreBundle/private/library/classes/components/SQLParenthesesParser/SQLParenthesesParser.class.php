<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class SQLParenthesesParser
{
    /**
     * level based string part stack.
     *
     * @var array|null
     */
    protected $aStack;

    /**
     * @var array|null
     */
    protected $current;

    /**
     * @var string
     */
    protected $sQuery = '';

    protected $iPosition;

    protected $iBufferStart;

    /**
     * all query parts not in a bracket.
     *
     * @var array
     */
    protected $aOuterQueryParts = [];

    /**
     * full query string length.
     *
     * @var int
     */
    protected $iLength = 0;

    /**
     * current level of brackets.
     *
     * @var int
     */
    protected $iCurrentLevel = 0;

    /**
     * parses the query string and returns multidimensional array
     * of all query parts split by brackets.
     *
     * @param string $sString
     *
     * @return array
     */
    public function parse($sString)
    {
        if (!$sString) {
            return [];
        }

        if ('(' == $sString[0]) {
            $sString = substr($sString, 1, -1);
        }

        $this->current = [];
        $this->aStack = [];

        $this->sQuery = $sString;
        $this->iLength = strlen($this->sQuery);

        for ($this->iPosition = 0; $this->iPosition < $this->iLength; ++$this->iPosition) {
            switch ($this->sQuery[$this->iPosition]) {
                case '(':
                    $this->push();
                    ++$this->iCurrentLevel;
                    array_push($this->aStack, $this->current);
                    $this->current = [];
                    break;

                case ')':
                    $this->push();
                    --$this->iCurrentLevel;
                    $t = $this->current;
                    $this->current = array_pop($this->aStack);
                    $this->current[] = $t;
                    break;

                default:
                    // save last outer query part
                    if ($this->iPosition == ($this->iLength - 1)) {
                        $this->push();
                    }

                    if (null === $this->iBufferStart) {
                        $this->iBufferStart = $this->iPosition;
                    }
            }
        }

        return $this->current;
    }

    /**
     * returns array of all query string parts not in brackets.
     *
     * @param string $sString
     *
     * @return array
     */
    public function getStringsNotInParentheses($sString)
    {
        $this->parse($sString);
        if (0 == count($this->current)) {
            $this->aOuterQueryParts[0] = $sString;
        }

        return $this->aOuterQueryParts;
    }

    protected function push()
    {
        if (null !== $this->iBufferStart) {
            $buffer = substr($this->sQuery, $this->iBufferStart, $this->iPosition - $this->iBufferStart);
            if (0 == $this->iCurrentLevel) {
                $this->aOuterQueryParts[$this->iBufferStart] = $buffer;
            }
            $this->iBufferStart = null;
            $this->current[] = $buffer;
        }
    }
}
