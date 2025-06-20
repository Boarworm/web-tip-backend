export default function init(element) {
    window.showLoader = function () {
        element.classList.remove('transition-opacity', 'duration-1000', 'opacity-0');
        element.classList.add('opacity-100', '!z-20');
    }
    window.hideLoader = function () {
        element.classList.add('transition-opacity', 'duration-1000', 'opacity-0');
        element.classList.remove('opacity-100');
        setTimeout(() => {
            element.classList.remove('!z-20');
        }, 1000);
    }
}