# Json Editor Bundle
This bundle adds a Json Editor as a selectable field into the cms.

## Usage
1. Add the bundle to the AppKernel
2. Run updates
3. Add the editor as a field to one of your cms tables (for example products)
4. Make sure to create a template in /snippets-cms/Fields/jsonEditor (for example "jsonEditor.html.twig")
5. in the field type configuration, add your created template like this "layout=[name_of_your_layout]" (e.g. "layout="jsonEditor")
   - when no layout is provided or the layout you entered cant be found, a standard layout "jsonEditorInputStandard.html.twig" will be loaded instead
6. To have an easy start, copy the content of jsonEditorInputStandard.html.twig into your new template. Now you can add the scheme for building your editor

## Schema example
An example of how a scheme can look like can be found below:

```js
const schema = {
   title: "Belletristik Suchanfrage",
   type: "object",
   required: ["filter", "sorting", "pagination"],
   properties: {
      filter: {
         type: "object",
         title: "Filter",
         required: ["keywords", "productTypes", "availableWithinDays"],
         properties: {
            keywords: {
               type: "array",
               title: "Keywords",
               items: { type: "string" },
               minItems: 1
            },
            productTypes: {
               type: "array",
               title: "Product Types",
               items: {
                  type: "string",
                  enum: ["BUCH", "EBOOK", "AUDIOCD", "DVD"] // erweiterbar
               },
               minItems: 1
            },
            availableWithinDays: {
               type: "integer",
               title: "Available Within Days",
               minimum: 0
            }
         }
      },
      sorting: {
         type: "object",
         title: "Sorting",
         required: ["field", "order"],
         properties: {
            field: {
               type: "string",
               enum: ["RANKING", "TITLE", "AUTHOR", "DATE", "PRICE"],
               title: "Sort Field"
            },
            order: {
               type: "string",
               enum: ["ascending", "descending"],
               title: "Order"
            }
         }
      },
      pagination: {
         type: "object",
         title: "Pagination",
         required: ["offset", "maxResults"],
         properties: {
            offset: { type: "integer", title: "Offset", minimum: 0 },
            maxResults: { type: "integer", title: "Max Results", minimum: 1 }
         }
      }
   }
};
```

For more reference, visit the official github page of the editor: https://github.com/json-editor/json-editor