CREATE TABLE `module_faq` (
                    `id` CHAR( 36 ) NOT NULL ,
                    `cmsident` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Key is used so that records can be easily identified in Chameleon',
                    PRIMARY KEY ( `id` ) ,
                    UNIQUE (`cmsident`)
                  );
ALTER TABLE `module_faq`
                        ADD `cms_tpl_module_instance_id` CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;
ALTER TABLE `module_faq` ADD INDEX ( `cms_tpl_module_instance_id` ) ;
ALTER TABLE `module_faq`
                        ADD `name` VARCHAR(255) NOT NULL;
ALTER TABLE `module_faq`
                        ADD `qdescription` LONGTEXT NOT NULL;
ALTER TABLE `module_faq`
                        ADD `artikel` LONGTEXT NOT NULL;
ALTER TABLE `module_faq`
                        ADD `position` INT NOT NULL;
ALTER TABLE `module_faq` ADD INDEX ( `position` ) ;
INSERT INTO `cms_tbl_conf` SET `name` = 'module_faq', `dbobject_type` = 'Customer', `translation` = 'FAQ', `cms_tbl_field_tab` = '', `list_query` = '', `cms_content_box_id` = '', `only_one_record_tbl` = '0', `is_multilanguage` = '0', `is_workflow` = '0', `locking_active` = '', `changelog_active` = '0', `revision_management_active` = '0', `cms_field_conf_mlt` = 'cms_field_conf', `property_list_fields` = 'cms_tbl_display_list_fields', `property_order_fields` = 'cms_tbl_display_orderfields', `name_column` = '', `name_column_callback` = '', `display_column` = '', `display_column_callback` = '', `list_group_field` = '', `list_group_field_header` = '', `list_group_field_column` = '', `cms_tbl_list_class` = 'cms_tbl_list_class', `cms_tbl_list_class_id` = '', `table_editor_class` = '', `table_editor_class_subtype` = '', `table_editor_class_type` = 'Core', `cms_tbl_conf_restrictions` = 'cms_tbl_conf_restrictions', `icon_list` = 'page_script.gif', `show_previewbutton` = '0', `cms_tpl_page_id` = '', `cms_usergroup_id` = '4', `notes` = '', `dbobject_extend_class` = 'TCMSRecord', `dbobject_extend_subtype` = 'dbobjects', `dbobject_extend_type` = 'Core', `cms_tbl_extension` = 'cms_tbl_extension', `auto_limit_results` = '-1', `cms_tbl_conf_index` = 'cms_tbl_conf_index', `id` = '9167eb68-f1e8-c8d3-77e6-9bad7685f7f3';
SET @recordIdcms_tbl_conf='9167eb68-f1e8-c8d3-77e6-9bad7685f7f3';
INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `name` = 'cms_tpl_module_instance_id', `translation` = 'Modulverknüpfung', `cms_field_type_id` = '48', `cms_tbl_field_tab` = '', `isrequired` = '0', `fieldclass` = '', `class_type` = 'Core', `modifier` = 'readonly', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '532', `049_helptext` = '', `row_hexcolor` = '', `is_translatable` = '0', `validation_regex` = '', `id` = 'd741d3d0-1598-f694-e3b2-5fe0a89e4bee';
SET @recordIdcms_field_conf='d741d3d0-1598-f694-e3b2-5fe0a89e4bee';
INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `name` = 'name', `translation` = 'Titel', `cms_field_type_id` = '34', `cms_tbl_field_tab` = '', `isrequired` = '1', `fieldclass` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '532', `049_helptext` = '', `row_hexcolor` = '', `is_translatable` = '0', `validation_regex` = '', `id` = '903d342d-f722-1b98-72f5-896e7aab949c';
SET @recordIdcms_field_conf='903d342d-f722-1b98-72f5-896e7aab949c';
INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `name` = 'qdescription', `translation` = 'Frage', `cms_field_type_id` = '43', `cms_tbl_field_tab` = '', `isrequired` = '0', `fieldclass` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '532', `049_helptext` = '', `row_hexcolor` = '', `is_translatable` = '0', `validation_regex` = '', `id` = 'c185a1ed-2d11-ee10-3bc5-7fef53fb9bbb';
SET @recordIdcms_field_conf='c185a1ed-2d11-ee10-3bc5-7fef53fb9bbb';
INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `name` = 'artikel', `translation` = 'Antwort', `cms_field_type_id` = '42', `cms_tbl_field_tab` = '', `isrequired` = '0', `fieldclass` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '532', `049_helptext` = '', `row_hexcolor` = '', `is_translatable` = '0', `validation_regex` = '', `id` = '86e599d4-6a57-129c-5c10-e8be4c254154';
SET @recordIdcms_field_conf='86e599d4-6a57-129c-5c10-e8be4c254154';
INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `name` = 'position', `translation` = 'Sortierung', `cms_field_type_id` = '41', `cms_tbl_field_tab` = '', `isrequired` = '0', `fieldclass` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '532', `049_helptext` = '', `row_hexcolor` = '', `is_translatable` = '0', `validation_regex` = '', `id` = '7264deb8-6ac7-7238-cdcf-1b7a22e25c4b';
SET @recordIdcms_field_conf='7264deb8-6ac7-7238-cdcf-1b7a22e25c4b';
INSERT INTO `cms_tbl_display_orderfields` SET `name` = 'position', `sort_order_direction` = 'ASC', `position` = '18', `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `id` = '26f987d8-5c82-5ee8-f680-eafc973ae0fa';
SET @recordIdcms_tbl_display_orderfields='26f987d8-5c82-5ee8-f680-eafc973ae0fa';

INSERT INTO `cms_tpl_module` (`id`, `cmsident`, `name`, `description`, `icon_list`, `cms_tbl_conf_mlt`, `classname`, `show_in_template_engine`, `position`, `is_restricted`, `cms_usergroup_mlt`, `name__en`) VALUES('9167eb68-f1e8-c8d3-77e6-9bad7685f7f4', NULL, 'FAQ', '', 'page_script.gif', '', 'MTFAQList', '1', 21, '0', 0, '');