<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgExtranetMapper_FormLoginAndPassword extends AbstractPkgExtranetMapper_User
{
    /**
     * A mapper has to specify its requirements by providing th passed MapperRequirements instance with the
     * needed information and returning it.
     *
     * example:
     *
     * $oRequirements->NeedsSourceObject("foo",'stdClass','default-value');
     * $oRequirements->NeedsSourceObject("bar");
     * $oRequirements->NeedsMappedValue("baz");
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        parent::GetRequirements($oRequirements);
    }

    /**
     * To map values from models to views the mapper has to implement iVisitable.
     * The ViewRender will pass a prepared MapeprVisitor instance to the mapper.
     *
     * The mapper has to fill the values it is responsible for in the visitor.
     *
     * example:
     *
     * $foo = $oVisitor->GetSourceObject("foomodel")->GetFoo();
     * $oVisitor->SetMapperValue("foo", $foo);
     *
     *
     * To be able to access the desired source object in the visitor, the mapper has
     * to declare this requirement in its GetRequirements method (see IViewMapper)
     *
     * @param bool $bCachingEnabled - if set to true, you need to define your cache trigger that invalidate the view rendered via mapper. if set to false, you should NOT set any trigger
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        /** @var TdbDataExtranetUser $oUser */
        $oUser = $oVisitor->GetSourceObject('oUser');
        $aFieldList = $this->GetFieldList();
        $this->SetInputFields($aFieldList, $oVisitor, $oUser);
    }

    /**
     * @return array
     */
    protected function GetFieldList()
    {
        $aFieldList = ['aFieldLogin' => 'name',
                            'aFieldPassword' => 'password',
                            'aFieldPassword2' => 'password2',
                            'aFieldBirthday' => 'birthdate',
        ];

        return $aFieldList;
    }

    /**
     * set errors and values for given field list.
     *
     * @param array $aFieldList (MappedFieldName(name used in template) => RealFieldName (user input field name) )
     * @param IMapperVisitorRestricted $oVisitor
     * @param TdbDataExtranetUser $oUser
     * @param string $sFieldType
     * @param TCMSRecordList $oFieldOptionList
     *
     * @internal param string $sMSGConsumer
     *
     * @return void
     */
    protected function SetInputFields($aFieldList, $oVisitor, $oUser, $sFieldType = 'text', $oFieldOptionList = null)
    {
        foreach ($aFieldList as $sMappedFieldName => $sRealFieldName) {
            $aField = [];
            switch ($sFieldType) {
                case 'radio':
                case 'select':
                    $aField['sValue'] = (isset($oUser->sqlData[$sRealFieldName])) ? ($oUser->sqlData[$sRealFieldName]) : ('');
                    $aField['sError'] = $this->GetMessageForField($sRealFieldName, TdbDataExtranetUser::MSG_FORM_FIELD);
                    $aField['aValueList'] = [];
                    if (!is_null($oFieldOptionList)) {
                        while ($oFieldOption = $oFieldOptionList->Next()) {
                            $aFieldOption = [];
                            $aFieldOption['sName'] = $oFieldOption->GetName();
                            $aFieldOption['sValue'] = $oFieldOption->id;
                            $aField['aValueList'][] = $aFieldOption;
                        }
                    }
                    break;
                default:
                case 'text':
                    $aField['sError'] = $this->GetMessageForField($sRealFieldName, TdbDataExtranetUser::MSG_FORM_FIELD);
                    $aField['sValue'] = '';
                    if (is_array($oUser->sqlData) && isset($oUser->sqlData[$sRealFieldName])) {
                        $aField['sValue'] = $oUser->sqlData[$sRealFieldName];
                    }
                    break;
            }
            $oVisitor->SetMappedValue($sMappedFieldName, $aField);
        }
    }

    /**
     * Set error message for given field from message manager.
     *
     * @param string $sFieldName
     * @param string $sCustomMSGConsumer
     *
     * @return string
     */
    protected function GetMessageForField($sFieldName, $sCustomMSGConsumer)
    {
        $sMessage = '';
        $oMsgManager = TCMSMessageManager::GetInstance();
        if ($oMsgManager->ConsumerHasMessages($sCustomMSGConsumer.'-'.$sFieldName)) {
            $sMessage = $oMsgManager->RenderMessages($sCustomMSGConsumer.'-'.$sFieldName);
        }

        return $sMessage;
    }
}
