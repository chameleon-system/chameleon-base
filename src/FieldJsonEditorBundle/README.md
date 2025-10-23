# Json Editor Bundle
This bundle integrates [NanoJSON](https://github.com/pardnchiu/NanoJSON) as a JSON editor field into the Chameleon CMS.  
NanoJSON is a lightweight tree-based JSON editor with no external dependencies.

## Usage
1. Add the bundle to the AppKernel (`\ChameleonSystem\FieldJsonEditorBundle\ChameleonSystemFieldJsonEditorBundle()`)
2. Run
3. Execute `app/console assets:install --symlink --relative web` in your root directory
4. Add the editor as a field to one of your CMS tables (for example `products`)
5. Make sure to create a template in `/snippets-cms/Fields/FieldJsonEditor` (for example `jsonEditor.html.twig`)
6. In the field type configuration, add your created template like this:  
   `layout=[name_of_your_layout]` (e.g. `layout="jsonEditor"`)
    - If no layout is provided or the layout you entered cannot be found, the standard template `jsonEditorInputStandard.html.twig` will be used automatically.
7. If you need project-specific adjustments, copy `jsonEditorInputStandard.html.twig` into your project (e.g. `jsonEditor.html.twig`) and adapt it as required.

## NanoJSON
For more reference, visit the official GitHub page of the editor:  
👉 https://github.com/pardnchiu/NanoJSON
