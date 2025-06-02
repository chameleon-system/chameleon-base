<?php

namespace ChameleonSystem\SecurityBundle\Voter;

final class CmsPermissionAttributeConstants
{
    public const TABLE_EDITOR_ACTIONS = [
        self::TABLE_EDITOR_ACCESS,
        self::TABLE_EDITOR_ACCESS_ALL,
        self::TABLE_EDITOR_NEW,
        self::TABLE_EDITOR_EDIT,
        self::TABLE_EDITOR_EDIT_ALL,
        self::TABLE_EDITOR_DELETE,
        self::TABLE_EDITOR_NEW_LANGUAGE,
        self::TABLE_EDITOR_WORKFLOW_PUBLISH, // @deprecated since 8.0
        self::TABLE_EDITOR_VERSIONING,
    ];

    public const ACCESS = 'cms::access';

    public const TABLE_EDITOR_ACCESS = 'cms::tbl_edit_access';
    public const TABLE_EDITOR_ACCESS_ALL = 'cms::tbl_edit_access-all';
    public const TABLE_EDITOR_NEW = 'cms::tbl_edit_new';
    public const TABLE_EDITOR_EDIT = 'cms::tbl_edit_edit';
    public const TABLE_EDITOR_EDIT_ALL = 'cms::tbl_edit_edit-all';
    public const TABLE_EDITOR_DELETE = 'cms::tbl_edit_delete';
    public const TABLE_EDITOR_NEW_LANGUAGE = 'cms::tbl_edit_new-language';
    public const TABLE_EDITOR_WORKFLOW_PUBLISH = 'cms::tbl_edit_workflow-publish'; // @deprecated since 8.0
    public const TABLE_EDITOR_VERSIONING = 'cms::tbl_edit_versioning';

    public const DASHBOARD_ACCESS = 'dashboard::access';
}
