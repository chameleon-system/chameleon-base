# Resource Collection

## Motivation

The resource collection combines all JavaScript and all CSS resources of a page and replaces them in the rendered page by at most 4 file includes. These files are delivered with a unique file name based on the content. This serves two purposes:

- Serving a single or only a few files is faster than multiple files.
- The unique file name allows for both long browser cache validity and instant cache invalidation after changes.

## Configuration

There are three parameters which govern this feature (given with their defaults):

- `chameleon_system_core.resources.enable_external_resource_collection: false`
- `chameleon_system_core.resources.enable_external_resource_collection_minify: false`
- `chameleon_system_core.resources.enable_external_resource_collection_refresh_prefix: ""`

Normally the resource collection is only active in the prod environment and should therefore be specified in `config_prod.yml`. The value of the prefix must change with every live deployment of modified JS or CSS files. A good practice is to do this unconditionally for every live deployment by an automatic deploy system - even without change.

## Details

The resource collection mechanism will honor page-specific includes so they are not delivered for other pages. To do this there is a differentiation between global includes and page-specific ones. Generally every include line (CSS or JS) without the tag `<!-- #GLOBALRESOURCECOLLECTION# -->` will be considered page-specific. This also allows for example module-specific includes (using `GetHtmlHeadIncludes()` or a view config.yml) to work as expected and be page-specific.

As a consequence there are at most 4 different files for a page: Global JS, global CSS, optional page-specific JS, optional page-specific CSS.

To regenerate the resource collection after changes to the original files, change the value of the prefix parameter. Only on development machines it is recommended to delete the actual resource collection cache files, which can be found in `web/chameleon/outbox/static/js` and `web/chameleon/outbox/static/js`.

## Limitations

Only includes in the head tags (<head>...</head>) are considered for this mechanism. So if for example JS is included in the footer of a page or with `GetHtmlFooterIncludes()` it is ignored and remains untouched and unversioned.