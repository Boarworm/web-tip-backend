let mix = require('laravel-mix');
var SVGSpritemapPlugin = require('svg-spritemap-webpack-plugin');

const jsFileList = [
    'assets/src/js/app',
];

mix.options(
    {
        processCssUrls: false,
        fileLoaderDirs: {
            fonts: 'fonts'
        },
        terser: {
            extractComments: false,
        }
    });


let postCssPlugins = [
    require('tailwindcss'),
];

mix.setPublicPath('assets');

mix.babelConfig({
    plugins: ['@babel/plugin-syntax-dynamic-import'],
});

mix.webpackConfig(webpack => ({
    output: {
        publicPath: '/themes/main/assets/',
    },
    plugins: [
        new SVGSpritemapPlugin('assets/src/icons/*.svg',
            {
                output: {
                    filename: 'dist/sprite.svg',
                    svgo: true,
                    chunk: {
                        keep: true
                    }
                },
                sprite: {
                    prefix: 'icon-',
                    generate: {
                        title: false,
                        symbol: true,
                    }
                },
            }
        ),
    ]
}))

mix.copy('assets/src/fonts', 'assets/dist/fonts');

jsFileList.forEach(fileName => mix.js(`./${fileName}.js`, 'assets/dist/js'));

mix.postCss('assets/src/css/app.css', 'assets/dist/css', postCssPlugins);

mix.browserSync({
    proxy: 'web-tip.local:8000',
    open: false,
    notify: false,
    files: [
        './layouts/*.{htm,js}',
        './pages/**/*.{htm,js}',
        './partials/**/*.{htm,js}',
        './content/**/*.{htm,js}'
    ]
});

mix.sourceMaps(true, 'source-map');
