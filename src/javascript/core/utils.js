export const utils = {

    /**
     * did element has class name
     *
     * @param element
     * @param className
     * @returns {boolean}
     */
    hasClass: (element, className) => {
        return element.classList ? element.classList.contains(className) : new RegExp('\\b' + className + '\\b').test(element.className);
    },

    /**
     * add class to the element
     *
     * @param element
     * @param className
     */
    addClass: (element, className) => {
        if (element.classList) {
            element.classList.add(className);
        } else if (!this.hasClass(element, className)) {
            element.className += '' + className;
        }
    },

    /**
     * remove class from the element
     *
     * @param element
     * @param className
     */
    removeClass: (element, className) => {
        if (element.classList) {
            element.classList.remove(className);
        } else {
            element.className = element.className.replace(new RegExp('\\b' + className + '\\b', 'g'), '');
        }
    },

    /**
     * function will generate random id string only
     *
     * @returns {string}
     */
    randomId: () => {
        return (0 | Math.random() * 9e6).toString(36);
    },

    /**
     * escape html entities
     *
     * @param text
     * @returns {*}
     */
    escapeEntity: (text) => {
        return text.replace(/&/g, '&amp;')
            .replace(/>/g, '&gt;')
            .replace(/</g, '&lt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/`/g, '&#96;');
    },

    /**
     * convert escaped html entities to symbols
     *
     * @param text
     * @returns {*}
     */
    convertEntity: (text) => {
        const span = document.createElement('span');

        return text
            .replace(/&[#A-Za-z0-9]+;/gi, (entity) => {
                span.innerHTML = entity;
                return span.innerText;
            });
    },

    /**
     * function rendering given template within options included. Example
     * how to right use this function:
     *
     * const template = (picture, pictures) => utils.render`
     *  <img src="${picture}" />
     *
     *  ${pictures.map(item => utils.render`
     *      <img src="${item.source}" alt="${item.alt}" />
     *  `)}
     * `
     *
     * template(options.picture, options.pictures); // return string
     *
     * @param sections
     * @param substitutes
     * @returns {string}
     */
    render: (sections, ...substitutes) => {
        let raw = sections.raw;
        let result = '';

        substitutes.forEach((substitute, i) => {
            let lit = raw[i];

            if (Array.isArray(substitute)) {
                substitute = substitute.join('');
            }

            if (lit.endsWith('$')) {
                subst = this.escapeEntity(substitute);
                lit = lit.slice(0, -1);
            }
            result += lit;
            result += substitute;
        });

        result += raw[raw.length - 1];

        return result;
    }
};