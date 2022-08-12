<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\Event\DisplayListmanagerCellEvent;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * class TGroupTableField is a subclass of TGroupTable. It is used to display one cell in the table.
 *
 * /**/
class TGroupTableField
{
    /**
     * Sql field name. If this is an array it should be of the form 'name'=>'full_name'.
     *
     * @var array|string
     */
    public $name = '';

    /**
     * Full db field name (like B.`name`) default = same as $name.
     *
     * @var string
     */
    public $full_db_name = '';

    /**
     * Left, center, right.
     *
     * @var string
     */
    public $align = 'left';

    /**
     * Callback formatting function, receiving the cell value and row array.
     *
     * @var null|callable(string, array<string, mixed>, string):(string|null)
     */
    public $format;

    /**
     * Array of db fields to return from an onClick. can also be just one field name (ie. string).
     *
     * @var array|string|null
     */
    public $linkFields;

    /**
     * Number of columns to cover in this column.
     *
     * @var int
     */
    public $colSpan = 1;

    /**
     * Callback function for option titles.
     *
     * @var string|null
     */
    public $selectFormat;

    /**
     * Identifier of the field (needed to be able to delete a field in table extensions).
     *
     * @var string|null
     */
    public $sIdent;

    /**
     * Holds the original fieldname before it was transformed for field based translations.
     *
     * @var string|null
     */
    public $sOriginalField;

    /**
     * Holds original table name for reverse lookups.
     *
     * @var string|null
     */
    public $originalTable;

    /**
     * Table cell configuration.
     *
     * @param string|non-empty-array<string, string>  $name - if it is an array it should be of the form 'name'=>'full_name'
     * @param string        $align
     * @param callable(string, array<string, mixed>, string):(string|null) $formatCallBack - a callback function to use for the column (the function will get 3 parameters,
     *                                 the value, the row and the name. The string returned by the function will be displayed
     *                                 in the cell
     * @param array|null    $linkFields
     * @param int           $colSpan
     * @param object|null   $selectFormat   - callback function for option titles
     * @param string|null   $ident
     * @param string|null   $originalField
     * @param string|null   $originalTable
     */
    public function __construct($name, $align = 'left', $formatCallBack = null, $linkFields = null, $colSpan = 1, $selectFormat = null, $ident = null, $originalField = null, $originalTable = null)
    {
        if (is_array($name)) {
            $tkey = array_keys($name);
            $key = $tkey[0];
            $this->name = $key;
            $this->full_db_name = $name[$key];
        } else {
            $this->name = $name;
            $this->full_db_name = $this->name;
        }
        if (null === $originalField) {
            $originalField = $this->name;
        }
        $this->originalTable = $originalTable;
        $this->sOriginalField = $originalField;
        $this->align = $align;
        $this->format = $formatCallBack;
        $this->linkFields = $linkFields;
        $this->colSpan = $colSpan;
        $this->selectFormat = $selectFormat;
        if (null !== $ident) {
            $this->sIdent = $ident;
        } else {
            $this->sIdent = $this->name;
        }
    }

    /**
     * @param string $sCellValue
     *
     * @return string
     */
    protected function EscapeCellValue($sCellValue)
    {
        return TGlobal::OutHTML($sCellValue);
    }

    /**
     * return cell HTML.
     *
     * @param array<string, mixed>  $row           - assoc array of data for that row
     * @param string $style         - the style class to apply
     * @param string $onClickEvent  - the js function to call onClick
     * @param bool   $isTableHeader
     *
     * @return string
     */
    public function Display($row, $style, $onClickEvent = null, $isTableHeader = false)
    {
        $cellValue = $this->getCellValue($row, $isTableHeader);
        list($linkFields, $linkEvent) = $this->getLinkData($row, $onClickEvent);

        $style = str_replace('class="', '', $style);
        $style = str_replace('"', '', $style);

        return $this->getCellHtml($row, $isTableHeader, $linkEvent, $style, $cellValue, $linkFields);
    }

    /**
     * @param array<string, mixed> $row
     * @param bool  $isTableHeader
     *
     * @return string
     */
    private function getCellValue(array $row, $isTableHeader)
    {
        if (null === $this->name || array_key_exists($this->name, $row)) {
            if (null !== $this->name) {
                $cellValue = $row[$this->name];
            } else {
                $cellValue = '';
            }
            if (null === $this->format) {
                if (false === $isTableHeader) {
                    $cellValue = $this->EscapeCellValue($cellValue);
                }
            } else {
                $cellValue = $this->getFormatBasedCellValueForDisplayString($row, $cellValue);
            }
        } else {
            $cellValue = sprintf('[Error: unable to find column ({%s})]', $this->name);
        }

        if (CMS_TRANSLATION_FIELD_BASED_EMPTY_TRANSLATION_FALLBACK_TO_BASE_LANGUAGE) {
            $cellValue = $this->getFormattedValueForDisplayString($row, $cellValue);
        }

        return $cellValue;
    }

