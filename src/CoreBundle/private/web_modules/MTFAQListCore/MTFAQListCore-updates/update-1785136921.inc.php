<h1>Build #1785136921</h1>
<h2>Date: 2026-07-27</h2>
<div class="changelog">
    - ref #70900: make module_faq fields translatable
</div><?php

TCMSLogChange::makeFieldMultilingual('module_faq', 'name');
TCMSLogChange::makeFieldMultilingual('module_faq', 'qdescription');
TCMSLogChange::makeFieldMultilingual('module_faq', 'artikel');