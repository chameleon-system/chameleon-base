Important for jQuery UI:
In the updated version, some features of the UI were removed from ui.core and put to extra packages, some extra packages were added like "dialog".
So these js-files would have to be included in HTMLHeadIncludes wherever needed. To prevent this and be backward-compatible, we moved these features
back into ui.core. This has to be done whenever the UI is updated again. For now the moved packages are (in this order):
ui.widget
ui.mouse
ui.positions
ui.draggable
ui.resizable
ui.dialog