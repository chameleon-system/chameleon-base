# Building the CoreUI Theme for Chameleon

## CoreUI as Requirement in `composer.json`

To build CoreUI please require CoreUI in your `composer.json` (the CoreBundle already requires the bundle):

`composer require coreui/coreui`


## Scripts

You will find scripts to build all CoreUI assets including overrides for Chameleon in:
`vendor/chameleon-system/chameleon-base/src/CoreBundle/Resources/Theme/package.json`.

Call these scripts from within the project's root directory (usually `customer`) by typing:

`npm --prefix vendor/chameleon-system/chameleon-base/src/CoreBundle/Resources/Theme/ run <script-name>`

Example:
development: `npm --prefix vendor/chameleon-system/chameleon-base/src/CoreBundle/Resources/Theme run dev`
production: `npm --prefix vendor/chameleon-system/chameleon-base/src/CoreBundle/Resources/Theme run prod`
run all: `npm --prefix vendor/chameleon-system/chameleon-base/src/CoreBundle/Resources/Theme run build`

**Please make sure** to always call these scripts from within your project's root directory and provide the path to the theme's `package.json` file as a value to the `prefix` option as shown above. This ensures that scripts run properly in docker environments.

The following scripts will be useful during development and installation:

1. `build` Please call this script to re-install or update CoreUI. It will execute the following steps:
  * Install all requirements for the build process of CoreUI
  * Install all CoreUI requirements and assets
  * Copy CoreUI's JavaScript files to Chameleon's theme directory
  * Run the `css` command (see below)

2. `css` Please call this script whenever you make changes to your custom scss sources. It will perform the following steps:
  * Lint scss sources to enforce conventions and avoid frequent errors
  (see `vendor/chameleon-system/chameleon-base/src/CoreBundle/Resources/Theme/.stylelintrc.json`)
  * Compile scss sources to css
  * Minify the resulting css
  * Copy the result to the target directory (see below)

3. `watch-css` This script starts the watch mode for all files within `vendor/chameleon-system/chameleon-base/src/CoreBundle/Resources/public/themes/standard/src/scss` and compiles and minifies scss sources to the target directory exactly as the `css` script (see above) does. Thus calling the `css` script over and over again after every change is not necessary.
For performance reasons the watch mode drops linting. You might want to call the `css` command after having made your customizations.

The `package.json` file contains some other scripts that are not intended to be called separately.


## Target Directory
All resulting files can be found in the target directory:
`/vendor/chameleon-system/chameleon-base/src/CoreBundle/Resources/public/themes/standard`


## Customization for Chameleon

Please add your styles and imports to `vendor/chameleon-system/chameleon-base/src/CoreBundle/Resources/public/themes/standard/src/scss/styles.scss` to customize the CoreUI theme.
Please note the documentation within this file.


## Other Sources

At the time of writing this documentation not all `css` sources are included in the build process. Some are included by calling `GetHtmlHeadIncludes` or directly in the HTML code or by other means.