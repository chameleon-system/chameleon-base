# Chameleon System ViewRendererBundle

## URL Sanitation

URLs are automatically sanitized by Twig (depending on the context, the filters escape('html_attr') or escape('js) need
to be used). This sanitation does however NOT include protection from malicious "javascript:" and "data:" URLs. To
protect against these URLs, use the `sanitizeurl` filter. This filter mimics the standard Twig `escape` filter and
replaces "javascript:" and "data:" URLs with a "#". Options for this filter are exactly the same as for `escape` (e.g.
use `|sanitizeurl("html_attr")` in HTML attributes.

This filter should be used for all URLs that are provided by the user (for persisted user content as well as GET and
POST parameters).