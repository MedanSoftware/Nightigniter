var library = 'Nightigniter';
var path = require('path');
var CompressionPlugin = require('compression-webpack-plugin');
var CopyPlugin = require('copy-webpack-plugin');
var CleanWebpackPlugin = require('clean-webpack-plugin').CleanWebpackPlugin;

module.exports = (env = 'development', arg) => {

	let config = {
		target: 'web',
		mode: env,
		plugins: [
			new CleanWebpackPlugin()
		],
		optimization: {
			minimizer: [],
			// splitChunks: {
			// 	chunks: 'all'
			// }
		},
		entry: {
			Nightigniter: path.resolve(__dirname, './src/Nightigniter.js')
		},
		output: {
			path: path.resolve(__dirname, 'dist'),
			filename: '[name].bundle.js',
			// chunkFilename: '[name].bundle.js',
			library: library,
			libraryTarget: 'umd',
			libraryExport: 'default',
			umdNamedDefine: true
		},
		resolve: {
			alias: {
				media: path.resolve(__dirname, '../../media/'),
				nightigniter: path.resolve(__dirname, '../../../')
			}
		},
		performance: {
			hints: process.env.NODE_ENV === 'production' ? 'warning' : false
		},
		module: {
			rules: [
				{
					test: /\.config$/,
					exclude: /(node_modules|bower_components)/,
					loader: 'raw-loader'
				},
				{
					test: /\.(ogg|mp3|wav|mpe?g)$/i,
					exclude: /(node_modules|bower_components)/,
					loader: 'url-loader',
					options: {
						esModule: false
					}
				}
			]
		}
	}

	if (env == 'production') {
		/**
		 * Compress files
		 */
		config.plugins.push(
			new CompressionPlugin({
				cache:true,
				deleteOriginalAssets:false
			})
		);

		/**
		 * copy .htaccess to output directory
		 */
		config.plugins.push(
			new CopyPlugin([
				{
					from : path.resolve(__dirname, './src/.htaccess'),
					to : path.resolve(__dirname, './dist/')
				}
			])
		);
	}

	return config;
}