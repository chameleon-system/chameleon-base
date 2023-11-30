// Project specific configuration. Please configure any sources for bundling here.
// There are additional configuration options available as explained inline.
const path = require('path');

module.exports = () => {

    return {
        // Define different entries here.
        // The entry's key will be used as file name with 'js' as suffix for JavaScript and `css` for Styles.
        // The entry's value is expected to be an array containing files to be bundled.
        // For the example given below a file named `example_entry.js` will be created containing bundled sources
        // from file1.js, file2.js, file3.js and `example_entry.css` for file1.scss and file2.scss.
        entry: {
            coreuiTheme: [
                path.resolve(__dirname, '../public/themes/standard/src/scss/main.scss'),
            ]
        },
        // Path to put bundled sources relatively to the path of `webpack.config.js`
        outputPath: '../public/themes/standard/coreui/',
        // Source maps as explained here: https://webpack.js.org/configuration/devtool/
        // Default values are `cheap-source-map` for production and `eval-cheap-module-source-map` for development.
        sourceMap: {
            production: 'nosources-source-map',
            development: 'inline-source-map'
        },
        // Define any custom key/value pairs here. The keys as in the examples below will be searched and replaced in
        // the bundled sources by the corresponding values. Thus any occurrence of 'EXAMPLE1' in the bundled code will
        // be replaced by a boolean `true`.
        // Please use screaming snake case for the keys and make sure that keys are only used as placeholders.
        // Please note: Any occurrence of 'DEVELOPMENT' in the bundled code will be replaced by `true` when the
        // build process is in dev mode (else false) as a default behavior.
        define: {
            // EXAMPLE1: true,
            // EXAMPLE2: 'arbitrary value'
            // EXAMPLE3: {1: 'test', 'two': 2}
        }
    };
};
