<?php

/*
 * Copyright (c) 2003 Jose Solorzano.  All rights reserved.
 * Redistribution of source must retain this copyright notice.
 *
 * Jose Solorzano (http://jexpert.us) is a software consultant.
 *
 * Contributions by:
 * - Leo West (performance improvements)
 */

define('NODE_TYPE_START', 0);
define('NODE_TYPE_ELEMENT', 1);
define('NODE_TYPE_ENDELEMENT', 2);
define('NODE_TYPE_TEXT', 3);
define('NODE_TYPE_COMMENT', 4);
define('NODE_TYPE_DONE', 5);

/**
 * Class HtmlParser.
 * To use, create an instance of the class passing
 * HTML text. Then invoke parse() until it's false.
 * When parse() returns true, $iNodeType, $iNodeName
 * $iNodeValue and $iNodeAttributes are updated.
 *
 * To create an HtmlParser instance you may also
 * use convenience functions HtmlParser_ForFile
 * and HtmlParser_ForURL.
 */
class HtmlParser
{
    /**
     * Field iNodeType.
     * May be one of the NODE_TYPE_* constants above.
     */
    public $iNodeType;

    /**
     * Field iNodeName.
     * For elements, it's the name of the element.
     */
    public $iNodeName = '';

    /**
     * Field iNodeValue.
     * For text nodes, it's the text.
     */
    public $iNodeValue = '';

    /**
     * Field iNodeAttributes.
     * A string-indexed array containing attribute values
     * of the current node. Indexes are always lowercase.
     */
    public $iNodeAttributes;
    // The following fields should be
    // considered private:
    public $iHtmlText;
    public $iHtmlTextLength;
    public $iHtmlTextIndex = 0;
    public $iHtmlCurrentChar;
    public $BOE_ARRAY;
    public $B_ARRAY;
    public $BOS_ARRAY;

    /**
     * Constructor.
     * Constructs an HtmlParser instance with
     * the HTML text given.
     */
    public function HtmlParser($aHtmlText)
    {
        $this->iHtmlText = $aHtmlText;
        $this->iHtmlTextLength = mb_strlen($aHtmlText);
        $this->iNodeAttributes = array();
        $this->setTextIndex(0);

        $this->BOE_ARRAY = array(' ', "\t", "\r", "\n", '=');
        $this->B_ARRAY = array(' ', "\t", "\r", "\n");
        $this->BOS_ARRAY = array(' ', "\t", "\r", "\n", '/');
    }

    /**
     * Method parse.
     * Parses the next node. Returns false only if
     * the end of the HTML text has been reached.
     * Updates values of iNode* fields.
     */
    public function parse()
    {
        $text = $this->skipToElement();
        if ('' != $text) {
            $this->iNodeType = NODE_TYPE_TEXT;
            $this->iNodeName = 'Text';
            $this->iNodeValue = $text;

            return true;
        }

        return $this->readTag();
    }

    public function clearAttributes()
    {
        $this->iNodeAttributes = array();
    }

    public function readTag()
    {
        if ('<' != $this->iCurrentChar) {
            $this->iNodeType = NODE_TYPE_DONE;

            return false;
        }
        $this->clearAttributes();
        $this->skipMaxInTag('<', 1);
        if ('/' == $this->iCurrentChar) {
            $this->moveNext();
            $name = $this->skipToBlanksInTag();
            $this->iNodeType = NODE_TYPE_ENDELEMENT;
            $this->iNodeName = $name;
            $this->iNodeValue = '';
            $this->skipEndOfTag();

            return true;
        }
        $name = $this->skipToBlanksOrSlashInTag();
        if (!$this->isValidTagIdentifier($name)) {
            $comment = false;
            if (0 === mb_strpos($name, '!--')) {
                $ppos = mb_strpos($name, '--', 3);
                if (mb_strpos($name, '--', 3) === (mb_strlen($name) - 2)) {
                    $this->iNodeType = NODE_TYPE_COMMENT;
                    $this->iNodeName = 'Comment';
                    $this->iNodeValue = '<'.$name.'>';
                    $comment = true;
                } else {
                    $rest = $this->skipToStringInTag('-->');
                    if ('' != $rest) {
                        $this->iNodeType = NODE_TYPE_COMMENT;
                        $this->iNodeName = 'Comment';
                        $this->iNodeValue = '<'.$name.$rest;
                        $comment = true;
                        // Already skipped end of tag
                        return true;
                    }
                }
            }
            if (!$comment) {
                $this->iNodeType = NODE_TYPE_TEXT;
                $this->iNodeName = 'Text';
                $this->iNodeValue = '<'.$name;

                return true;
            }
        } else {
            $this->iNodeType = NODE_TYPE_ELEMENT;
            $this->iNodeValue = '';
            $this->iNodeName = $name;
            while ($this->skipBlanksInTag()) {
                $attrName = $this->skipToBlanksOrEqualsInTag();
                if ('' != $attrName && '/' != $attrName) {
                    $this->skipBlanksInTag();
                    if ('=' == $this->iCurrentChar) {
                        $this->skipEqualsInTag();
                        $this->skipBlanksInTag();
                        $value = $this->readValueInTag();
                        $this->iNodeAttributes[mb_strtolower($attrName)] = $value;
                    } else {
                        $this->iNodeAttributes[mb_strtolower($attrName)] = '';
                    }
                }
            }
        }
        $this->skipEndOfTag();

        return true;
    }

