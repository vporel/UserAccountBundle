const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizer = require('css-minimizer-webpack-plugin')

module.exports = {
    mode: 'development',
    entry: './assets/index.js',  
    output: {
        path: __dirname+'/public',
        filename: 'script.js',
    },
    module: {
        rules: [
            {
                test: /\.jsx?$/,
                exclude: /node_modules\/(?!vporel)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ["@babel/preset-env", "@babel/preset-react"]
                    }
                }
            },
            {
                test: /\.(css|scss|sass)$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: { outputPath: '', name: '[name].css'}  //Output in the general output folder (public)
                    },
                    'sass-loader', 
                ]
            }
        ]
    },
    resolve: {
        extensions: ['.js','.jsx', '.ts', '.tsx'],
        modules: ['node_modules']
    },
    watchOptions: { ignored: /(node_modules|public)/ },
    plugins: [new MiniCssExtractPlugin()],
    optimization: {
        minimizer: [new CssMinimizer()]
    }
};