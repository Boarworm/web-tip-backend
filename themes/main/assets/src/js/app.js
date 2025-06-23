import './dynamic-imports';

import {setCookie, getCookie, deleteCookie} from './helpers';
import {scrollToElement} from './helpers';

window.setCookie = setCookie;
window.getCookie = getCookie;
window.deleteCookie = deleteCookie;
window.scrollToElement = scrollToElement;

// scroll to anchor
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const element = document.querySelector(this.getAttribute('href'));
        if (element) {
            e.preventDefault();
            element.scrollIntoView({behavior: "smooth", block: "start", inline: "nearest"});
        }
    });
});



