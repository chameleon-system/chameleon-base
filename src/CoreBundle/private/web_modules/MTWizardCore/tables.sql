CREATE TABLE `module_wizard` (
                  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
                );
ALTER TABLE `module_wizard`
                        ADD `cms_tpl_module_instance_id` INT(11) NOT NULL;
ALTER TABLE `module_wizard` ADD INDEX ( `cms_tpl_module_instance_id` ) ;
ALTER TABLE `module_wizard`
                        ADD `name` VARCHAR(255) NOT NULL;
ALTER TABLE `module_wizard`
                        ADD `description` TEXT NOT NULL;
ALTER TABLE `module_wizard`
                        ADD `position` INT NOT NULL;
ALTER TABLE `module_wizard` ADD INDEX ( `position` ) ;
ALTER TABLE `module_wizard`
                        ADD `class_type` ENUM('Core','Custom','Customer') DEFAULT 'Core' NOT NULL;
ALTER TABLE `module_wizard`
                        ADD `class` VARCHAR(255) NOT NULL;
ALTER TABLE `module_wizard`
                        ADD `name_internal` VARCHAR(255) NOT NULL;
INSERT INTO `cms_tbl_conf` SET `name` = 'module_wizard', `translation` = 'Modul: Formular Wizard', `list_query` = '', `cms_content_box_id` = '0', `only_one_record_tbl` = '0', `is_multilanguage` = '0', `cms_field_conf_mlt` = 'cms_field_conf', `property_list_fields` = 'cms_tbl_display_list_fields', `property_order_fields` = 'cms_tbl_display_orderfields', `name_column` = '', `name_column_callback` = '', `display_column` = '', `display_column_callback` = '', `list_group_field` = '', `list_group_field_header` = '', `list_group_field_column` = '', `cms_tbl_list_class` = 'cms_tbl_list_class', `cms_tbl_list_class_id` = '0', `table_editor_class` = '', `table_editor_class_type` = 'Core', `cms_tbl_conf_restrictions` = 'cms_tbl_conf_restrictions', `right_seperator_hidden` = '', `cms_usergroup_id` = '4', `notes` = '';
SET @recordIdcms_tbl_conf=LAST_INSERT_ID();
INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `name` = 'cms_tpl_module_instance_id', `translation` = 'gehört zur Modul-Instanze', `cms_field_type_id` = '17', `isrequired` = '0', `fieldclass` = '', `class_type` = 'Core', `modifier` = 'readonly', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '290', `049_helptext` = '', `row_hexcolor` = '';
SET @recordIdcms_field_conf=LAST_INSERT_ID();
INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `name` = 'name', `translation` = 'Titel', `cms_field_type_id` = '34', `isrequired` = '0', `fieldclass` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '290', `049_helptext` = '', `row_hexcolor` = '';
SET @recordIdcms_field_conf=LAST_INSERT_ID();
INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `name` = 'description', `translation` = 'Beschreibung', `cms_field_type_id` = '42', `isrequired` = '0', `fieldclass` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '0', `field_width` = '0', `position` = '290', `049_helptext` = '', `row_hexcolor` = '';
SET @recordIdcms_field_conf=LAST_INSERT_ID();
INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `name` = 'position', `translation` = 'Position', `cms_field_type_id` = '41', `isrequired` = '0', `fieldclass` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '1', `field_width` = '0', `049_helptext` = '', `row_hexcolor` = '';
SET @recordIdcms_field_conf=LAST_INSERT_ID();
INSERT INTO `cms_field_conf_cms_usergroup_mlt`        SET `source_id` = @recordIdcms_field_conf, `target_id` = '6';
INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `name` = 'class', `translation` = 'Class', `cms_field_type_id` = '34', `isrequired` = '0', `fieldclass` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '1', `field_width` = '0', `049_helptext` = '', `row_hexcolor` = '';
SET @recordIdcms_field_conf=LAST_INSERT_ID();
INSERT INTO `cms_field_conf_cms_usergroup_mlt`        SET `source_id` = @recordIdcms_field_conf, `target_id` = '6';
INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `name` = 'class_type', `translation` = 'Klassenart', `cms_field_type_id` = '35', `isrequired` = '0', `fieldclass` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = 'Core', `length_set` = '\'Core\',\'Custom\',\'Customer\'', `fieldtype_config` = '', `restrict_to_groups` = '1', `field_width` = '0', `049_helptext` = '', `row_hexcolor` = '';
SET @recordIdcms_field_conf=LAST_INSERT_ID();
INSERT INTO `cms_field_conf_cms_usergroup_mlt`        SET `source_id` = @recordIdcms_field_conf, `target_id` = '6';
INSERT INTO `cms_field_conf` SET `cms_tbl_conf_id` = @recordIdcms_tbl_conf, `name` = 'name_internal', `translation` = 'Systemname', `cms_field_type_id` = '34', `isrequired` = '1', `fieldclass` = '', `class_type` = 'Core', `modifier` = 'none', `field_default_value` = '', `length_set` = '', `fieldtype_config` = '', `restrict_to_groups` = '1', `field_width` = '0', `049_helptext` = '', `row_hexcolor` = '';
SET @recordIdcms_field_conf=LAST_INSERT_ID();
INSERT INTO `cms_field_conf_cms_usergroup_mlt`        SET `source_id` = @recordIdcms_field_conf, `target_id` = '6';
