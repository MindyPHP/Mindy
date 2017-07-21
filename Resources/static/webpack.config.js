'use strict';

let webpack = require('webpack'),
    WatchMissingNodeModulesPlugin = require('react-dev-utils/WatchMissingNodeModulesPlugin'),
    CaseSensitivePathsPlugin = require('case-sensitive-paths-webpack-plugin'),
    path = require('path');

const env = process.env.NODE_ENV || 'development';
const isProd = env === 'production';

module.exports = {
    devtool: isProd ? 'cheap-source-map' : 'eval',
    context: __dirname, // string (absolute path!)
    target: "web",
    entry: {
        admin: [
            // We ship a few polyfills by default:
            require.resolve('./polyfills'),

            './js/index.js',
        ],
    },
    output: {
        path: path.resolve(path.join(__dirname, '/../public/js')),
        filename: "[name].bundle.js",
        libraryTarget: 'umd'
    },
    module: {
        rules: [
            {
                test: require.resolve('jquery'),
                use: [
                    { loader: 'expose-loader', options: 'jQuery' },
                    { loader: 'expose-loader', options: '$' }
                ]
            },
            {
                test: require.resolve('./js/api'),
                use: [{ loader: 'expose-loader', options: 'api' }]
            },
            {
                test: require.resolve('./js/notify'),
                use: [{ loader: 'expose-loader', options: 'notify' }]
            },
            {
                test: /\.(js|jsx)$/,
                exclude: /node_modules/,
                loader: 'babel-loader',
                query: {
                    // This is a feature of `babel-loader` for webpack (not Babel itself).
                    // It enables caching results in ./node_modules/.cache/babel-loader/
                    // directory for faster rebuilds.
                    cacheDirectory: true
                }
            },
            {
                test: /\.json$/,
                loader: "json-loader"
            }
        ]
    },
    resolve: {
        modules: [
            path.resolve(__dirname, "js"),
            "node_modules",
        ],
        extensions: [".js", ".json", ".jsx"],
        alias: {
            "jquery-ui/widget": "jquery-ui/ui/widget.js"
        }
    },
    plugins: [
        new webpack.optimize.ModuleConcatenationPlugin(),
        new webpack.ProvidePlugin({
            'window.$': 'jquery',
            '$': 'jquery',
            'window.jQuery': 'jquery',
            'jQuery': 'jquery'
        }),
        // Moment.js is an extremely popular library that bundles large locale files
        // by default due to how Webpack interprets its code. This is a practical
        // solution that requires the user to opt into importing specific locales.
        // https://github.com/jmblog/how-to-optimize-momentjs-with-webpack
        // You can remove this if you don't use Moment.js:
        new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),
        new CaseSensitivePathsPlugin,
        new WatchMissingNodeModulesPlugin(path.resolve(__dirname, 'node_modules')),
        new webpack.NoEmitOnErrorsPlugin(),
        new webpack.optimize.UglifyJsPlugin({
            compress: {
                screw_ie8: true, // React doesn't support IE8
                warnings: false
            },
            mangle: {
                screw_ie8: true
            },
            output: {
                comments: false,
                screw_ie8: true
            }
        }),
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: JSON.stringify(env)
            }
        })
    ]
};