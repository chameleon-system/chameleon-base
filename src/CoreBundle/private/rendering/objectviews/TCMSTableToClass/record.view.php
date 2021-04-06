<?php echo "<?php\n";
/**
 * @var $oTableConf TdbCmsTblConf
 * @var $cmsConfig  TdbCmsConfig|TCMSConfig
 */
$translatableFields = array();
$oFields->GoToStart();
while ($oField = $oFields->Next()) {
    if (is_a($oField, 'stdClass')) {
        trigger_error('Error in field config of field '.$oField->name.' in table '.$oField->sTableName, E_USER_ERROR);
    } elseif (array_key_exists('is_translatable', $oField->oDefinition->sqlData) && '1' == $oField->oDefinition->sqlData['is_translatable']) {
        $translatableFields[] = $oField->oDefinition->sqlData['name'];
    }
}

?>
/**
* THIS FILE IS CREATED BY CHAMELEON. DO NOT CHANGE IT BY HAND! IT WILL BE OVERWRITTEN BY
* CHAMELEON ANYTIME A CHANGE IS MADE TO THE CONNECTED TABLE. IF YOU NEED TO MODIFY THE CLASS
* YOU MUST USE ITS EXTENSION IN CMSDataObjects
*/

/****************************************************************************
* Copyright <?=$year; ?> by ESONO AG, Freiburg, Germany
<?php
  if (is_array($aTableNotes)) {
      foreach ($aTableNotes as $sLine) {
          echo "  * {$sLine}\n";
      }
  }
?>
/***************************************************************************/
class <?=$sAutoClassName; ?> extends <?=$sParentClass; ?>

{
    private static $translatableFields = array(
<?php
    foreach ($translatableFields as $translatableField) {
        echo "      '".$translatableField."' => 1,\n";
    }
?>
    );

    /**
     * return the table id of the objects table conf object
     *
     * @return string
     */
    static public function GetOwningCmsTblConfId()
    {
        return '<?=$aTableConf['id']; ?>';
    }

    /**
     * return true if the frontend auto cache trigger clear is enabled - false if it is not
     * @return boolean
    */
    static public function isFrontendAutoCacheClearEnabled() {
        return <?php if (isset($aTableConf['frontend_auto_cache_clear_enabled']) && '1' == $aTableConf['frontend_auto_cache_clear_enabled']) {
    echo 'true';
} else {
    echo 'false';
} ?>;
    }

<?php
if (isset($aTableConf['frontend_auto_cache_clear_enabled']) && '0' == $aTableConf['frontend_auto_cache_clear_enabled']) {
    ?>
    /**
     * disabled because the frontend auto cache clear is disabled - these objects should not auto cache since their cache values are not auto disabled
     * @return boolean
    */
    public function GetEnableObjectCaching()
    {
        return false;
    }
<?php
}
?>

<?php

$oFields->GoToStart();
  while ($oField = $oFields->Next()) {
      if (is_a($oField, 'stdClass')) {
          trigger_error('Error in field config of field '.$oField->name.' in table '.$oField->sTableName, E_USER_ERROR);
      } else {
          $sProp = $oField->RenderFieldPropertyString();
          if (!empty($sProp)) {
              echo $sProp."\n";
          }
      }
  }
?>
    /**
     *
     *
     * @return boolean
     */
     protected function GetMltTableName($sFieldName,$sTableName,$bActiveRecordIsTarget=FALSE)
    {
        $sMLTTableName = '';
        if (substr($sTableName,-4)=='_mlt') {
            if (empty($sFieldName)) $sFieldName = $sTableName;
            $sTableName = substr($sTableName,0,-4);
        }
        if ($bActiveRecordIsTarget) {
            if (empty($sFieldName)) $sFieldName = $this->table.'_mlt';
            $sSourceClass = TCMSTableToClass::GetClassName('Tdb',$sTableName);
            $aFieldMapping = call_user_func(array($sSourceClass,'GetMLTFieldToTableMapping'));
        } else {
            if (empty($sFieldName)) $sFieldName = $sTableName.'_mlt';
            $aFieldMapping = self::GetMLTFieldToTableMapping();
        }
        if (array_key_exists($sFieldName, $aFieldMapping)) $sMLTTableName = $aFieldMapping[$sFieldName];
        else $sMLTTableName = parent::GetMltTableName($sFieldName,$sTableName,$bActiveRecordIsTarget);
        return $sMLTTableName;
    }

