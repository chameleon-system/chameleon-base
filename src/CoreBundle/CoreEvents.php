<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle;

final class CoreEvents
{
    public const GLOBAL_HTML_FOOTER_INCLUDE = 'chameleon_system_core.html_includes.footer';
    public const GLOBAL_HTML_HEADER_INCLUDE = 'chameleon_system_core.html_includes.header';

    public const GLOBAL_RESOURCE_COLLECTION_COLLECTED_JAVASCRIPT = 'chameleon_system_core.resource_collection_collected.javascript';

    public const LOCALE_CHANGED = 'chameleon_system_core.locale_changed';

    public const CHANGE_ACTIVE_PAGE = 'chameleon_system_core.change_active_page';

    public const CHANGE_ACTIVE_PORTAL = 'chameleon_system_core.change_active_portal';

    public const ADD_NAVIGATION_TREE_NODE = 'chameleon_system_core.add_navigation_tree_node';
    public const UPDATE_NAVIGATION_TREE_NODE = 'chameleon_system_core.update_navigation_tree_node';
    public const DELETE_NAVIGATION_TREE_NODE = 'chameleon_system_core.delete_navigation_tree_node';

    public const ADD_NAVIGATION_TREE_CONNECTION = 'chameleon_system_core.add_navigation_tree_connection';
    public const UPDATE_NAVIGATION_TREE_CONNECTION = 'chameleon_system_core.update_navigation_tree_connection';
    public const DELETE_NAVIGATION_TREE_CONNECTION = 'chameleon_system_core.delete_navigation_tree_connection';

    public const ADD_DOMAIN = 'chameleon_system_core.add_domain';
    public const UPDATE_DOMAIN = 'chameleon_system_core.update_domain';
    public const DELETE_DOMAIN = 'chameleon_system_core.delete_domain';

    public const ADD_SHOP_ORDER_STEP = 'chameleon_system_core.add_shop_order_step';
    public const UPDATE_SHOP_ORDER_STEP = 'chameleon_system_core.update_shop_order_step';
    public const DELETE_SHOP_ORDER_STEP = 'chameleon_system_core.delete_shop_order_step';

    public const UPDATE_BEFORE_COLLECTION = 'chameleon_system_core.update.before_collection';

    public const CHANGE_DEFAULT_LANGUAGE_FOR_PORTAL = 'chameleon_system_core.change_default_language_for_portal';
    public const CHANGE_ACTIVE_LANGUAGES_FOR_PORTAL = 'chameleon_system_core.change_active_languages_for_portal';

    public const CHANGE_USE_SLASH_IN_SEO_URLS_FOR_PORTAL = 'chameleon_system_core.change_use_slash_in_seo_urls_for_portal';

    public const CHANGE_ROUTING_CONFIG = 'chameleon_system_core.change_routing_config';

    /**
     * The chameleon_system_core.*_record is dispatched each time a record is inserted/updated/deleted.
     * The event listener receives an \ChameleonSystem\CoreBundle\Event\RecordChangeEvent instance.
     */
    public const INSERT_RECORD = 'chameleon_system_core.insert_record';
    public const UPDATE_RECORD = 'chameleon_system_core.update_record';
    public const DELETE_RECORD = 'chameleon_system_core.delete_record';

    public const BEFORE_DELETE_MEDIA = 'chameleon_system_core.before_delete_media';

    public const DISPLAY_LISTMANAGER_CELL = 'chameleon_system_core.display_listmanager_cell';

    /**
     * chameleon_system_core.filter_content is dispatched right before the content is sent to the client.
     *
     * @deprecated since 8.0.0 - use symfony's kernel.response instead (the core uses this event internally, which needs to refactored first)
     */
    public const FILTER_CONTENT = 'chameleon_system_core.filter_content';

    /**
     * When copying a portal a map of old tree ids to new tree ids is created.
     * - @see TCMSPortal::dispatchTreeIdMapCompletedEvent()
     * - @see \ChameleonSystem\CoreBundle\Event\TreeIdMapCompletedEvent
     * This event is dispatched when the map is completed and therefore available to e.g. proprietary modules that need to fix references to tree ids.
     */
    public const TREE_ID_MAP_COMPLETED = 'chameleon_system_core.tree_id_map_completed';
}
