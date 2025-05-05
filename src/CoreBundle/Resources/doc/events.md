## Events

- `chameleon_system_core.html_includes.footer` (CoreEvents::GLOBAL_HTML_FOOTER_INCLUDE)
- `chameleon_system_core.html_includes.header` (CoreEvents::GLOBAL_HTML_HEADER_INCLUDE)

The event is triggered when the frontend controller collects all includes that are to be added to the footer/header of the page. The event contains a list of data collected up to that point. An event listener can add lines to the event which will be included in the page.

Note that every line can only be included once (first come, first served). A line is either identified by the key provided or by the md5 sum if no (or an integer) key is provided for that line.

Lines added are added as-is (no escaping) - so make sure you escape as needed.

- `chameleon_system_core.resource_collection_collected.javascript` (CoreEvents:GLOBAL_RESOURCE_COLLECTION_COLLECTED_JAVASCRIPT)

The event is triggered when the resource collection have collected all javaScrip that are to be added to the page. An event listener can now modify the whole javascript content.