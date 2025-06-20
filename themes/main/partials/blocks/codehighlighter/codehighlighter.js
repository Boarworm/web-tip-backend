import hljs from 'highlight.js';
import 'highlight.js/styles/agate.css';

export default function init(element) {
    element.querySelectorAll('pre code').forEach((el) => {
        hljs.highlightElement(el);
    });
}