const NodePolyfillPlugin = require("node-polyfill-webpack-plugin");
const TerserJSPlugin = require("terser-webpack-plugin");
const path = require("path");
const webpack = require("webpack");

module.exports = {
  optimization: {
    minimizer: [new TerserJSPlugin({})],
  },
  entry: ["./app.js"],
  output: {
    path: path.resolve(__dirname, "../public"),
    filename: "./index.js",
  },
  resolve: {
    alias: {
      react: "preact/compat",
      "react-dom": "preact/compat",
    },
  },
  module: {
    rules: [
      {
        test: /\.m?js$/,
        exclude: /(node_modules|bower_components)/,
        use: {
          loader: "babel-loader",
          options: {
            presets: ["@babel/preset-react", "@babel/preset-env"],
            plugins: [
              ["@babel/plugin-transform-runtime"],
              [
                "@babel/plugin-transform-react-jsx",
                {
                  pragma: "h",
                  pragmaFrag: "Fragment",
                },
              ],
            ],
          },
        },
      },
      {
        test: /\.css$/i,
        use: ["style-loader", "css-loader"],
      },
    ],
  },
  plugins: [
    new NodePolyfillPlugin(),
    new webpack.ProvidePlugin({
      Buffer: ["buffer", "Buffer"],
    }),
  ],
  experiments: {
    topLevelAwait: true,
  },
};
