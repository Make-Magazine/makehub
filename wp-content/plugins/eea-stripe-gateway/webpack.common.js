const path = require( 'path' );
const assets = './assets/src/';
// const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );
// const combineLoaders = require( 'webpack-combine-loaders' );
// const autoprefixer = require( 'autoprefixer' );
const externals = {
	Stripe: 'Stripe',
	jquery: 'jQuery',
	stripeElementsArgs: 'stripeElementsArgs',
};
/** see below for multiple configurations.
 /** https://webpack.js.org/configuration/configuration-types/#exporting-multiple-configurations */
const config = [
	{
		configName: 'stripe',
		entry: {
			'eventespresso-stripe-elements': assets + 'stripe-elements.js',
		},
		externals,
		output: {
			filename: '[name].[chunkhash].dist.js',
			path: path.resolve( __dirname, 'assets/dist' ),
		},
		module: {
			rules: [
				{
					test: /\.js$/,
					exclude: /node_modules/,
					use: 'babel-loader',
				},
			],
		},
		watchOptions: {
			poll: 1000,
		},
	},
];
module.exports = config;
