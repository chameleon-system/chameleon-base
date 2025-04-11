# Markdown CMS Integration Bundle

## Setup 

Add `new \ChameleonSystem\MarkdownCmsBundle\ChameleonSystemMarkdownCmsBundle(),` to AppKernel.php and run updates

## Toast UI Markdown Editor

The bundle uses the Toast UI Markdown Editor (short tui.editor).
https://github.com/nhn/tui.editor

We use the CDN builds but loaded from local.

To update the editor you need to download all files from: https://uicdn.toast.com/editor/latest/toastui-editor-all.min.js

Language Files are only in "min" format availabe via CDN: https://uicdn.toast.com/editor/latest/i18n/de-de.min.js

Currently, we only support german and english based on the active backend language.

## Backend Field

The Bundle installs a new field type "Markdown-Text", System name: **CMSFIELD_MARKDOWNTEXT**.
You can use the field for any multiline text field.

## HTML Output in Frontend

To convert the markdown to HTML in the frontend, you need to use the markdown twig filter.
Example: 
```
{{ categoryDescription | markdown | raw }}
```

If you need to output the markdown in PHP, use the service: chameleon_system_markdown_cms.markdown_parser_service

Example:
```
$markdownParserService->getMarkdownParser()->convert($content);
```