    /**
     *
     *
     * @return array
     */
     static public function GetMLTFieldToTableMapping()
    {
        $aFieldMapping = array(
    <?php

      $aMapping = array();
      $oFields->GoToStart();
      while ($oField = $oFields->Next()) {
          $oFieldType = $oField->oDefinition->GetFieldType();
          if ('mlt' == $oFieldType->sqlData['base_type']) {
              /** @var $oField TCMSFieldLookupMultiselect */
              $aMapping[$oField->oDefinition->sqlData['name']] = "'{$oField->oDefinition->sqlData['name']}'=>'".$oField->GetMLTTableName()."'";
          }
      }
          echo implode(', ', $aMapping);
    ?>
        );

        return $aFieldMapping;
    }

    /**
     *
     *
     * @return array
     */
    static public function GetMLTTargetListOrderBy($sMLTField)
    {
        $aFieldMapping = array(
    <?php

      /** @var \Doctrine\DBAL\Connection $databaseConnection */
      $aMapping = array();
      $oFields->GoToStart();
      while ($oField = $oFields->Next()) {
          $oFieldType = $oField->oDefinition->GetFieldType();
          if ('mlt' == $oFieldType->sqlData['base_type']) {
              $sOrderBy = '';
              /** @var $oField TCMSFieldLookupMultiselect */
              if (true == $oField->oDefinition->GetFieldtypeConfigKey('bAllowCustomSortOrder')) {
                  $sOrderBy = $databaseConnection->quoteIdentifier($oField->GetMLTTableName()).'.`entry_sort` ASC';
              } else {
                  $sTargetTable = $oField->GetConnectedTableName();
                  $query = 'SELECT `cms_tbl_display_orderfields`.*
                        FROM `cms_tbl_display_orderfields`
                  INNER JOIN `cms_tbl_conf` ON `cms_tbl_display_orderfields`.`cms_tbl_conf_id` = `cms_tbl_conf`.`id`
                       WHERE `cms_tbl_conf`.`name` = :targetTableName
                    ORDER BY `cms_tbl_display_orderfields`.`position` ASC
                     ';
                  $tRes = $databaseConnection->executeQuery($query, array('targetTableName' => $sTargetTable));
                  $aOrderByList = array();
                  while ($aOrder = $tRes->fetch(\PDO::FETCH_ASSOC)) {
                      $aOrderByList[] = "{$aOrder['name']} {$aOrder['sort_order_direction']}";
                  }
                  if (count($aOrderByList) > 0) {
                      $sOrderBy = implode(',', array_map(array($databaseConnection, 'quote'), $aOrderByList));
                  }
              }
              if (!empty($sOrderBy)) {
                  $aMapping[$oField->oDefinition->sqlData['name']] = '"'.$oField->oDefinition->sqlData['name'].'" => "'.$sOrderBy.'"';
              }
          }
      }
          echo implode(', ', $aMapping);
    ?>
        );
        if (array_key_exists($sMLTField, $aFieldMapping)) return $aFieldMapping[$sMLTField];
        else return false;
    }