    /**
     * @param array<string, mixed> $row
     * @param string $onClickEvent
     *
     * @return array
     */
    private function getLinkData(array $row, $onClickEvent)
    {
        // Check for presence of a link field.
        $linkFields = null;
        $linkEvent = null;

        if (\is_array($this->linkFields)) {
            if (\count($this->linkFields) > 0) {
                $linkFields = $this->linkFields;
            }
        } elseif (false === empty($this->linkFields)) {
            $linkFields = [$this->linkFields];
        }

        if (null !== $linkFields && null !== $onClickEvent) {
            $linkEvent = $onClickEvent.'(';
            $isFirst = true;
            foreach ($linkFields as $linkField) {
                if ($isFirst) {
                    $isFirst = false;
                } else {
                    $linkEvent .= ', ';
                }
                $linkEvent .= "'".$this->getLinkField($row, $linkField)."'";
            }
            $linkEvent .= ');';
        }

        return [
            $linkFields,
            $linkEvent,
        ];
    }

    /**
     * @param array<string, mixed> $row
     * @param bool   $isTableHeader
     * @param string $linkEvent
     * @param string $style
     * @param string $cellValue
     *
     * @return string
     */
    private function getCellHtml(array $row, $isTableHeader, $linkEvent, $style, $cellValue, $linkFields)
    {
        $event = new DisplayListmanagerCellEvent($this, $row, $isTableHeader);
        $event->setAttributes($this->getAttributes());
        $event->setOnclickEvent($linkEvent);
        $event->setCssClasses(\explode(' ', $style));
        $event->setCellValue($cellValue);

        $this->getEventDispatcher()->dispatch($event, CoreEvents::DISPLAY_LISTMANAGER_CELL);

        $tag = true === $event->isHeader() ? 'th' : 'td';
        $onclick = $event->getOnclickEvent();

        $cellValue = $event->getCellValue();
        if (null === $onclick && null !== $linkFields && 1 === \count($linkFields)) {
            $cellValue = '<a href="'.$this->getDetailLinkURL($row, $linkFields[0]).'" target="_top" class="TGroupTableLink">'.$cellValue.'</a>';
        }
        if (null !== $onclick && '' !== $onclick) {
            $onclick = "onclick=\"$onclick\"";
        }

        return \sprintf(
            '<%s %s %s %s class="%s">%s</%s>',
            $tag,
            $this->formatAttributes($event->getAttributes()),
            $onclick,
            $this->_inTDCallback(),
            \implode(' ', $event->getCssClasses()),
            $cellValue,
            $tag
        );
    }

    /**
     * @param array $attributes
     *
     * @return string
     */
    private function formatAttributes(array $attributes)
    {
        $cell = '';
        foreach ($attributes as $key => $value) {
            if (null === $value || false === $value || '' === $value) {
                continue;
            }
            $cell .= TGlobal::OutHTML($key);
            if (true !== $value) {
                $cell .= '="'.TGlobal::OutHTML($value).'"';
            }
            $cell .= ' ';
        }

        return $cell;
    }

    /**
     * @return array
     */
    private function getAttributes()
    {
        $attributes = [
            'align' => $this->align,
        ];

        if ($this->colSpan > 1) {
            $attributes['colspan'] = $this->colSpan;
        }

        return $attributes;
    }

    /**
     * @param array<string, mixed> $row
     * @param string $linkField
     *
     * @return string
     */
    protected function getDetailLinkURL($row, $linkField)
    {
        $id = $this->getLinkField($row, $linkField);

        /*
         * The static variable in the following line improves performance quite significantly as this method is called
         * once per table cell and therefore dozens of times per backend list request.
         * In a better world, user input would be read once and then passed on to where they are used, but this is not
         * possible here without major changes.
         */
        static $baseUrl = null;
        if (null !== $baseUrl) {
            return $baseUrl.TGlobal::OutHTML($id);
        }

        list($tableID, $sRestrictionField, $sRestriction, $bIsLoadedFromIFrame, $_isiniframe) = $this->getUserInput();

        $baseUrl = PATH_CMS_CONTROLLER.'?pagedef=tableeditor&tableid='.TGlobal::OutHTML($tableID);

        if (!empty($sRestrictionField)) {
            $baseUrl .= '&sRestrictionField='.TGlobal::OutHTML($sRestrictionField);
        }

        if (!empty($sRestriction)) {
            $baseUrl .= '&sRestriction='.TGlobal::OutHTML($sRestriction);
        }

        if (!empty($bIsLoadedFromIFrame)) {
            $baseUrl .= '&bIsLoadedFromIFrame='.TGlobal::OutHTML($bIsLoadedFromIFrame);
        }

        if (!empty($_isiniframe)) {
            $baseUrl .= '&_isiniframe='.TGlobal::OutHTML($_isiniframe);
        }

        $baseUrl .= '&id=';

        return $baseUrl.TGlobal::OutHTML($id);
    }

