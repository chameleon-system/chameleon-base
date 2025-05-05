<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * class TGroupTableField is a subclass of TGroupTable. It is used to display one cell in the table
*/

class TGroupTableHeaderField extends TGroupTableField
{
    /**
     * if set to true, then the header allows resorting the table (default: true).
     *
     * @var bool
     */
    public $allowSort = true;

    /**
     * default: false.
     *
     * @var bool|int
     */
    public $width = false;

    /**
     * @param string|non-empty-array<string, string> $name - if it is an array it should be of the form 'name'=>'full_name'
     * @param string $align
     * @param string|null $format
     * @param int $colSpan
     * @param bool $allowSort
     * @param bool $width
     * @param string|null $sOriginalField
     */
    public function __construct($name, $align = 'left', $format = null, $colSpan = 1, $allowSort = true, $width = false, $sOriginalField = null)
    {
        // name may be a string, or an array. if it is an array it should be of the form 'name'=>'full_name'
        $linkField = null; // header cells cannot use linkFields
        parent::__construct($name, $align, $format, $linkField, $colSpan, null, null, $sOriginalField);
        $this->allowSort = $allowSort;
        $this->width = $width;
    }

    /**
     * @return string
     */
    public function _inTDCallback()
    {
        if (false !== $this->width) {
            return 'style="width:'.TGlobal::OutHTML($this->width).'px"';
        } else {
            return '';
        }
    }
}