    public function isValidTagIdentifier($name)
    {
        return preg_match('/^[A-Za-z0-9_\\-]+$/', $name);
    }

    public function skipBlanksInTag()
    {
        return '' != ($this->skipInTag($this->B_ARRAY));
    }

    public function skipToBlanksOrEqualsInTag()
    {
        return $this->skipToInTag($this->BOE_ARRAY);
    }

    public function skipToBlanksInTag()
    {
        return $this->skipToInTag($this->B_ARRAY);
    }

    public function skipToBlanksOrSlashInTag()
    {
        return $this->skipToInTag($this->BOS_ARRAY);
    }

    public function skipEqualsInTag()
    {
        return $this->skipMaxInTag('=', 1);
    }

    public function readValueInTag()
    {
        $ch = $this->iCurrentChar;
        $value = '';
        if ('"' == $ch) {
            $this->skipMaxInTag('"', 1);
            $value = $this->skipToInTag('"');
            $this->skipMaxInTag('"', 1);
        } else {
            if ("'" == $ch) {
                $this->skipMaxInTag("'", 1);
                $value = $this->skipToInTag("'");
                $this->skipMaxInTag("'", 1);
            } else {
                $value = $this->skipToBlanksInTag();
            }
        }

        return $value;
    }

    public function setTextIndex($index)
    {
        $this->iHtmlTextIndex = $index;
        if ($index >= $this->iHtmlTextLength) {
            $this->iCurrentChar = -1;
        } else {
            $this->iCurrentChar = $this->iHtmlText[$index];
        }
    }

    public function moveNext()
    {
        if ($this->iHtmlTextIndex < $this->iHtmlTextLength) {
            $this->setTextIndex($this->iHtmlTextIndex + 1);

            return true;
        } else {
            return false;
        }
    }

    public function skipEndOfTag()
    {
        while (-1 !== ($ch = $this->iCurrentChar)) {
            if ('>' == $ch) {
                $this->moveNext();

                return;
            }
            $this->moveNext();
        }
    }

    public function skipInTag($chars)
    {
        $sb = '';
        while (-1 !== ($ch = $this->iCurrentChar)) {
            if ('>' == $ch) {
                return $sb;
            } else {
                $match = false;
                for ($idx = 0; $idx < count($chars); ++$idx) {
                    if ($ch == $chars[$idx]) {
                        $match = true;
                        break;
                    }
                }
                if (!$match) {
                    return $sb;
                }
                $sb .= $ch;
                $this->moveNext();
            }
        }

        return $sb;
    }

    public function skipMaxInTag($chars, $maxChars)
    {
        $sb = '';
        $count = 0;
        while (-1 !== ($ch = $this->iCurrentChar) && $count++ < $maxChars) {
            if ('>' == $ch) {
                return $sb;
            } else {
                $match = false;
                for ($idx = 0; $idx < count($chars); ++$idx) {
                    if ($ch == $chars[$idx]) {
                        $match = true;
                        break;
                    }
                }
                if (!$match) {
                    return $sb;
                }
                $sb .= $ch;
                $this->moveNext();
            }
        }

        return $sb;
    }

    public function skipToInTag($chars)
    {
        $sb = '';
        while (-1 !== ($ch = $this->iCurrentChar)) {
            $match = '>' == $ch;
            if (!$match) {
                for ($idx = 0; $idx < count($chars); ++$idx) {
                    if ($ch == $chars[$idx]) {
                        $match = true;
                        break;
                    }
                }
            }
            if ($match) {
                return $sb;
            }
            $sb .= $ch;
            $this->moveNext();
        }

        return $sb;
    }

    public function skipToElement()
    {
        $sb = '';
        while (-1 !== ($ch = $this->iCurrentChar)) {
            if ('<' == $ch) {
                return $sb;
            }
            $sb .= $ch;
            $this->moveNext();
        }

        return $sb;
    }

    /**
     * Returns text between current position and $needle,
     * inclusive, or "" if not found. The current index is moved to a point
     * after the location of $needle, or not moved at all
     * if nothing is found.
     */
    public function skipToStringInTag($needle)
    {
        $pos = strpos($this->iHtmlText, $needle, $this->iHtmlTextIndex);
        if (false === $pos) {
            return '';
        }
        $top = $pos + mb_strlen($needle);
        $retvalue = substr($this->iHtmlText, $this->iHtmlTextIndex, $top - $this->iHtmlTextIndex);
        $this->setTextIndex($top);

        return $retvalue;
    }
}

function HtmlParser_ForFile($fileName)
{
    return HtmlParser_ForURL($fileName);
}

function HtmlParser_ForURL($url)
{
    $fp = fopen($url, 'r');
    $content = '';
    while (true) {
        $data = fread($fp, 8192);
        if (0 == mb_strlen($data)) {
            break;
        }
        $content .= $data;
    }
    fclose($fp);

    return new HtmlParser($content);
}