    /**
     * return true if the field passed is marked as translatable (and field based translation is active)
     *
     * @return boolean
     */
    static public function CMSFieldIsTranslated($sFieldName)
    {
        return isset(self::$translatableFields[$sFieldName]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslatableFields()
    {
        return array_keys(self::$translatableFields);
    }

<?php
?>
    /**
     * factory creates a new instance and returns it.
     *
     * @param string|array $sData - either the id of the object to load, or the row with which the instance should be initialized
     * @param string $sLanguage - init with the language passed
     * @return <?=$sClassName; ?>
     */
    static public function GetNewInstance($sData = null, $sLanguage = null)
    {
        $oObject = new <?=$sClassName; ?>();
        if (!is_null($sLanguage)) {
            $oObject->SetLanguage($sLanguage);
        }
        if (!is_null($sData)) {
            if (is_array($sData)) {
                $oObject->LoadFromRow($sData);
            } else {
                $oObject->Load($sData);
            }
        }

        return $oObject;
    }

    /**
     * constructor
     *
     * @param string $id
     * @param string $sLanguageId
     */
    public function __construct($id=null,$sLanguageId=null)
    {
        $this->table = '<?=$sTableDBName; ?>';
        $this->id = $id;
        if (null !== $sLanguageId) {
            $this->SetLanguage($sLanguageId);
        }
        parent::__construct();
    }
<?php
  $aLines = array();
  $oFields->GoToStart();
  while ($oField = $oFields->Next()) {
      if (is_a($oField, 'stdClass')) {
          trigger_error('Error in field config of field '.$oField->name.' in table '.$oField->sTableName, E_USER_ERROR);
      } else {
          $sProp = $oField->RenderFieldPostWakeupString();
          if (!empty($sProp)) {
              $aLines[] = $sProp."\n";
          }
      }
  }
  if (count($aLines) > 0) {
      ?>
    protected function PostWakeUpHook()
    {
        parent::PostWakeUpHook();
<?php
    echo "\n";
      echo implode("    \n", $aLines);
      echo "\n"; ?>
    }
<?php
  }
?>

    protected function PostLoadHook() {
        parent::PostLoadHook();
        $oLocal = null;
      <?php
      $needsLanguageHandling = false;
      $oFields->GoToStart();
      if (true === ACTIVE_TRANSLATION) {
          while (!$needsLanguageHandling && ($oField = $oFields->Next())) {
              if ($oField->oDefinition && '1' == $oField->oDefinition->sqlData['is_translatable']) {
                  $needsLanguageHandling = true;
              }
          }
      }
      if ($needsLanguageHandling) {
          ?>
        $sActiveLanguagePrefix = '';
        $skipPrefixLoading = false;
<?php
        if ('TAdbCmsLocals' != $sAutoClassName) {
            ?>
        $oLocal =& TCMSLocal::GetActive();
        <?php
        }

          /**
           * Avoid endless recursion for TAdbCmsLanguage, but still enable language support for cms_language itself.
           */
          if ('TAdbCmsLanguage' === $sAutoClassName) {
              ?>
            static $languageLoading = false;

            if (true === $languageLoading) {
                $skipPrefixLoading = true;
            }
            $languageLoading = true;
            <?php
          } ?>
        if (false === $skipPrefixLoading) {
            $activeLanguageId = self::getLanguageService()->getActiveLanguageId();
            if (null === $this->iLanguageId || $activeLanguageId === $this->iLanguageId) {
                $languageId = $activeLanguageId;
            } else {
                $languageId = $this->iLanguageId;
            }
            $sActiveLanguagePrefix = TGlobal::GetLanguagePrefix($languageId);
        }
<?php
        if ('TAdbCmsLanguage' === $sAutoClassName) {
            ?>
            $languageLoading = false;
            <?php
        }
      }
?>
<?php
  $oFields->GoToStart();
  while ($oField = $oFields->Next()) {
      if (is_a($oField, 'stdClass')) {
          trigger_error('Error in field config of field '.$oField->name.' in table '.$oField->sTableName, E_USER_ERROR);
      } else {
          $sProp = $oField->RenderFieldPostLoadString();
          if (!empty($sProp)) {
              echo $sProp."\n";
          }
      }
  }
?>

    }

<?php
  $oFields->GoToStart();
  while ($oField = $oFields->Next()) {
      if (is_a($oField, 'stdClass')) {
          trigger_error('Error in field config of field '.$oField->name.' in table '.$oField->sTableName, E_USER_ERROR);
      } else {
          $sProp = $oField->RenderFieldMethodsString();
          if (!empty($sProp)) {
              echo $sProp."\n";
          }
      }
  }
?>

    /**
     * returns name string for the field
     *
     * @return string
     */
    public function GetName()
    {
        // handle the standard cases...

        $sContent = '';
        if ($this->sqlData !== false) {
            $sContent = $this->GetFromInternalCache('recordName');
            if (is_null($sContent)) {
                $sNameColumn = '<?php if (!empty($aTableConf['name_column'])) {
    echo $aTableConf['name_column'];
} else {
    echo 'name';
} ?>';
                $sActiveLanguagePrefix = '';
                $sAutoClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS,$this->table);
                $isMultiLanguageField = call_user_func(array($sAutoClassName,'CMSFieldIsTranslated'), $sNameColumn);
                if($isMultiLanguageField ) {
                    if(null === $this->GetLanguage()) {
                        $this->SetLanguage(self::getLanguageService()->getActiveLanguageId());
                    }
                    $sActiveLanguagePrefix = '__'.TGlobal::GetLanguagePrefix($this->GetLanguage());
                    // prefix only if array key exists
                    if (!array_key_exists($sNameColumn.$sActiveLanguagePrefix,$this->sqlData)) $sActiveLanguagePrefix = '';
                }
                $fieldExists = (is_array($this->sqlData) && array_key_exists($sNameColumn, $this->sqlData));
          <?php
          $sListQueryFiltered = str_replace('"', '\"', $aTableConf['list_query']);
          $sListQueryFiltered = str_replace('$$langID$$', '".$this->iLanguageId."', $sListQueryFiltered);
          ?>
                $sListQuery = "<?=$sListQueryFiltered; ?>";
                $databaseConnection = $this->getDatabaseConnection();
                // if it does not exist, we will need to try to fetch it using the query...
                if (!$fieldExists && !empty($sListQuery)) {

              <?php
              if (!stristr($sListQueryFiltered, 'WHERE ')) {
                  ?>
                    $listQuery = $sListQuery." WHERE ".$databaseConnection->quoteIdentifier($this->table).".`id` = ".$databaseConnection->quote($this->id);
              <?php
              } else {
                  $sListQueryFiltered = str_replace('WHERE ', 'WHERE ".$databaseConnection->quoteIdentifier($this->table).".`id` = ".$databaseConnection->quote($this->id)." AND ', $sListQueryFiltered); ?>
                    $listQuery = "<?=$sListQueryFiltered; ?>";
              <?php
              }
              ?>

                    if ($nameRecord = $databaseConnection->fetchAssoc($listQuery)) {
                        if (array_key_exists($sNameColumn, $nameRecord)) {
                            $this->sqlData[$sNameColumn] = $nameRecord[$sNameColumn];
                        }
                        $fieldExists = true;
                    } else {
                        // echo $listQuery;
                    }
                }

                if ($fieldExists) {
                    $sContent = $this->id;
                    if (array_key_exists($sNameColumn . $sActiveLanguagePrefix, $this->sqlData)) {
                        if ($this->sqlData[$sNameColumn . $sActiveLanguagePrefix] == '' && CMS_TRANSLATION_FIELD_BASED_EMPTY_TRANSLATION_FALLBACK_TO_BASE_LANGUAGE) {
                            $sContent = $this->sqlData[$sNameColumn];
                        } else {
                            $sContent = $this->sqlData[$sNameColumn . $sActiveLanguagePrefix];
                        }
                    }

                    $sNameFieldCallbackFunction = '<?=$aTableConf['name_column_callback']; ?>';
                    if (!empty($sNameFieldCallbackFunction)) {
                        TGlobal::LoadCallbackFunction($sNameFieldCallbackFunction);
                        $sContent = $sNameFieldCallbackFunction($sContent, $this->sqlData, $sNameColumn);
                    }
                } else {
                    $sContent = "<?=$aTableConf['translation']; ?>";
                }

                $this->SetInternalCache('recordName', $sContent);
            }
        }

        return $sContent;
    }

