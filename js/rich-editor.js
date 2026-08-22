let richMediaType = '';

function formatDoc(cmd, value = null) {
    const editor = document.getElementById('rich-editor');
    if (!editor) return;

    if (cmd === 'fontName') {
        const selection = window.getSelection();
        if (selection.rangeCount > 0 && selection.toString().length > 0) {
            const range = selection.getRangeAt(0);
            const span = document.createElement('span');
            span.style.setProperty('font-family', value, 'important');
            
            const clone = range.cloneContents();
            span.appendChild(clone);
            range.deleteContents();
            range.insertNode(span);
        } else {
            editor.style.setProperty('font-family', value, 'important');
        }
    } else if (cmd === 'foreColor') {
        const selection = window.getSelection();
        if (selection.rangeCount > 0 && selection.toString().length > 0) {
            const range = selection.getRangeAt(0);
            const span = document.createElement('span');
            span.style.setProperty('color', value, 'important');
            const clone = range.cloneContents();
            span.appendChild(clone);
            range.deleteContents();
            range.insertNode(span);
        } else {
            editor.style.setProperty('color', value, 'important');
        }
    } else if (cmd === 'backColor') {
        const selection = window.getSelection();
        if (selection.rangeCount > 0 && selection.toString().length > 0) {
            const range = selection.getRangeAt(0);
            const span = document.createElement('span');
            span.style.setProperty('background-color', value, 'important');
            const clone = range.cloneContents();
            span.appendChild(clone);
            range.deleteContents();
            range.insertNode(span);
        } else {
            editor.style.setProperty('background-color', value, 'important');
        }
    } else if (cmd === 'justifyLeft' || cmd === 'justifyCenter' || cmd === 'justifyRight' || cmd === 'justifyFull') {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            let alignValue = 'left';
            if (cmd === 'justifyCenter') alignValue = 'center';
            else if (cmd === 'justifyRight') alignValue = 'right';
            else if (cmd === 'justifyFull') alignValue = 'justify';

            document.execCommand(cmd, false, value);
            
            const anchorNode = selection.anchorNode;
            if (anchorNode) {
                let parentP = anchorNode.parentElement;
                while (parentP && parentP.id !== 'rich-editor' && parentP.tagName !== 'P' && parentP.tagName !== 'DIV') {
                    parentP = parentP.parentElement;
                }
                if (parentP && parentP.id !== 'rich-editor') {
                    parentP.style.setProperty('text-align', alignValue, 'important');
                }
            }
        }
    } else {
        document.execCommand(cmd, false, value);
    }
    editor.focus();
}
function changeFont(element) { formatDoc('fontName', element.value); }
function changeSize(element) { formatDoc('fontSize', element.value); }
function changeTextColor(element) { formatDoc('foreColor', element.value); }
function changeHighlightColor(element) { formatDoc('backColor', element.value); }

function changeLineSpacing(element) {
    const spaceValue = element.value;
    const editor = document.getElementById('rich-editor');
    if (editor) {
        editor.style.setProperty('line-height', spaceValue, 'important');
    }
}

function triggerRichUpload(type) {
    richMediaType = type;
    const uploader = document.getElementById('rich-uploader');
    if (!uploader) return;
    if (type === 'image') uploader.accept = 'image/*';
    else if (type === 'video') uploader.accept = 'video/*';
    else if (type === 'audio') uploader.accept = 'audio/*';
    uploader.click();
}

function handleRichMedia(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const originalSrc = e.target.result;
            if (richMediaType === 'image' && confirm("Do you want to crop this image to a square?")) {
                const img = new Image();
                img.src = originalSrc;
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    const size = Math.min(img.width, img.height);
                    canvas.width = size;
                    canvas.height = size;
                    const startX = (img.width - size) / 2;
                    const startY = (img.height - size) / 2;
                    ctx.drawImage(img, startX, startY, size, size, 0, 0, size, size);
                    const croppedSrc = canvas.toDataURL(file.type);
                    insertHtmlComponent(croppedSrc);
                };
            } else {
                insertHtmlComponent(originalSrc);
            }
            input.value = '';
        };
        reader.readAsDataURL(file);
    }
}

function insertHtmlComponent(srcData) {
    let htmlComponent = '';
    if (richMediaType === 'image') {
        htmlComponent = `<img src="${srcData}" class="editor-uploaded-image" alt="Uploaded Image" />`;
    } else if (richMediaType === 'video') {
        htmlComponent = `<video src="${srcData}" controls class="editor-uploaded-video"></video>`;
    } else if (richMediaType === 'audio') {
        htmlComponent = `<audio src="${srcData}" controls class="editor-uploaded-audio"></audio>`;
    }
    const editor = document.getElementById('rich-editor');
    if (editor) {
        editor.focus();
        document.execCommand('insertHTML', false, htmlComponent);
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const blogForm = document.getElementById('main-blog-form');
    if (blogForm) {
        blogForm.onsubmit = function() {
            const editor = document.getElementById('rich-editor');
            const hiddenInput = document.getElementById('hidden-content');
            if (editor && hiddenInput) {
                hiddenInput.value = editor.innerHTML;
            }
        };
    }
});
