export default function init(element) {
    let scrollPos = 0;
    setDefaultStyle();
    window.addEventListener('scroll', fixedHeader);

    function fixedHeader() {
        if ((document.body.getBoundingClientRect()).top > scrollPos) {
            element.classList.remove('!-top-32');
            if (scrollPos > -100) {
                element.classList.remove('bg-primary-darkest');
            }
        } else {
            element.classList.add('!-top-32', 'bg-primary-darkest');
        }
        scrollPos = (document.body.getBoundingClientRect()).top;
    }

    function setDefaultStyle(){
        if ((document.body.getBoundingClientRect()).top < 0) {
            element.classList.add('bg-primary-darkest');
        }
    }
}

