/**
 * @param image
 * @param container
 * @returns {Element}
 */
export function createNavigationButton(image, container) {
    let control = container.querySelector('.gmnoprint.gm-bundled-control.gm-bundled-control-on-bottom > .gmnoprint:first-child > div');
    let navigation = document.createElement('div');

    navigation.setAttribute('class', 'maps-editor__add-marker-container');
    navigation.innerHTML = `
        <div class="maps-editor__add-marker-subcontainer">
            <img src="${image}" draggable="false" class="maps-editor__add-new-marker" />
        </div>
    `;

    let separator = document.createElement('div');

    separator.setAttribute('class', 'maps-editor__separator');

    control.prepend(navigation);
    navigation.parentNode.insertBefore(separator, navigation.nextSibling);

    return navigation;
}