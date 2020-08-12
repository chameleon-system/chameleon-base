<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * allow mlt selection of fields. Target table is defined via sShowFieldsFromTable.
/**/
class TCMSFieldLookupMultiselectCheckboxesSelectFieldsFromTable extends TCMSFieldLookupMultiselectCheckboxes
{
    protected function GetMLTFilterQuery()
    {
        $filterQuery = parent::GetMLTFilterQuery();

        // NOTE these conditions are _very_ specific to the current configuration of cms_field_conf

        if (false !== \strpos($filterQuery, 'AS tabname') && false !== \strpos($filterQuery, 'cms_field_conf')) {
            $orderClauseCheck = 'ORDER BY position ASC';
            $orderPosition = \strpos($filterQuery, $orderClauseCheck);

            if (false !== $orderPosition) {
                // NOTE translations are handled later (EntityList?)
                $filterQuery = \str_replace($orderClauseCheck, 'ORDER BY tabname ASC, `cms_field_conf`.`translation` ASC', $filterQuery);
            }
        }

        return $filterQuery;
    }

    protected function GetMLTRecordRestrictions()
    {
        $sRestriction = parent::GetMLTRecordRestrictions();

        $sShowFieldsFromTable = $this->oDefinition->GetFieldtypeConfigKey('sShowFieldsFromTable');
        if (!empty($sShowFieldsFromTable)) {
            $oTablConf = TdbCmsTblConf::GetNewInstance();
            /** @var $oTablConf TdbCmsTblConf */
            if ($oTablConf->LoadFromField('name', $sShowFieldsFromTable)) {
                $sTmpRestriction = "(`cms_field_conf`.`cms_tbl_conf_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oTablConf->id)."')";
                $sRestriction .= ' AND '.$sTmpRestriction;
            }
        }

        return $sRestriction;
    }

    /**
     * returns true if field data is not empty
     * overwrite this method for mlt and property fields.
     *
     * @return bool
     */
    public function HasContent()
    {
        $bHasContent = false;
        if (is_array($this->data)) {
            if (array_key_exists('x', $this->data)) {
                if (count($this->data) > 1) {
                    $bHasContent = true;
                }
            } else {
                $bHasContent = true;
            }
        }

        return $bHasContent;
    }

    /**
     * {@inheritdoc}
     */
    protected function isRecordCreationAllowed(string $foreignTableName): bool
    {
        return false; // Would make no sense (no use-case) here: Create a new field in cms_field_conf.
    }
}
