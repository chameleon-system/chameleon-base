Backend Assets
==============

CoreUI Theme
------------

The backend uses the CoreUI theme. To make changes e.g. to SCSS variables, proceed as follows:

- Install Node.js and NPM on your machine.
- Sources are located under `src/CoreBundle/Resources/asset-source/coreui-2.1.6`. Make changes there.
- In a terminal, go to `src/CoreBundle/Resources/asset-source/coreui-2.1.6`.
- Run `npm install` (ignore dependency warnings - we manage dependencies separately).
- Run `npm run dist`.
- Copy the resulting filess `coreui-standalone.css`, `coreui-standalone.css.map`, `coreui-standalone.min.css`,
  `coreui-standalone.min.css.map` from the directory `dist/css` to
  `src/CoreBundle/Resources/public/themes/standard/coreui/css`.
- Copy the resulting files `coreui.js`, `coreui.js.map`, `coreui.min.js`, `coreui.min.js.map` from the directory
  `dist/js` to `src/CoreBundle/Resources/public/themes/standard/coreui/js`.

Please remember create a pull request that contains both the changed source and the resulting files.
