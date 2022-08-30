const webpack = require('webpack');
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
let config = {
	mode: 'development',
	entry: {
		theme: ['./frontend/js/front.js','./frontend/css/theme.scss']
	},
	output:{
		filename: '[name].js',
		path: path.resolve(__dirname,'../../public/assets/js')
	}
    ,
	module: {
    rules: [
      {
        test: /\.js/,
        loader: 'babel-loader',
      },
      {
        test: /\.scss$/,
        use:[ 
            MiniCssExtractPlugin.loader,
            'css-loader',
            'postcss-loader',
            'sass-loader',
          ],
      },
      {
        test: /.(png|woff(2)?|eot|otf|ttf|svg|gif)(\?[a-z0-9=\.]+)?$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              name: '../css/[hash].[ext]',
            },
          },
        ],
      },
      {
        test: /\.css$/,
        use: [MiniCssExtractPlugin.loader, 'style-loader', 'css-loader', 'postcss-loader'],
      },
    ],
  },
	externals: {
		//$: 'jQuery',
              //   jquery: 'jQuery',
		//jquery: 'jQuery',
		Tether: 'tether',
	},
	plugins: [
		
                 new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery'
        }),
        new MiniCssExtractPlugin({filename: path.join('..', 'css', '[name].css')}),
	]
};

if (process.env.NODE_ENV === 'production') {
  config.optimization = {
    minimizer: [
      new UglifyJsPlugin({
        sourceMap: false,
        uglifyOptions: {
          compress: {
            sequences: true,
            conditionals: true,
            booleans: true,
            if_return: true,
            join_vars: true,
            drop_console: true,
          },
          output: {
            comments: false,
          },
        }
      })
    ]
  }
}
module.exports =   config;