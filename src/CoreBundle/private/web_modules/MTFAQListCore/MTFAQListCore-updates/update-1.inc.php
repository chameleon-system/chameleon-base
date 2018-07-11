<?php
if (TCMSLogChange::AllowTransaction(1, 'dbversion-MTFAQListCore', 'update counter for faq-module')) {
    ?>
<h1>Chameleon MTFAQListCore Build #1</h1>
<h2>Date: 2011-12-27</h2>
<div class="changelog" style="margin-top: 20px; margin-bottom: 20px;">
    - add basic tables
    <div style="padding: 15px;"></div>
</div>
<?php
    if (!TCMSLogChange::TableExists('module_faq')) {
        $query = "CREATE TABLE `module_faq` (
                        `id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                        `cmsident` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Key is used so that records can be easily identified in Chameleon',
                        PRIMARY KEY ( `id` ),
                        UNIQUE (`cmsident`)
                      ) ENGINE = InnoDB";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = "INSERT INTO `cms_tbl_conf` SET `name` = 'module_faq', `dbobject_type` = 'Customer', `translation` = 'FAQ', `cms_tbl_field_tab` = 'cms_tbl_field_tab', `list_query` = '', `cms_content_box_id` = '', `only_one_record_tbl` = '0', `is_multilanguage` = '0', `locking_active` = '0', `changelog_active` = '0', `cms_field_conf_mlt` = 'cms_field_conf', `property_list_fields` = 'cms_tbl_display_list_fields', `property_order_fields` = 'cms_tbl_display_orderfields', `name_column` = '', `name_column_callback` = '', `display_column` = '', `display_column_callback` = '', `list_group_field` = '', `list_group_field_header` = '', `list_group_field_column` = '', `cms_tbl_list_class` = 'cms_tbl_list_class', `cms_tbl_list_class_id` = '', `table_editor_class` = '', `table_editor_class_subtype` = '', `table_editor_class_type` = 'Core', `cms_tbl_conf_restrictions` = 'cms_tbl_conf_restrictions', `icon_list` = 'page_script.gif', `show_previewbutton` = '0', `cms_tpl_page_id` = '', `rename_on_copy` = '0', `cms_usergroup_id` = '".TCMSLogChange::GetUserGroupIdByKey('website_editor')."', `notes` = '', `dbobject_extend_class` = 'TCMSRecord', `dbobject_extend_subtype` = 'dbobjects', `dbobject_extend_type` = 'Core', `cms_tbl_extension` = 'cms_tbl_extension', `auto_limit_results` = '-1', `cms_tbl_conf_index` = 'cms_tbl_conf_index'";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = "ALTER TABLE `module_faq` COMMENT = 'FAQ:\\n'";
        TCMSLogChange::_RunQuery($query, __LINE__);

        TCMSLogChange::SetTableRolePermissions('editor', 'module_faq', true, array(0, 1, 2, 3, 4, 6));

        $query = "INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = '".TCMSLogChange::GetTableId('module_faq')."', `name` = 'cms_tpl_module_instance_id', `translation` = 'Modulverknüpfung', `cms_field_type_id` = '".TCMSLogChange::GetFieldType('CMSFIELD_PROPERTY_PARENT_ID')."', `cms_tbl_field_tab` = '', `isrequired` = '0', `fieldclass` = '', `fieldclass_subtype` = '', `class_type` = 'Core', `modifier` = 'readonly', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '1', `049_helptext` = '', `row_hexcolor` = '', `is_translatable` = '0', `validation_regex` = ''";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = "ALTER TABLE `module_faq`
                              ADD `cms_tpl_module_instance_id` CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Modulverknüpfung: '";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = 'ALTER TABLE `module_faq` ADD INDEX ( `cms_tpl_module_instance_id` ) ';
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = "INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = '".TCMSLogChange::GetTableId('module_faq')."', `name` = 'name', `translation` = 'Titel', `cms_field_type_id` = '".TCMSLogChange::GetFieldType('CMSFIELD_STRING')."', `cms_tbl_field_tab` = '', `isrequired` = '1', `fieldclass` = '', `fieldclass_subtype` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '2', `049_helptext` = '', `row_hexcolor` = '', `is_translatable` = '0', `validation_regex` = ''";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = "ALTER TABLE `module_faq`
                              ADD `name` VARCHAR(255) NOT NULL COMMENT 'Titel: '";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = "INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = '".TCMSLogChange::GetTableId('module_faq')."', `name` = 'qdescription', `translation` = 'Frage', `cms_field_type_id` = '".TCMSLogChange::GetFieldType('CMSFIELD_WYSIWYG_LIGHT')."', `cms_tbl_field_tab` = '', `isrequired` = '0', `fieldclass` = '', `fieldclass_subtype` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '3', `049_helptext` = '', `row_hexcolor` = '', `is_translatable` = '0', `validation_regex` = ''";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = "ALTER TABLE `module_faq`
                              ADD `qdescription` LONGTEXT NOT NULL COMMENT 'Frage: '";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = "INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = '".TCMSLogChange::GetTableId('module_faq')."', `name` = 'artikel', `translation` = 'Antwort', `cms_field_type_id` = '".TCMSLogChange::GetFieldType('CMSFIELD_WYSIWYG')."', `cms_tbl_field_tab` = '', `isrequired` = '0', `fieldclass` = '', `fieldclass_subtype` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '4', `049_helptext` = '', `row_hexcolor` = '', `is_translatable` = '0', `validation_regex` = ''";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = "ALTER TABLE `module_faq`
                              ADD `artikel` LONGTEXT NOT NULL COMMENT 'Antwort: '";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = "INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = '".TCMSLogChange::GetTableId('module_faq')."', `name` = 'position', `translation` = 'Sortierung', `cms_field_type_id` = '".TCMSLogChange::GetFieldType('CMSFIELD_SORTORDER')."', `cms_tbl_field_tab` = '', `isrequired` = '0', `fieldclass` = '', `fieldclass_subtype` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '5', `049_helptext` = '', `row_hexcolor` = '', `is_translatable` = '0', `validation_regex` = ''";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = "ALTER TABLE `module_faq`
                              ADD `position` INT NOT NULL COMMENT 'Sortierung: '";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = 'ALTER TABLE `module_faq` ADD INDEX ( `position` ) ';
        TCMSLogChange::_RunQuery($query, __LINE__);

        $query = "INSERT INTO `cms_tbl_display_orderfields` SET `name` = 'position', `sort_order_direction` = 'ASC', `position` = '0', `cms_tbl_conf_id` = '".TCMSLogChange::GetTableId('module_faq')."'";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $aRow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query('SELECT MAX(`cms_tpl_module`.`position`) AS pos FROM `cms_tpl_module`'));
        $iPos = $aRow['pos'];
        ++$iPos;

        $query = "INSERT INTO `cms_tpl_module` SET `name` = 'FAQ', `description` = '', `icon_list` = 'page_script.gif', `classname` = 'MTFAQList', `view_mapping` = '', `revision_management_active` = '0', `is_copy_allowed` = '0', `show_in_template_engine` = '1', `position` = '".$iPos."', `is_restricted` = '0'";
        TCMSLogChange::_RunQuery($query, __LINE__);

        $aRow = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query("SELECT `cms_tpl_module`.* FROM `cms_tpl_module` WHERE `cms_tpl_module`.`classname` = 'MTFAQList'"));

        $query = "INSERT INTO `cms_tpl_module_cms_tbl_conf_mlt`
                         SET `source_id` = '".$aRow['id']."',
                             `target_id` = '".TCMSLogChange::GetTableId('module_faq')."',
                             `entry_sort` = '0'";
        TCMSLogChange::_RunQuery($query, __LINE__);
    }
}
?>