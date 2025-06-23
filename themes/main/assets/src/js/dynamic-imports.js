import {initInView} from "./helpers";

document.querySelectorAll('[data-layout="header"]').forEach($el => {
    import('../../../partials/layout/header/header' /* webpackChunkName: "/dist/js/header" */).then(({default: init}) => init($el));
});

document.querySelectorAll('[data-layout="menu-mobile"]').forEach($el => {
    import('../../../partials/layout/menu-mobile/menu-mobile' /* webpackChunkName: "/dist/js/menu-mobile" */).then(({default: init}) => init($el));
});

document.querySelectorAll('.js-loader').forEach($el => {
    import('../../../partials/blocks/loader/loader' /* webpackChunkName: "/dist/js/loader" */).then(({default: init}) => init($el));
});

document.querySelectorAll('[data-block="codehighlighter"]').forEach($el => {
    initInView($el).then(() => {
        import('../../../partials/blocks/codehighlighter/codehighlighter' /* webpackChunkName: "/dist/js/codehighlighter" */).then(({default: init}) => init($el));
    });
});

document.querySelectorAll('[data-block="gallery-grid"]').forEach($el => {
    initInView($el).then(() => {
        import('../../../partials/blocks/gallery-grid/gallery-grid' /* webpackChunkName: "/dist/js/gallery-grid" */).then(({default: init}) => init($el));
    });
});