Chameleon System CmsNavigationBundle
====================================

This bundle can be used to render navigations.

use the module "CMS Navigation" to render navigations. Define the navigations to render using the view_mapper_config field - then use as static modules

```php
if (TCMSLogChange::AllowTransaction(2, 'dbversion-pkgCmsNavigation')) {
  $query ="INSERT INTO `cms_tpl_module` SET `name` = '', `description` = '', `cms_tbl_conf_mlt` = '', `icon_list` = 'application.png', `classname` = '', `view_mapper_config` = '', `view_mapping` = '', `revision_management_active` = '0', `is_copy_allowed` = '0', `show_in_template_engine` = '1', `position` = '', `is_restricted` = '0', `cms_usergroup_mlt` = '', `cms_portal_mlt` = '', `id`='ae5b3a1b-65f9-a915-6338-77a4163cbd26'";
  TCMSLogChange::_RunQuery($query,__LINE__);
}
```

```php
if (TCMSLogChange::AllowTransaction(3, 'dbversion-pkgCmsNavigation')) {
  $query ="UPDATE `cms_tpl_module` SET `name` = 'CMS Navigation', `description` = '', `icon_list` = 'chart_organisation.png', `classname` = 'MTPkgCmsNavigation', `view_mapper_config` = 'standard=/common/navigation/standard.html.twig', `view_mapping` = '', `revision_management_active` = '0', `is_copy_allowed` = '0', `show_in_template_engine` = '0', `position` = '112', `is_restricted` = '0' WHERE `id` = 'ae5b3a1b-65f9-a915-6338-77a4163cbd26'";
  TCMSLogChange::_RunQuery($query,__LINE__);
}
```