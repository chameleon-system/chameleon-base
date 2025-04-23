# Chameleon System JavaScriptMinificationBundle

The JavaScriptMinificationBundle provides support to optimize JavaScript resources.

## Installation

### Step 1: Download the Bundle

Open a command console in the project directory and execute the following command to download the latest stable version of this bundle:

```bash
$ composer require "chameleon-system/javascript-minification-bundle": "@stable"
```

This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation. Be sure to adjust the version information "@stable" to the actual version you need.

### Step 2: Enable the Bundle

Then, enable the bundle by adding the following lines in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...

public function registerBundles()
{
    $bundles = array(
        // ...
        new \ChameleonSystem\JavaScriptMinificationBundle\ChameleonSystemJavaScriptMinificationBundle(),
        new \Symfony\Bundle\MonologBundle\MonologBundle(),
    );
}
```

### Step 3: Configure Minifier Integration

We use a service that manages minifying JavaScript content. Configure a JS minifier integration to minify JS content, e.g. JsMin or Jshrink. If this block is not set, no JS will be minified.

```yaml
chameleon_system_java_script_minification:
    js_minifier_to_use: "service alias tag"
```

### Optional Step 4: Configure Logging

The bundle uses standard logging (Monolog). It uses the channel "javascript_minify".

## Usage

1. You can use the service manually and call the "minifyJsContent" function to minify your JavaScript.

```php
$minfiyservice = new MinifyJsService();
$minfiyservice->minifyJsContent($javaScriptContent);
```

2. Automatic JavaScript minification if event `chameleon_system_core.resource_collection_collected.javascript` was dispatched.

3. To add your own minifier, do the following:

- create a class the implements MinifyJsIntegrationInterface
- register a Symfony service for this class
- add the tag "chameleon_system.minify_js" to this service and specify an alias
- set the configuration value `js_minifier_to_use` to this alias

## Note

This bundle does not contain any javascript minification integration. You will need an external integration bundle, such as chameleon-system/minifier-js-jshrink-bundle or your own implementation.