# Head/Footer Includes (javascript, css)

There are two events that help you inject content into the head or footer section of your page:

- `chameleon_system_core.html_includes.footer` (CoreEvents::GLOBAL_HTML_FOOTER_INCLUDE)
- `chameleon_system_core.html_includes.header` (CoreEvents::GLOBAL_HTML_HEADER_INCLUDE)

Alternatively, you may add lines using `AddHTMLHeaderLine` and `AddHTMLFooterLine` of the `chameleon_system_core.chameleon_controller` service.

Note: this last option is not ideal and will most likely change at some point.
