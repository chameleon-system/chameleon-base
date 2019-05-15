# Building the CoreUI Theme for Chameleon

## CoreUI as Requirement in `composer.json`

CoreUI is defined as a requirement in the `composer.json` file. Thus CoreUI will be installed when calling `composer install`. Yet `composer` will not install all dependencies of CoreUI. To do that you need to build CoreUI using a script defined in CoreUI's `package.json` file. You don't have to call that script directly. Instead use one of the scripts described below.  

## Scripts

You will find scripts to build CoreUI including overrides necessary for Chameleon in `./package.json`.
Call these scripts on the console by typing:

`npm run <script-name>`

Example: `npm run css`

See a list of scripts below:

1. `build` Please call this script once after having installed Chameleon. It will do the following steps:
  * Install all requirements for the build process
  * Install all CoreUI requirements  
  * Install 'Perfect Scrollbar'
  * Copy all necessary files for "Perfect Scrollbar" to the target directory
  * Copy CoreUI's JavaScript files to the target directory
  * Run the `css` command (see below)
  
2. `css` Please call this script whenever you make changes to your custom scss sources. I will do the following steps:
  * Lint scss sources to enforce conventions and avoid frequent errors
  * Compile scss sources to css
  * Minify the resulting css  

## Target Directory   
All resulting files can be found in the target directory:  `customer/vendor/chameleon-system/chameleon-base/src/CoreBundle/Resources/public/themes/coreui`

