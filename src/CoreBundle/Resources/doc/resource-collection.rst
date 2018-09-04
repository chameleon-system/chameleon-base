Resource Collection
===================

Motivation
----------

The resource collection combines if activated all JavaScript and all CSS resources of a page and replaces them in the
rendered page by at most 4 file includes. These files are delivered with a unique file name based on the content.
This serves two purposes:
- Serving a single file is faster than multiple files.
- Furthermore the unique file name allows to update this with a change (or more likely a deploy) and so over-caching
problems of Browsers are not possible.

Configuration
-------------

There are three parameters which govern this feature (with their defaults):
- chameleon_system_core.resources.enable_external_resource_collection: false
- chameleon_system_core.resources.enable_external_resource_collection_minify: false
- chameleon_system_core.resources.enable_external_resource_collection_refresh_prefix: ""

The first two are normally put in the config_prod.yml (and deactivated in config_dev.yml). The last one can be specified
in the config.yml of the project.
It's value must change with every change to JS or CSS files that lead to a deploy on a live server. A good practice is
probably to do this automatically for every (live) deploy by an automatic deploy system - even without change.

Details
-------

The resource collection mechanism will honor site-specific includes so they are not delivered for the other pages.
To do this there is a differentiation between global includes and site-specific ones. Generally every include line
(CSS or JS) without the following tag _<!-- #GLOBALRESOURCECOLLECTION# -->_ will be considered page specific.
This also allows for example module specific footer includes (_GetHtmlFooterIncludes()_) to work as expected and be
site-specific.
As a consequence there might be 4 different files for a page: 2 global files (JS and CSS) and 2 site-specific ones.

One caveat to note (see also https://github.com/chameleon-system/chameleon-system/issues/57) is that only includes in
the head tags (<head>...</head>) are considered for this mechanism. So for example the normal JS includes in the footer
of a page are ignored and remain untouched (and unversioned).

If you want to have a change to resource files show up in a resource collection file you normally need to change the
value of the prefix parameter. Then they will be created newly.
Only on development computers it is recommended to delete the actual content of the resource collection cache.
This can be found in _web/chameleon/outbox/static/js_ (or _css_ respectively).