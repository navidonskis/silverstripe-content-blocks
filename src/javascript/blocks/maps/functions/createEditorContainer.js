export function createEditorContainer(container, translation = {}, data = {}) {
    let editor = document.createElement('div');

    editor.setAttribute('class', 'bg-overlay edit-marker');
    editor.innerHTML = `
        <div class="close" data-action="close" title="${translation.close}">&#9587;</div>
        <div class="valign">
            <div class="edit-marker-form">
                <h1>${translation.editMarker}</h1>
                
                <div class="field">
                    <label for="address">${translation.address}</label>
                    <input type="text" id="address" name="address" placeholder="${translation.address}" class="add-marker-form__address" value="${data.address}" />
                </div>
                
                <div class="field">
                    <input type="checkbox" id="display-window" name="displayWindow" ${data.displayWindow ? ' checked="checked"' : ''} />
                    <label for="display-window">${translation.displayInfo}</label>
                </div>
                
                <div class="field">
                    <label for="content">${translation.content}</label>
                    <textarea name="content" id="content" rows="5" placeholder="${translation.content}">${data.content}</textarea>
                </div>
                
                <div class="field actions">
                    <button name="delete" class="button delete">${translation.delete}</button>
                    <button name="save" class="button save">${translation.save}</button>
                </div>
            </div>
        </div>
    `;

    container.append(editor);

    editor.querySelector('*[data-action="close"]').addEventListener('click', () => editor.parentNode.removeChild(editor));

    return editor;
}