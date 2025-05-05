# Routing

## General

Chameleon uses the CMS chain router to execute multiple routers:

- standard Symfony router
- ChameleonBackendRouter
- ChameleonFrontendRouter

## Page Routing and Generation

Frontend routing needs to support arbitrary SEO URLs that can be managed in the backend. Thus it is not possible to match these routes by regular expressions as the Symfony routing normally expects. Instead, there is a catch-all route `cms_tpl_page` that finds the correct page for a given URL. This route is added as the final route, so that custom routing can be used.

## Case-sensitivity

- route matching is case-insensitive.
- requests that do not match the route case are redirected to the real route to avoid duplicate content.
- generated routes will be lower-cased if the constant CHAMELEON_SEO_URL_REWRITE_TO_LOWERCASE is set to true.

## Multiple Pages and Assignments

- there can be more than one page assigned to a tree node; in this case the order is undefined, so any of the assigned pages may be displayed.
- a page can be assigned to multiple nodes. TAdbCmsTplPage::$fieldPrimaryTreeIdHidden specifies the primary assignment.

## Page Activation

- a page assignment can be deactivated. If a deactivated page is requested, a 404 error will be displayed. If there are multiple pages assigned to a given node, inactive assignments are simply not considered.
- a page assignment can be configured to get a start date and an end date. The assignment is automatically only active in this timespan if set.

## Route Normalization

- trailing slashes in generated routes will be cut if the constant CHAMELEON_SEO_URL_REMOVE_TRAILING_SLASH is set to true. Otherwise trailing slashes will be added to the routes.
- a ".html" suffix will be added to each route if TAdbCmsPortal::$fieldUseSlashInSeoUrls is set to false. In this case, trailing slashes will be removed.
- the request will automatically be redirected to the correct route with/without trailing slash to avoid duplicate content.

## Security

- the request will automatically be redirected to the equivalent HTTPS route if the page requires HTTPS (set TAdbCmsTplPage::$fieldUseSsl to true).
- URLs to those pages will always be generated as HTTPS URLs.
- if the current request uses HTTPS, all generated URLs will also use HTTPS.
- in other cases, HTTPS URL generation can be enforced.
- if the system uses HTTPS only, it is adviced to configure the web server correspondingly. Chameleon will never generate unsafe HTTP URLs if the current request uses HTTPS, so at most one redirect at the begin of the session is required.

## Portals and Domains

- pages are assigned to one portal
- multiple domains can be assigned to a portal; generated absolute URLs will include the primary domain, and routes will only match if the request is "caught" by a domain assigned to a portal containing the required page.
- the portal URL prefix is considered if set (TAdbCmsPortal::$fieldIdentifier).

## Navigation

- A page can only be accessed if it is part of a navigation (i.e. if the primary tree node of the page has a parent node that is registered in the cms_portal_navigation table for the page's portal).

## Internationalization

- page routes are language-specific, therefore the request language influences routing. If the portal does not support the selected language (i.e. the language is not checked in the portal configuration in the Chameleon backend), a 404 error will be displayed. The route is generated based on the tree node names of the selected language.
- if a node is not translated, the path is normally not available. Set TAdbCmsPortal::$fieldShowNotTanslated (mind the typo which is retained for backwards compatiblity) to true to enable the route anyway (the default language will be used for non-translated path parts).

## Twig Extension

- Chameleon handles URLs for different portals and languages internally by changing the route names; therefore it is currently not possible to use the `path` Twig extension (using it will result in `RouteNotFoundException`s). It is yet to be decided if the Twig extension will be modified or if there will be a new one. Do not try to construct the route names manually, but use ChameleonFrontendRouter::generateWithPrefixes() instead.
- The preceding statement is only true for routes that were registered in the routing configuration in the Chameleon backend. To use completely unaltered routes as they are specified by Symfony, simply create routing configuration files and import them in the project config files. Note that no portal or language functionality will be available for these routes.

## Access Control

- page access can be restricted to logged-in users (set TAdbCmsTplPage::$fieldExtranetPage to true). Anonymous users will be shown a login page.
- page access can additionally be restricted to certain user groups (set TAdbCmsTplPage::$fieldExtranetPage to true and select groups that gain access in the backend; as long as at least one group is selected, the user needs to be member of at least one of these groups).
- page access can additionally be restricted to confirmed users (set TAdbCmsTplPage::$fieldExtranetPage to true and TAdbCmsTplPage::$fieldAccessNotConfirmedUser to false). If set, users that did not confirm their registration cannot access the page (double opt-in).

## Interactivity

In Chameleon, administrators may change all of the above-mentioned routing-specific values at any time. This means that it will always be required to react dynamically to these changes, such as cache invalidation. Avoid writing dynamic data to the file system.

## Multi-node Systems

In all routing solutions it needs to be taken into account that Chameleon is designed to operate in a cluster environment. Typically changes are made on one of the cluster nodes, but need to be available to all nodes in a timely manner. Nodes could synchronize by using e.g. memcached or the database. If the filesystem is used to write routing data, a custom synchronization functionality needs to be implemented.

## URL Generation Methods

In principle, URL or link generation for CMS pages (!) works the same as in Symfony. The most significant difference is that we always need to consider the current domain, portal and language (in the standard case URLs have e.g. a "/my-portal/fr" prefix for the portal "my-portal" and French language).

Thus Chameleon uses the ChameleonFrontendRouter::generateWithPrefixes() method to generate URLs, where domain, portal and language can be passed (defaulting to the ones in the current request). However, it is not advised to use this method directly, but one of these:

- `ChameleonSystem\CoreBundle\Service\PageServiceInterface::getLinkToPageRelative()` (or `*Absolute()`)
- `ChameleonSystem\CoreBundle\Service\PageServiceInterface::getLinkToPortalHomePageRelative()` (or `*Absolute()`)
- `ChameleonSystem\CoreBundle\Service\TreeServiceInterface::getLinkToPageForTreeRelative()` (or `*Absolute()`)
- `ChameleonSystem\CoreBundle\Service\SystemPageServiceInterface::getLinkToSystemPageRelative()` (or `*Absolute()`)

This way using the route names can be avoided, as they might be changed in the future (and we need some route definition magic to ensure that all URLs are created with or without trailing slash, depending on the preferences described above).

In the past, URL generation was done directly in the classes that represented a linkable object (e.g. a shop product). These classes implement the interface `ICmsLinkableObject` and generate URLs in `getLink()`. While some of these methods are deprecated and only link to the adjusted Symfony URL generation functionality as described above, some are still in use and implement a mix of custom and default generation logic.