    /**
     * Returns the user input required to display table cells.
     *
     * The static variables improve performance quite significantly as every table cell requires this data, which leads
     * to dozens or hundreds of calls per backend list request.
     * In a better world, user input would be read once and then passed on to where they are used, but this is not
     * possible here without major changes.
     *
     * @return array
     */
    private function getUserInput()
    {
        $inputFilterUtil = $this->getInputFilterUtil();

        static $tableId = null;
        if (null === $tableId) {
            $tableId = $inputFilterUtil->getFilteredInput('id');
        }

        static $restrictionField = null;
        if (null === $restrictionField) {
            $restrictionField = $inputFilterUtil->getFilteredInput('sRestrictionField', '');
        }

        static $restriction = null;
        if (null === $restriction) {
            $restriction = $inputFilterUtil->getFilteredInput('sRestriction', '');
        }

        static $isLoadedFromIFrame = null;
        if (null === $isLoadedFromIFrame) {
            $isLoadedFromIFrame = $inputFilterUtil->getFilteredInput('bIsLoadedFromIFrame', '');
        }

        static $isInIFrame = null;
        if (null === $isInIFrame) {
            $isInIFrame = $inputFilterUtil->getFilteredInput('_isiniframe', '');
        }

        return [
            $tableId,
            $restrictionField,
            $restriction,
            $isLoadedFromIFrame,
            $isInIFrame,
        ];
    }

    /**
     * @param array<string, mixed>  $row
     * @param string $cellValue
     *
     * @return string
     */
    private function getFormatBasedCellValueForDisplayString(array $row, $cellValue)
    {
        $format = $this->format;
        $name = $this->name;
        if (is_array($format)) {
            $classNameOrObject = $format[0];
            $callbackFunctionName = $format[1];
            $className = true === \is_string($classNameOrObject) ? $classNameOrObject : \get_class($classNameOrObject);

            if (false === is_callable([$classNameOrObject, $callbackFunctionName])) {
                return sprintf('[Error: function "%s" is not present or callable in "%s" for field "%s"]', $callbackFunctionName, $className, $name);
            }

            return call_user_func([$classNameOrObject, $callbackFunctionName], $cellValue, $row, $name);
        }

        if (function_exists($format)) {
            return call_user_func($format, $cellValue, $row, $name);
        }

        return sprintf('[Error: callback function "%s" does not exist for field "%s"]', $format, $name);
    }

    /**
     * @param array<string, mixed> $row
     * @param string $cellValue
     *
     * @return string
     */
    private function getFormattedValueForDisplayString(array $row, $cellValue)
    {
        // Try to load value from default language if value is empty.
        if ('' === $cellValue
            && $this->full_db_name !== $this->sOriginalField
            && $this->name !== $this->sOriginalField
            && array_key_exists($this->sOriginalField, $row)
            && '' !== $row[$this->sOriginalField]
        ) {
            if (null !== $this->format && function_exists($this->format)) {
                $cellValue = call_user_func($this->format, $row[$this->sOriginalField], $row, $this->sOriginalField);
            } else {
                $cellValue = $this->EscapeCellValue($row[$this->sOriginalField]);
            }
            $cellValue .= ' <i class="fas fa-language" title="'.TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.list.default_language')).'"></i>';
        }

        return $cellValue;
    }

    /**
     * @return string
     */
    protected function _inTDCallback()
    {
        return '';
    }

    /**
     * @param array<string, mixed> $row
     * @param string $linkField
     *
     * @return string
     */
    private function getLinkField(array $row, $linkField)
    {
        if (array_key_exists($linkField, $row)) {
            $row[$linkField] = preg_replace("/(\r\n|\n|\r)/", '\\n', $row[$linkField]); // cross-platform newlines
            return addslashes($row[$linkField]);
        }

        return "Error: field ({$linkField}) does not exist";
    }

    /**
     * @return EventDispatcherInterface
     */
    private function getEventDispatcher()
    {
        static $eventDispatcher = null;
        if (null === $eventDispatcher) {
            $eventDispatcher = ServiceLocator::get('event_dispatcher');
        }

        return $eventDispatcher;
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}
