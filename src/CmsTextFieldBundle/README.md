Chameleon System CmsTextFieldBundle
===================================

This package parses CMS WYSIWYG Editor fields (based on CKEditor) and does some conversion.

* Adds the class 'cmsLinkSurroundsImage' to all links that enclose an image tag to allow custom styling or javascript 
  event handling.
* Adds the class 'external' to all links linking to an offsite target to allow special styling.
* Adds the class 'cmsanchor' to all anchors.
* Replaces all mailto: links with an obfuscating JavaScript to prevent bot indexing.
* Renders Chameleon internal page links as SEO URL.
* Renders 3 types of Chameleon document links, <span> based and placeholder based '[{123...}]'.
* Replaces invalid DIVs that where added by the old WYSWIYGPro under some circumstances.
* Replaces empty align properties with 'bottom'.
* Parses image tags and renders images, flash video embed codes or external video embed codes.
* Removes \<script\> tags (disabled by default).

pkgCmsTextblock
---------------

If the package pkgCmsTextblock is installed, it will use the hook: _ReplaceCmsTextBlockInString to replace placeholders 
in format [{fooBar}].

Options
-------

Check the properties of TCMSTextFieldEndPoint for some options.
