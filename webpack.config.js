const path               = require( 'path' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );

module.exports = {
	entry: {
		'carousel': './src/js/carousel.js',
	},

	output: {
		filename: '[name].js',
		path:     path.resolve( __dirname, 'assets/js' ),
		clean:    false,
	},

	externals: {
		jquery: 'jQuery',
	},

	module: {
		rules: [
			{
				test:    /\.js$/,
				exclude: /node_modules/,
				use:     {
					loader:  'babel-loader',
					options: { presets: [ '@babel/preset-env' ] },
				},
			},
			{
				test: /\.scss$/,
				use:  [
					MiniCssExtractPlugin.loader,
					'css-loader',
					{
					loader:  'sass-loader',
					options: { api: 'modern' },
				},
				],
			},
			{
				test: /\.css$/,
				use:  [
					MiniCssExtractPlugin.loader,
					'css-loader',
				],
			},
		],
	},

	plugins: [
		new MiniCssExtractPlugin( {
			filename: '../css/[name].css',
		} ),
	],
};
