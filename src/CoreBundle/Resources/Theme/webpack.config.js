// Please note: This is the default configuration of webpack.
// Please do not change this file (unlessyou exactly what you're doing).
// Instead use `./webpack.custom.config.js` to configure your project.

const path = require('path');
const webpack = require('webpack');

module.exports = (env, options) => {
    console.info(`Webpack 'mode': ${options.mode}`);

    const configuration = require('./webpack.custom.config.js')();

    const isDevMode = 'development' === options.mode;
    const watch = isDevMode;
    const minimize = !isDevMode;
    const sourceMap = isDevMode ? (configuration.sourceMap.development || 'eval-cheap-module-source-map') : (configuration.sourceMap.production || 'cheap-source-map');
    const outputPath = configuration.sourceMap.outputPath || '../public/themes/standard/coreui/';
    const MiniCssExtractPlugin = require('mini-css-extract-plugin');

    console.info(`Minimizing: ${minimize ?  'yes' : ' no'}`);
    console.info(`Bundling with: "${sourceMap}" for JS.`);
    console.info(`Bundling with source maps for scss: `, isDevMode);

    function getDefineValues() {
        let definitions = {};
        for (let property in configuration.define) {
            if (configuration.define.hasOwnProperty(property)) {
                definitions[property] = JSON.stringify(configuration.define[property]);
            }
        }
        definitions['DEVELOPMENT'] = JSON.stringify(options.mode === 'development');

        return definitions;
    }

    return {
        entry: configuration.entry,
        optimization: {
            minimize: minimize
        },
        output: {
            path: path.resolve(__dirname, outputPath),
            filename: '[name]' + '.js'
        },
        watch: watch,
        devtool: sourceMap,
        plugins: [
            new MiniCssExtractPlugin({
                filename: '[name]' + '.css'
            }),
            new webpack.DefinePlugin(getDefineValues()),
        ],
        mode: options.mode,
        module: {
            rules: [
                {
                    test: /\.css$/i,
                    use: ['css-loader'],
                },
                {
                    test: /\.scss$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        {
                            loader: 'css-loader',
                            options: {
                                sourceMap: isDevMode,
                                url: false
                            }
                        },
                        {
                            loader: 'sass-loader',
                            options: {
                                sourceMap: isDevMode
                            }
                        }
                    ]
                }
            ]
        }

    };
};
