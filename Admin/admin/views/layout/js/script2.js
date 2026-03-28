import {iconLinks} from './fontawesome.js';

iconLinks.forEach(lk => {
    let tag = document.createElement('link');
    tag.rel = 'stylesheet';
    tag.href = lk;

    document.head.appendChild(tag);
});