    /**
     * returns the name of the record modified to display it in breadcrumbs or anywhere
     * you may add icons, prefixes or whatever here
     *
     * @return string
     */
    public function GetDisplayValue()
    {
        $sContent = $this->GetFromInternalCache('recordDisplayName');
        if (is_null($sContent)) {
            $sContent = '';
            $displayColumn = '<?php echo $sDisplayColumnName; ?>';
            if (is_array($this->sqlData) && array_key_exists($displayColumn, $this->sqlData)) {
                $sContent = $this->sqlData[$displayColumn];
<?php
          if (!empty($sDisplayColumnCallbackFunctionName)) {
              ?>

                if (!function_exists('<?=$sDisplayColumnCallbackFunctionName; ?>')) TGlobal::LoadCallbackFunction('<?=$sDisplayColumnCallbackFunctionName; ?>');
                $sContent = <?=$sDisplayColumnCallbackFunctionName; ?>($sContent, $this->sqlData, $displayColumn);
<?php
          } ?>

            } else {
                $sContent = $this->sqlData['name'];
            }

            if(!stristr($sContent,'<') && !stristr($sContent,'>')) {
                if(mb_strlen($sContent)>50) {
                    $sContent = mb_substr($sContent,0,50)."...";
                }
            }

            $this->SetInternalCache('recordDisplayName',$sContent);
        }
        return $sContent;
    }
}
