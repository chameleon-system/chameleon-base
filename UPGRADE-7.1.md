UPGRADE FROM 7.0 TO 7.1
=======================

# Changed Features

## Less Compilation Supports STATIC_CONTENT_URL

The less compiler TPkgViewRendererLessCompiler now supports an additional argument for variables that are passed to
less files. Currently this is used to inject STATIC_CONTENT_URL which can be used to load resources from another system
(e.g. a CDN). This in turn can be configured with `chameleon_system_view_renderer.less_compiler: static_content_url`.

For the usage in a .less file use this exact notation: url("@{STATIC_CONTENT_URL}").

## Changed Interfaces and Method Signatures

### TPkgViewRendererLessCompiler

- Added constructor argument 3 ($additionalVariables).
