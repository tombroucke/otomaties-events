const path = require('path');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const AssetsPlugin = require('assets-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = (env, argv) => ({
	entry: {
		main: './assets/js/main.js',
		admin: './assets/js/admin.js',
	},
	output: {
		path: __dirname + '/public',
		filename: 'js/[name].[contenthash].js',
		publicPath: '',
	},
	module: {
		rules: [
		{
			test: /\.(jpg|png|gif|svg)$/,
			use: [
			{
				loader: 'file-loader',
				options: {
					name: "./images/[name].[ext]",
					publicPath: argv.mode === 'production' ? '../' : ''
				},
			},
			{
				loader: 'image-webpack-loader',
				options: {
					disable: argv.mode !== 'production',
				},
			}
			]
		},
		{
			test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
			exclude: [/images/],
			use: [{
				loader: 'file-loader',
				options: {
					name: '[name].[ext]',
					outputPath: 'fonts/'
				}
			}]
		}
		]
	},
	optimization: {
		minimizer: argv.mode === 'production' ? [ new TerserPlugin() ] : []
	},
	plugins: [
	new AssetsPlugin({
		path: path.join(__dirname, 'public'),
		filename: 'assets.json',
		prettyPrint: true,
		includeAllFileTypes: false,
		fileTypes: ['js', 'css']
	}),
	new BrowserSyncPlugin({
		port: 3000,
		proxy: 'https://wervel.local',
	}),
	]
});
