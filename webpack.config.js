import path from 'path';
import { fileURLToPath } from 'url';
import pkg from 'webpack';
const { ProvidePlugin } = pkg;
import CopyWebpackPlugin from 'copy-webpack-plugin';
import MiniCssExtractPlugin from 'mini-css-extract-plugin';
import CssMinimizerPlugin from 'css-minimizer-webpack-plugin';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export default {
    entry: {
        mainStyles: './public/scss/main.scss',
        loginStyles: './public/scss/login.scss',
        errorStyles: './public/scss/error.scss',
        vendor: './public/scss/vendor.scss',
        app: './public/js/app.js',
    },
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: 'js/[name].js',
        chunkFilename: 'js/chunks/[name].js', // Bez hash, aby nazwy były przewidywalne
        assetModuleFilename: '[ext]/[name][ext]',
        clean: true,
        //publicPath: '/', // Ensure the public path is set for dynamic imports
    },
    resolve: {
        alias: {
            'jquery-ui': path.resolve(__dirname, 'node_modules/jquery-ui-dist/jquery-ui.min.js'),
            'jquery-ui-css': path.resolve(__dirname, 'node_modules/jquery-ui-dist/jquery-ui.min.css'),

            'jquery-confirm': path.resolve(__dirname, 'node_modules/jquery-confirm/dist/jquery-confirm.min.js'),
            'jquery-confirm-css': path.resolve(__dirname, 'node_modules/jquery-confirm/dist/jquery-confirm.min.css'),

            'bootstrap': path.resolve(__dirname, 'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js'),
            'bootstrap-css': path.resolve(__dirname, 'node_modules/bootstrap/dist/css/bootstrap.min.css'),

            'fontawesome-css': path.resolve(__dirname, 'node_modules/@fortawesome/fontawesome-free/css/all.min.css'),
            'bootstrap-icons-css': path.resolve(__dirname, 'node_modules/bootstrap-icons/font/bootstrap-icons.min.css'),

            'bootstrap-menu-editor-css': path.resolve(__dirname, 'node_modules/@maxsoft/bootstrap_menu_editor/lib/css/bootstrap_menu_editor.min.css'),

            'bootstrap-table': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/bootstrap-table.min.js'),
            'bootstrap-table-locale': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/locale/bootstrap-table-pl-PL.min.js'),
            'bootstrap-table-cookie': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/cookie/bootstrap-table-cookie.min.js'),
            'bootstrap-table-filter-control': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/filter-control/bootstrap-table-filter-control.min.js'),
            'bootstrap-table-sticky-header': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/sticky-header/bootstrap-table-sticky-header.min.js'),
            'bootstrap-table-toolbar': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/toolbar/bootstrap-table-toolbar.min.js'),
            'bootstrap-table-auto-refresh': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/auto-refresh/bootstrap-table-auto-refresh.min.js'),
            'bootstrap-table-page-jump-to': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/page-jump-to/bootstrap-table-page-jump-to.min.js'),
            'bootstrap-table-custom-view': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/custom-view/bootstrap-table-custom-view.min.js'),
            'bootstrap-table-mobile': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/mobile/bootstrap-table-mobile.min.js'),
            'bootstrap-table-multiple-sort': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/multiple-sort/bootstrap-table-multiple-sort.min.js'),
            'bootstrap-table-addrbar': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/addrbar/bootstrap-table-addrbar.min.js'),
            'xeditable': path.resolve(__dirname, 'public/js/bootstrap-editable.min.js'),
            'bootstrap-table-editable': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/editable/bootstrap-table-editable.min.js'),
            
            'table-dnd': path.resolve(__dirname, 'node_modules/tablednd/dist/jquery.tablednd.1.0.5.min.js'),
            'bootstrap-table-reorder-rows': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/reorder-rows/bootstrap-table-reorder-rows.min.js'),

            //'bootstrap-table-print': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/print/bootstrap-table-print.min.js'),
            //'bootstrap-table-export': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/export/bootstrap-table-export.min.js'),

            'bootstrap-table-css': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/bootstrap-table.min.css'),
            'bootstrap-table-filter-control-css': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/filter-control/bootstrap-table-filter-control.min.css'),
            'bootstrap-table-sticky-header-css': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/sticky-header/bootstrap-table-sticky-header.min.css'),
            'bootstrap-table-page-jump-to-css': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/page-jump-to/bootstrap-table-page-jump-to.min.css'),
            'bootstrap-table-reorder-rows-css': path.resolve(__dirname, 'node_modules/bootstrap-table/dist/extensions/reorder-rows/bootstrap-table-reorder-rows.min.css'),

            'fancybox': path.resolve(__dirname, 'node_modules/@fancyapps/ui/dist/fancybox/fancybox.js'),
            'fancybox-css': path.resolve(__dirname, 'node_modules/@fancyapps/ui/dist/fancybox/fancybox.css')
        },
        extensions: ['.js', '.css', '.scss'],
        modules: [
            path.resolve(__dirname, 'node_modules'),
            'node_modules',
        ],
    },
    module: {
        rules: [
            // NOWA REGUŁA DLA EXPOSE-LOADER (dla starych wtyczek)
            {
                test: /jquery\.js$/, // Testuje ścieżkę pliku jquery.js
                // LUB jeszcze prościej, jeśli chcesz polegać na aliasach/rozwiązywaniu:
                // test: 'jquery', // Webpack rozwiąże 'jquery' do ścieżki pliku
                use: [{
                    loader: 'expose-loader',
                    options: {
                        exposes: ['$', 'jQuery'], // Wystaw $ i jQuery globalnie
                    },
                }],
            },
            // NOWA REGUŁA DLA BOOTSTRAP (dla starych wtyczek)
            {
                test: /\.js$/, // Ogólna reguła dla plików JS
                // 'include', aby ograniczyć to tylko do modułu Bootstrap
                include: path.resolve(__dirname, 'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js'),
                use: [{
                    loader: 'expose-loader',
                    options: {
                        exposes: ['bootstrap'], // Wystaw obiekt 'bootstrap' globalnie
                    },
                }],
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        configFile: path.resolve(__dirname, 'babel.config.cjs'),
                    },
                },
            },
            {
                test: /\.scss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    'resolve-url-loader', //resolve-url-loader
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: true,
                            sassOptions: {
                                includePaths: [
                                    path.resolve(__dirname, 'node_modules'),
                                ],
                            },
                        },
                    },
                ],
            },
            {
                test: /\.css$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                ],
            },
            {
                test: /\.(woff|woff2|eot|ttf|otf)$/,
                type: 'asset/resource',
                generator: {
                    filename: 'fonts/[name][ext]',
                },
            },
            {
                test: /\.(png|svg|jpg|jpeg|gif|webp)$/,
                type: 'asset/resource',
                generator: {
                    filename: 'images/[name][ext]',
                },
            }
        ],
    },
    plugins: [
        new CopyWebpackPlugin({
            patterns: [
                {
                    from: 'public', to: '', // Kopiuj wszystko z folderu public do dist
                },
            ],
        }),
        new MiniCssExtractPlugin({
            filename: ({ chunk }) => {
                // Precyzyjne mapowanie nazw plików CSS
                if (chunk.name === 'mainStyles') return 'css/main.css';
                if (chunk.name === 'loginStyles') return 'css/login.css';
                if (chunk.name === 'errorStyles') return 'css/error.css';
                return 'css/app.css'; // Wszystkie inne CSS (vendor i chunki) do app.css
            },
            chunkFilename: 'css/app.css', // Wszystkie chunki CSS do app.css
        }),
        new ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
            'window.$': 'jquery',
            'global.jQuery': 'jquery'
        }),
    ],
    devServer: {
        proxy: {
            '/': 'http://localhost',
        },
        static: path.resolve(__dirname, 'dist'),
        port: 3000,
    },
    optimization: {
        minimizer: [
            `...`, // domyślne optymalizatory (np. Terser dla JS)
            new CssMinimizerPlugin(), // Kompresuj CSS
        ],
        splitChunks: {
            chunks: 'async',
            cacheGroups: {
                defaultVendors: false, // Wyłącza domyślną grupę dla vendorów
                default: false,       // Wyłącza domyślną grupę dla modułów nie-vendors
            },
        },
    },
    //stats: 'verbose', // Wyświetlaj szczegółowe informacje o kompilacji
    stats: {
        warnings: false, // Wyłącz ostrzeżenia
        errors: true, // Włącz błędy
    }
};