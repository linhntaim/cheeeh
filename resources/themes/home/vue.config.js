const TerserPlugin = require('terser-webpack-plugin')

module.exports = {
    outputDir: '../../../public/home',
    productionSourceMap: false,
    configureWebpack: {
        optimization: {
            minimizer: [
                new TerserPlugin({
                    terserOptions: {
                        output: {
                            comments: false,
                        },
                    },
                }),
            ],
        },
    }
}
