# Chameleon System MinifierJsJshrinkBundle

This bundle uses JShrink to minify JavaScript content. To do this, the bundle requires an external bundle called JShrink.

## Installation

### Step 1: Download the Bundle

Open a command console in the project directory and execute the following command to download the latest stable version of this bundle:

```bash
$ composer require chameleon-system/minifier-js-jshrink-bundle "@stable"
```

This command requires you to have Composer installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation. Be sure to adjust the version information "@stable" to the actual version you need.

### Step 2: Enable the Bundle

Then, enable the bundle by adding the following line in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...

public function registerBundles()
{
    $bundles = array(
        // ...
        new \ChameleonSystem\MinifierJsJshrinkBundle\ChameleonSystemMinifierJsJshrinkBundle(),
    );
}
```

### Step 3: Configure JShrink as JavaScript Minifier to Use

To enable jshrink as js minifier add this to your config.yml

```yaml
chameleon_system_java_script_minification:
    js_minifier_to_use: "jshrink"
```

### Optional Step 4: Chameleon Special Configuration

Enable resource collection and resource collection minify (both are by default enabled for the prod environment):

```yaml
parameters:
    chameleon_system_core.resources.enable_external_resource_collection: true
    chameleon_system_core.resources.enable_external_resource_collection_minify: true
```

## Usage

This bundle does not require explicit usage. See the documentation of the JavaScriptMinificationBundle for information on the JavaScript minification process.