const webpack = require('webpack');
const path = require('path');
const glob = require('glob');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const env = process.env.ENV || 'production';
const prod = (env === 'production');
const dev = !prod;

const config = {
    context: path.resolve('./assets'),
    entry: {
        bundle: [
            path.resolve("./assets/js/app.js"),
            path.resolve("./assets/scss/style.scss"),
            ...glob.sync("./assets/images/**/*.@(png|jpe?(g)|gif|webp|svg)").map((s) => path.resolve(s))
        ]
    },
    output: {
        path: path.resolve("./public/dist"),
        publicPath: '/dist/',
        filename: (dev ? "[name].js" : "[name].[chunkhash:16].js"),
        chunkFilename: (dev ? "chunk-[id].[name].js" : "chunk-[id].[name].[chunkhash:16].js"),
    },

    watch: dev,
    devtool: dev ? "source-map" : false,

    resolve: {
        alias: {
            '@img': path.resolve("./assets/images/"),
            '@style': path.resolve("./assets/scss/"),
            '@font': path.resolve("./assets/fonts/"),
            '@': path.resolve("./assets/js/"),
        }
    },

    module: {
        rules: [
            {
                test: /\.js$/,
                use: 'babel-loader',
                exclude: /node_modules/
            },
            {
                test: /\.s?css$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    'sass-loader',
                    'postcss-loader'
                ]
            },
            {
                test: /\.(eot|ttf|woff2?)$/i,
                use: [{
                    loader: 'file-loader',
                    options: {
                        name: (dev ? "[name].[ext]" : "[name].[contenthash:16].[ext]")
                    }
                }],
            },
            {
                test: /\.(gif|webp|svg)$/i,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[path]' + (dev ? "[name].[ext]" : "[name].[contenthash:16].[ext]")
                        }
                    },
                    {
                        loader: 'image-webpack-loader',
                        options: {
                            disable: dev,
                        }
                    }
                ],
            },
            {
                test: /\.(png|jpe?g)$/i,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[path]' + (dev ? "[name].[ext]" : "[name].[contenthash:16].webp")
                        }
                    },
                    {
                        loader: 'image-webpack-loader',
                        options: {
                            disable: dev,
                            webp: {
                                quality: 100,
                                alphaQuality: 100,
                                lossless: true
                            }
                        }
                    }
                ],
            },
        ]
    },
    plugins: [
        new CleanWebpackPlugin(),
        new MiniCssExtractPlugin({
            filename: (dev ? "[name].css" : "[name].[fullhash:16].css"),
            chunkFilename: (dev ? "chunk-[id].[name].css" : "chunk-[id].[name].[fullhash:16].css")
        }),
        new WebpackManifestPlugin({
            publicPath: '/dist/',
        })
    ]
};

module.exports = config;
