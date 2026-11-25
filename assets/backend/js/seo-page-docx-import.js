/* "Import .docx" button on the SEO page form: file -> mammoth -> cleanDocxHtml -> Summernote + Title. */
$(function () {
    'use strict';

    var $input = $('#docx-import-input');
    if (!$input.length || typeof mammoth === 'undefined' || typeof window.cleanDocxHtml !== 'function') {
        return;
    }

    var MAX_BYTES = 50 * 1024 * 1024;
    var $report = $('#docx-import-report');
    var $content = $('#content');
    var $button = $('#docx-import-button');
    var buttonText = $button.text();

    function escapeHtml(text) {
        return $('<span>').text(text).html();
    }

    function setBusy(busy) {
        $button.prop('disabled', busy).text(busy ? 'Importing…' : buttonText);
    }

    // Parsed with DOMParser, not jQuery, so nothing in the current content runs.
    function editorHasContent() {
        var doc = new DOMParser().parseFromString($content.summernote('code') || '', 'text/html');
        return doc.body.textContent.trim() !== '' || doc.body.querySelector('img, table, iframe, video') !== null;
    }

    $button.on('click', function () {
        $input.val('');
        $input.trigger('click');
    });

    $input.on('change', function () {
        var file = this.files[0];
        if (!file) {
            return;
        }
        if (!/\.docx$/i.test(file.name)) {
            notifyEvs('error', 'Please choose a .docx file.');
            return;
        }
        if (file.size > MAX_BYTES) {
            notifyEvs('error', 'The file is larger than 10 MB.');
            return;
        }

        if (editorHasContent() && !window.confirm('Replace the current content with the imported document?')) {
            return;
        }

        setBusy(true);

        var reader = new FileReader();
        reader.onerror = function () {
            notifyEvs('error', 'Could not read the file.');
            setBusy(false);
        };
        reader.onload = function (event) {
            // Started from a resolved promise so a synchronous throw also reaches .catch and restores the button.
            Promise.resolve()
                .then(function () {
                    return mammoth.convertToHtml({ arrayBuffer: event.target.result }, {
                        // Images are never imported, so don't embed them as base64; the cleaner still counts and removes them.
                        convertImage: mammoth.images.imgElement(function () {
                            return { src: '' };
                        })
                    });
                })
                .then(function (converted) {
                    var result = window.cleanDocxHtml(converted.value);
                    var warnings = result.warnings.concat(converted.messages.map(function (m) {
                        return m.message;
                    }));

                    $content.summernote('code', result.html);

                    var titleNote = '';
                    var $title = $('#title');
                    if (result.title && $title.val().trim() === '') {
                        $title.val(result.title);
                        titleNote = ' Title set from the document\'s Heading 1.';
                    }

                    var stats = result.stats;
                    var html = '<div class="alert alert-info py-2 mb-2">Imported: ' + stats.h2 + ' H2, ' + stats.h3 + ' H3, '
                        + stats.tables + ' tables, ' + stats.listItems + ' list items.' + escapeHtml(titleNote) + '</div>';
                    if (warnings.length) {
                        html += '<div class="alert alert-warning py-2 mb-2"><ul class="mb-0">'
                            + warnings.map(function (w) { return '<li>' + escapeHtml(w) + '</li>'; }).join('')
                            + '</ul></div>';
                    }
                    $report.html(html);
                })
                .catch(function (error) {
                    // simple-notify renders the message as HTML.
                    notifyEvs('error', 'Could not read this document: ' + escapeHtml(String((error && error.message) || error)));
                })
                .then(function () {
                    setBusy(false);
                });
        };
        reader.readAsArrayBuffer(file);
    });
});
