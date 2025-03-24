# Backend Permissions

This document describes the backend permissions of the Chameleon System.

The Chameleon System uses the Symfony Security component for backend permissions. The Symfony Security component is a
powerful and flexible security system for PHP applications. It allows you to easily integrate security into your Symfony
application. The Symfony Security component is a complete security system that allows you to authenticate users, authorize
them based on a set of rules, and retrieve their roles and permissions.

The backend is placed into the firewall "backend" and the frontend into the firewall "frontend". The firewall "backend" is
accessible only to users logged into the backend. The firewall "frontend" is accessible to all users.

Currently, the frontend firewall is not used - instead, frontend security is handelt directly by the Chameleon System.

The backend user has roles, groups, and permissions. 
The groups are generated from the groups assigned to the user via `cms_user_cms_usergroup_mlt`,
the roles via `cms_user_cms_role_mlt` and the permissions from the `cms_right` assigned to the users roles via `cms_role_cms_right_mlt`.

Roles, groups and permissions are all changed to uppercase and prefixed with `ROLE_`, `CMS_GROUP_`, and `CMS_RIGHT_` respectively.

All cms users have the role `ROLE_CMS_USER`.

You can check if a user has a permission by using the `isGranted()` method of the `security.authorization_checker` service.

If you want to allow access to an object for only some roles, groups or rights, that object should implement `RestrictedByCmsRoleInterface`, 
`RestrictedByCmsGroupInterface` or `RestrictedByCmsRightInterface` respectively.

Objects extending `TCMSRecord` will be voted on by the `CmsTableObjectVoter`. In addition, you can check access to a table by passing
the table name to `isGranted()`.

Relevant permissions can be found in `CmsPermissionAttributeConstants`.

If you need access to the symfony security component in a place where injection is not possible, you can access it using the 
`SecurityHelperAccess` via the `ServiceLocator` service.

## Group, Role and Rights IDs

You can use the RightsDataAccessInterface to get the ID represantives of groups, roles and rights.


```php
/** @var SecurityHelperAccess $securityHelper */
$securityHelperAccess = ServiceLocator::get(SecurityHelperAccess::class);
```

## Examples:

Check if a user is a logged in CMS user:

```php
$securityHelperAccess->isGranted(CmsUserRoleConstants::CMS_USER)
```

Check if a user has the right to access a table.
This means the user has either edit or read only access to the table, so he is able to see/access the table in the backend at all.

```php
$securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $tableName)
```

See `SearchConsoleWidget` for an example how to check custom groups on any service like modules, widgets or anything else, 
by implementing the `RestrictedByCmsGroupInterface` 