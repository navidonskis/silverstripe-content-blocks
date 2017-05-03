export function createAddnewContainer(container, translation = {close: '', address: ''}) {
    let overlay = document.createElement('div');

    overlay.setAttribute('class', 'bg-overlay add-new-address');
    overlay.innerHTML = `
        <div class="close" data-action="close" title="${translation.close}">&#9587;</div>
        <div class="valign">
            <div class="edit-marker-form">
                <label for="address">${translation.address}</label>
                <input type="text" name="address" id="address" placeholder="${translation.address}" class="add-marker-form__address"/>
            </div>
        </div>
    `;

    container.append(overlay);

    overlay.querySelector('*[data-action="close"]').addEventListener('click', () => overlay.parentNode.removeChild(overlay));

    return overlay;
}