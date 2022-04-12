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
    const GLOBAL_HTML_FOOTER_INCLUDE = 'chameleon_system_core.html_includes.footer';
    const GLOBAL_HTML_HEADER_INCLUDE = 'chameleon_system_core.html_includes.header';

    const GLOBAL_RESOURCE_COLLECTION_COLLECTED_JAVASCRIPT = 'chameleon_system_core.resource_collection_collected.javascript';

    const BACKEND_LOGIN_SUCCESS = 'chameleon_system_core.login_success';
    const BACKEND_LOGIN_FAILURE = 'chameleon_system_core.login_failure';
    const BACKEND_LOGOUT_SUCCESS = 'chameleon_system_core.logout_success';

    const LOCALE_CHANGED = 'chameleon_system_core.locale_changed';

    const CHANGE_ACTIVE_PAGE = 'chameleon_system_core.change_active_page';

    const CHANGE_ACTIVE_PORTAL = 'chameleon_system_core.change_active_portal';

    const ADD_NAVIGATION_TREE_NODE = 'chameleon_system_core.add_navigation_tree_node';
    const UPDATE_NAVIGATION_TREE_NODE = 'chameleon_system_core.update_navigation_tree_node';
    const DELETE_NAVIGATION_TREE_NODE = 'chameleon_system_core.delete_navigation_tree_node';

    const ADD_NAVIGATION_TREE_CONNECTION = 'chameleon_system_core.add_navigation_tree_connection';
    const UPDATE_NAVIGATION_TREE_CONNECTION = 'chameleon_system_core.update_navigation_tree_connection';
    const DELETE_NAVIGATION_TREE_CONNECTION = 'chameleon_system_core.delete_navigation_tree_connection';

    const ADD_DOMAIN = 'chameleon_system_core.add_domain';
    const UPDATE_DOMAIN = 'chameleon_system_core.update_domain';
    const DELETE_DOMAIN = 'chameleon_system_core.delete_domain';

    const ADD_SHOP_ORDER_STEP = 'chameleon_system_core.add_shop_order_step';
    const UPDATE_SHOP_ORDER_STEP = 'chameleon_system_core.update_shop_order_step';
    const DELETE_SHOP_ORDER_STEP = 'chameleon_system_core.delete_shop_order_step';

    const UPDATE_BEFORE_COLLECTION = 'chameleon_system_core.update.before_collection';

    const CHANGE_DEFAULT_LANGUAGE_FOR_PORTAL = 'chameleon_system_core.change_default_language_for_portal';
    const CHANGE_ACTIVE_LANGUAGES_FOR_PORTAL = 'chameleon_system_core.change_active_languages_for_portal';

    const CHANGE_USE_SLASH_IN_SEO_URLS_FOR_PORTAL = 'chameleon_system_core.change_use_slash_in_seo_urls_for_portal';

    const CHANGE_ROUTING_CONFIG = 'chameleon_system_core.change_routing_config';

    /**
     * The chameleon_system_core.*_record is dispatched each time a record is inserted/updated/deleted.
     * The event listener receives an \ChameleonSystem\CoreBundle\Event\RecordChangeEvent instance.
     */
    const INSERT_RECORD = 'chameleon_system_core.insert_record';
    const UPDATE_RECORD = 'chameleon_system_core.update_record';
    const DELETE_RECORD = 'chameleon_system_core.delete_record';

    const BEFORE_DELETE_MEDIA = 'chameleon_system_core.before_delete_media';

    const DISPLAY_LISTMANAGER_CELL = 'chameleon_system_core.display_listmanager_cell';

    /**
     * chameleon_system_core.filter_content is dispatched right before the content is sent to the client.
     * It has to be used instead of symfony's kernel.response when the content is flushed to the client
     * before it reaches the end of execution. This is the case in layouts which use
     * \ChameleonSystem\CoreBundle\Controller\ChameleonControllerInterface::FlushContentToBrowser
     */
    const FILTER_CONTENT = 'chameleon_system_core.filter_content';
}
