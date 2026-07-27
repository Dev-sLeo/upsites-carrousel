const path               = require( 'path' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );

module.exports = {
	entry: {
		'accordion-slider': './src/js/accordion-slider.js',
		'cards-carousel':   './src/js/cards-carousel.js',
		'mega-menu-nav':    './src/js/mega-menu-nav.js',
		'button':           './src/js/button.js',
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
		],
	},

	plugins: [
		new MiniCssExtractPlugin( {
			filename: '../css/[name].css',
		} ),
	],
};
