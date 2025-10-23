# Json Editor Bundle
This bundle integrates [NanoJSON](https://github.com/pardnchiu/NanoJSON) as a JSON editor field into the Chameleon CMS.  
NanoJSON is a lightweight tree-based JSON editor with no external dependencies.

## Usage
1. Add the bundle to the AppKernel (`\ChameleonSystem\FieldJsonEditorBundle\ChameleonSystemFieldJsonEditorBundle()`)
2. Run
3. Execute `app/console assets:install --symlink --relative web` in your root directory
4. Add the editor as a field to one of your CMS tables

## Upgrading

The dist file NanoJSON.js requires an external CDN for the CSS file.
https://cdn.jsdelivr.net/npm/@pardnchiu/nanojson@1.1.2/dist/NanoJSON.css
This needs to be changed to /bundles/chameleonsystemfieldjsoneditor/css/NanoJSON.css on every update or till the developer allows the configuration of the file.

There is a styling bug with the type dropdown menues, not showing the select box option texts.
This is fixed by:

```
    .pd-json-editor-child select,
    ::picker(select) {
        color: #7f7ffa;
    }
```

Add this on ugrades to NanoJSON.css if necessary. If the styling bug is fixed, remove this comment. 

## NanoJSON
For more reference, visit the official GitHub page of the editor:  
👉 https://github.com/pardnchiu/NanoJSON
