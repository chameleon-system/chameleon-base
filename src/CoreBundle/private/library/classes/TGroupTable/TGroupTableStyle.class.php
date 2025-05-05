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
 * class TGroupTableStyle is a subclass of TGroupTable. It is used to organize the formatting of the TGroupTable class
*/
class TGroupTableStyle
{
    use ChameleonSystem\CoreBundle\BackwardsCompatibilityShims\NamedConstructorSupport;

    public $group; // style class name for the group field (default = NULL)
    public $emptyGroup; // style to use for the fill td's in the group by row (default = NULL)
    public $groupSpacer; // style class name to use for the row between the groups (default = NULL)
    public $columnList; // assoc array holding column name => style class name (default = array);

    public function __construct()
    {
        $this->group = null;
        $this->emptyGroup = null;
        $this->groupSpacer = null;
        $this->oddRow = null;
        $this->evenRow = null;
        $this->oddColumn = null;
        $this->evenColumn = null;
        $this->columnList = [];
    }

    /**
     * @deprecated Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.
     * @see self::__construct
     */
    public function TGroupTableStyle()
    {
        $this->callConstructorAndLogDeprecation(func_get_args());
    }

    public function GetGroup()
    {
        $output = '';
        if (!is_null($this->group)) {
            $output = "class=\"{$this->group}\"";
        }

        return $output;
    }

    public function GetEmptyGroup()
    {
        $output = '';
        if (!is_null($this->emptyGroup)) {
            $output = "class=\"{$this->emptyGroup}\"";
        }

        return $output;
    }

    public function GetGroupSpacer()
    {
        $output = '';
        if (!is_null($this->groupSpacer)) {
            $output = "class=\"{$this->groupSpacer}\"";
        }

        return $output;
    }

    public function GetColumnList($column)
    {
        $output = '';
        if (array_key_exists($column, $this->columnList)) {
            $output = "class=\"{$this->columnList[$column]}\"";
        }

        return $output;
    }
}
