CREATE TABLE `module_iframe` (
                    `id` CHAR( 36 ) NOT NULL ,
                    `cmsident` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Key is used so that records can be easily identified in Chameleon',
                    PRIMARY KEY ( `id` ) ,
                    UNIQUE (`cmsident`)
                  );
ALTER TABLE `module_iframe`
                        ADD `cms_tpl_module_instance_id` CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;
ALTER TABLE `module_iframe` ADD INDEX ( `cms_tpl_module_instance_id` ) ;
ALTER TABLE `module_iframe`
                        ADD `website_url` VARCHAR(255) NOT NULL;
ALTER TABLE `module_iframe`
                        ADD `width` VARCHAR(10) NOT NULL;
ALTER TABLE `module_iframe`
                        ADD `height` VARCHAR(10) NOT NULL;
INSERT INTO `cms_tbl_conf` SET `name` = 'module_iframe', `dbobject_type` = 'Customer', `translation` = 'Fremdseite (iframe)', `cms_tbl_field_tab` = '', `list_query` = '', `cms_content_box_id` = '', `only_one_record_tbl` = '1', `is_multilanguage` = '0', `is_workflow` = '0', `locking_active` = '', `changelog_active` = '0', `revision_management_active` = '0', `cms_field_conf_mlt` = 'cms_field_conf', `property_list_fields` = 'cms_tbl_display_list_fields', `property_order_fields` = 'cms_tbl_display_orderfields', `name_column` = '', `name_column_callback` = '', `display_column` = '', `display_column_callback` = '', `list_group_field` = '', `list_group_field_header` = '', `list_group_field_column` = '', `cms_tbl_list_class` = 'cms_tbl_list_class', `cms_tbl_list_class_id` = '', `table_editor_class` = '', `table_editor_class_subtype` = '', `table_editor_class_type` = 'Core', `cms_tbl_conf_restrictions` = 'cms_tbl_conf_restrictions', `icon_list` = '', `show_previewbutton` = '0', `cms_tpl_page_id` = '', `rename_on_copy` = '0', `cms_usergroup_id` = '4', `notes` = '', `dbobject_extend_class` = 'TCMSRecord', `dbobject_extend_subtype` = 'dbobjects', `dbobject_extend_type` = 'Core', `cms_tbl_extension` = 'cms_tbl_extension', `auto_limit_results` = '-1', `cms_tbl_conf_index` = 'cms_tbl_conf_index', `id` = '3cfade99-02c6-11a3-09a2-62236735be21';
SET @recordIdcms_tbl_conf='3cfade99-02c6-11a3-09a2-62236735be21';
INSERT INTO `cms_tbl_conf_cms_role_mlt`        SET `source_id` = @recordIdcms_tbl_conf, `target_id` = '4';
INSERT INTO `cms_tbl_conf_cms_role1_mlt`        SET `source_id` = @recordIdcms_tbl_conf, `target_id` = '4';
INSERT INTO `cms_tbl_conf_cms_role2_mlt`        SET `source_id` = @recordIdcms_tbl_conf, `target_id` = '4';
INSERT INTO `cms_tbl_conf_cms_role3_mlt`        SET `source_id` = @recordIdcms_tbl_conf, `target_id` = '4';
INSERT INTO `cms_tbl_conf_cms_role4_mlt`        SET `source_id` = @recordIdcms_tbl_conf, `target_id` = '4';
INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `name` = 'website_url', `translation` = 'URL', `cms_field_type_id` = '27', `cms_tbl_field_tab` = '', `isrequired` = '1', `fieldclass` = '', `fieldclass_subtype` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '255', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '2', `049_helptext` = '', `row_hexcolor` = '', `is_translatable` = '0', `validation_regex` = '', `id` = 'b16ab885-4ec5-e61b-b388-e15fd9cc3b0c';
SET @recordIdcms_field_conf='b16ab885-4ec5-e61b-b388-e15fd9cc3b0c';
INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `name` = 'width', `translation` = 'Breite', `cms_field_type_id` = '34', `cms_tbl_field_tab` = '', `isrequired` = '0', `fieldclass` = '', `fieldclass_subtype` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '10', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '3', `049_helptext` = 'Breite des iframes in Pixeln oder Prozent (%)', `row_hexcolor` = '', `is_translatable` = '0', `validation_regex` = '', `id` = '375cae9b-c948-7942-e125-1b4bbd263851';
SET @recordIdcms_field_conf='375cae9b-c948-7942-e125-1b4bbd263851';
INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `name` = 'height', `translation` = 'Höhe', `cms_field_type_id` = '34', `cms_tbl_field_tab` = '', `isrequired` = '1', `fieldclass` = '', `fieldclass_subtype` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '10', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '4', `049_helptext` = 'Höhe des iframes in Pixeln oder Prozent (%)', `row_hexcolor` = '', `is_translatable` = '0', `validation_regex` = '', `id` = '73893e59-8f60-1ba6-fa23-cf108130dc05';
SET @recordIdcms_field_conf='73893e59-8f60-1ba6-fa23-cf108130dc05';
INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `name` = 'cms_tpl_module_instance_id', `translation` = 'Gehört zu Modul', `cms_field_type_id` = '48', `cms_tbl_field_tab` = '', `isrequired` = '0', `fieldclass` = '', `fieldclass_subtype` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '1', `049_helptext` = '', `row_hexcolor` = '', `is_translatable` = '0', `validation_regex` = '', `id` = '815a50dc-4199-827c-6fb6-d85cebf3cd22';
SET @recordIdcms_field_conf='815a50dc-4199-827c-6fb6-d85cebf3cd22';

INSERt INTO cms_tpl_module SET id = '54', cmsident = null, name = 'Fremdseite (iframe)', description = 'Bettet eine URL als iframe in eine Seite ein', icon_list = 'folder_link.png', classname = 'MTIframe', show_in_template_engine = '1' ;

/**
 * Tabelle modul_iframe muss im neu angelegten Modul noch manuell als Editiertabelle angelegt werden.
 */