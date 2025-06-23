export function initInView(el) {
    return new Promise((resolve, reject) => {
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    resolve(true);
                    observer.unobserve(el);
                }
            });
        });
        observer.observe(el);
    });
}

export function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

export function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

export function deleteCookie(cname) {
    const d = new Date();
    d.setTime(d.getTime() + (24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=;" + expires + ";path=/";
}

export function scrollToElement(selector, offset = 0) {
    const element = document.querySelector(selector);
    if (element) {
        const offsetTop = element.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({
            top: offsetTop,
            behavior: 'smooth'
        });
    } else {
        console.error(`Element with selector '${selector}' not found.`);
    }
}