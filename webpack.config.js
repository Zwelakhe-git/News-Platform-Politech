import HtmlWebpackPlugin from 'html-webpack-plugin';
import MiniCssExtractPlugin from 'mini-css-extract-plugin';
import path from 'path';

export default {
    mode: "development",
    plugins: [
        new HtmlWebpackPlugin({template: './tmp/index.html'}),
        new MiniCssExtractPlugin({filename: '[name].03fab93e8c21ad73f9a8.css'})
    ],
    devServer: {
        hot: false,
        liveReload: true,
        port: 3000,
        open: true,
        static: {
            directory: path.resolve('public')
        },
        client: {
            overlay: {
                errors: true,
                warnings: false
            },
            logging: 'info',
            progress: true
        },
        devMiddleware: {
            publicPath: '/'
        }
    },
    entry: {
        index: './src/Admin/Views/News/index.jsx'  // изменено с .jsx на .tsx
    },
    output: {
        filename: '[name].16796132a0c23d48a61d.js',
        path: path.resolve('dist'),
        clean: true
    },
    module: {
        rules: [
            {
                test: /\.css$/i,
                use: [MiniCssExtractPlugin.loader, 'css-loader']
            },
            {
                test: /\.(js|jsx|ts|tsx)$/i,  // добавлены .ts и .tsx
                exclude: /node_modules/,      // исправлено: убраны кавычки
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: [
                            '@babel/preset-env',
                            '@babel/preset-react',
                            '@babel/preset-typescript'  // добавлен preset для TypeScript
                        ]
                    }
                }
            }
        ]
    },
    resolve: {
        extensions: ['.ts', '.tsx', '.js', '.jsx']  // порядок оптимизирован: сначала ts/tsx
    }
}