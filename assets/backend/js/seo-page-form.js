/* SEO page admin form: slug, group suggestions, counters, content editor, image preview, FAQ rows. */

/*
 * Content editor. custom.js initialises every `.summernote`; this textarea has the class
 * `seo-page-editor` instead, so it gets a toolbar limited to what the seo_content purifier keeps
 * (no H1, no colours). Images are uploaded to the server (never embedded as base64, which the
 * purifier strips) and each one needs alt text before it is inserted.
 */
function initContentEditor(slugify) {
    'use strict';

    var $content = $('#content.seo-page-editor');
    if (!$content.length || typeof $.fn.summernote !== 'function') {
        return;
    }

    var uploadUrl = $content.data('image-upload-url');
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var MAX_BYTES = 2 * 1024 * 1024;
    var TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    // Same rule as SeoPageRequest::FILE_NAME_ALT: a bare file name, not a sentence that mentions one.
    var FILE_NAME_ALT = /^[\w.-]+\.(jpe?g|png|webp|gif|svg)$/i;
    var pending = 0;

    function escapeHtml(text) {
        return $('<span>').text(String(text)).html();
    }

    function notifyError(message) {
        // simple-notify renders the text as HTML.
        notifyEvs('error', escapeHtml(message));
    }

    // The last segment of a URL's path (or a file's name), as SeoPageRequest compares it.
    function fileNameOf(src) {
        var path = String(src || '').split(/[?#]/)[0];
        return path.substring(path.lastIndexOf('/') + 1);
    }

    function altProblem(alt, srcName) {
        if (alt === '') {
            return 'Describe what the image shows.';
        }
        if (FILE_NAME_ALT.test(alt) || (srcName && alt === srcName)) {
            return 'The alt text must describe the image, not be a file name.';
        }
        return '';
    }

    // Alt text dialog: a Bootstrap modal appended to <body> (outside the page form), or
    // window.prompt if Bootstrap isn't available. Resolves with the alt text, or null on cancel.
    var altModal = null;
    function buildAltModal() {
        var $modal = $(
            '<div class="modal fade" id="seo-image-alt-modal" tabindex="-1" aria-labelledby="seo-image-alt-title" aria-hidden="true">'
            + '<div class="modal-dialog"><form class="modal-content" novalidate>'
            + '<div class="modal-header"><h2 class="modal-title h5" id="seo-image-alt-title">Image alt text</h2>'
            + '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cancel"></button></div>'
            + '<div class="modal-body">'
            + '<img class="img-fluid rounded mb-3 d-none" style="max-height: 160px" alt="">'
            + '<label class="form-label" for="seo-image-alt-input">Alt text</label>'
            + '<input class="form-control" id="seo-image-alt-input" maxlength="125" autocomplete="off">'
            + '<div class="invalid-feedback"></div>'
            + '<small class="text-muted d-block mt-1">Describe what the image shows, e.g. "Dashboard showing referring domains in Ahrefs".</small>'
            + '</div>'
            + '<div class="modal-footer"><button type="button" class="btn btn-outline-gray-600" data-bs-dismiss="modal">Cancel</button>'
            + '<button type="submit" class="btn btn-gray-800">Save</button></div>'
            + '</form></div></div>'
        ).appendTo('body');

        return { $el: $modal, modal: new bootstrap.Modal($modal[0]) };
    }

    // Every prompt goes through this one chain, so a second queue (e.g. a paste during a drop's
    // upload) waits for the open prompt to close instead of taking over the shared modal.
    var altQueue = Promise.resolve();

    /**
     * Asks for alt text. `preview` is a File/Blob (shown through a blob URL that is created when
     * the prompt opens and always revoked when it closes) or an image URL. `srcName` is the image's
     * file name, which the alt text may not simply repeat. Resolves with the alt, or null on cancel.
     */
    function askAlt(current, preview, srcName) {
        var run = altQueue.then(function () {
            var blobUrl = preview instanceof Blob ? URL.createObjectURL(preview) : null;
            var done = function () {
                if (blobUrl) {
                    URL.revokeObjectURL(blobUrl);
                }
            };
            return promptAlt(current, blobUrl || preview || '', srcName).then(function (alt) {
                done();
                return alt;
            }, function (error) {
                done();
                throw error;
            });
        });
        altQueue = run.catch(function () {});
        return run;
    }

    function promptAlt(current, previewSrc, srcName) {
        if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            var answer = window.prompt('Alt text (describe what the image shows):', current || '');
            while (answer !== null && altProblem(answer.trim(), srcName)) {
                answer = window.prompt(altProblem(answer.trim(), srcName) + '\nAlt text:', answer);
            }
            return Promise.resolve(answer === null ? null : answer.trim());
        }

        altModal = altModal || buildAltModal();
        var $el = altModal.$el;
        var $input = $el.find('#seo-image-alt-input').val(current || '').removeClass('is-invalid');
        var $preview = $el.find('.modal-body img');
        if (previewSrc) {
            $preview.attr('src', previewSrc).removeClass('d-none');
        } else {
            $preview.attr('src', '').addClass('d-none');
        }

        return new Promise(function (resolve) {
            var result = null;
            $el.find('form').off('submit').on('submit', function (event) {
                event.preventDefault();
                var alt = $input.val().trim();
                var problem = altProblem(alt, srcName);
                if (problem) {
                    $input.addClass('is-invalid');
                    $el.find('.invalid-feedback').text(problem);
                    $input.trigger('focus');
                    return;
                }
                result = alt;
                altModal.modal.hide();
            });
            $el.off('shown.bs.modal').on('shown.bs.modal', function () {
                $input.trigger('focus');
            });
            $el.off('hidden.bs.modal').on('hidden.bs.modal', function () {
                resolve(result);
            });
            altModal.modal.show();
        });
    }

    // Busy feedback: the picture button is disabled and a note shows while uploads run.
    var $status = $('#content-image-status');
    function setBusy(delta) {
        pending += delta;
        var busy = pending > 0;
        $content.next('.note-editor').find('.note-icon-picture').closest('button').prop('disabled', busy);
        $status.text(busy ? 'Uploading image…' : '').toggleClass('d-none', !busy);
    }

    function upload(file) {
        var data = new FormData();
        data.append('image', file);
        data.append('slug', slugify($('#slug').val() || $('#nav_label').val()));
        if ($('#parent_id').val()) {
            data.append('parent_id', $('#parent_id').val());
        }

        return fetch(uploadUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
            body: data
        }).then(function (response) {
            return response.json().catch(function () {
                return {};
            }).then(function (json) {
                if (response.ok && json.url) {
                    return json.url;
                }
                var message = (json.errors && json.errors.image && json.errors.image[0])
                    || (response.status === 403 ? 'You do not have permission to upload images.' : '')
                    || (response.status === 413 ? 'The image is larger than the server accepts.' : '')
                    || (response.status === 419 ? 'Your session expired. Reload the page and try again.' : '')
                    || json.message
                    || 'The image could not be uploaded.';
                throw new Error(message);
            });
        });
    }

    function insertImage(url, alt) {
        var img = document.createElement('img');
        img.setAttribute('src', url);
        img.setAttribute('alt', alt);
        $content.summernote('editor.restoreRange');
        $content.summernote('insertNode', img);
    }

    function checkFile(file) {
        if (TYPES.indexOf(file.type) === -1) {
            return (file.name || 'The file') + ' is not a JPG, PNG or WebP image.';
        }
        if (file.size > MAX_BYTES) {
            return (file.name || 'The image') + ' is larger than 2 MB.';
        }
        return '';
    }

    // Covers the image dialog's file picker, drag and drop, and paste. One file at a time, in order.
    function onImageUpload(files) {
        Array.prototype.reduce.call(files, function (chain, file) {
            return chain.then(function () {
                var problem = checkFile(file);
                if (problem) {
                    notifyError(problem);
                    return null;
                }
                return askAlt('', file, file.name).then(function (alt) {
                    if (alt === null) {
                        return null;
                    }
                    setBusy(1);
                    return upload(file)
                        .then(function (url) {
                            insertImage(url, alt);
                        })
                        .catch(function (error) {
                            notifyError(error.message);
                        })
                        .then(function () {
                            setBusy(-1);
                        });
                });
            });
        }, Promise.resolve());
    }

    // "Image URL" in the image dialog: http(s) only (the purifier drops anything else).
    function onImageLinkInsert(url) {
        url = String(url || '').trim();
        if (!/^https?:\/\/\S+$/i.test(url)) {
            notifyError('Enter an image URL starting with http:// or https://.');
            return;
        }
        askAlt('', url, fileNameOf(url)).then(function (alt) {
            if (alt !== null) {
                insertImage(url, alt);
            }
        });
    }

    // "Alt text" button in the image popover (click an image in the editor).
    function altTextButton(context) {
        return $.summernote.ui.button({
            contents: 'Alt text',
            tooltip: 'Edit the image\'s alt text',
            click: function () {
                var img = context.invoke('editor.restoreTarget');
                if (!img || img.nodeName !== 'IMG') {
                    return;
                }
                context.invoke('imagePopover.hide');
                askAlt(img.getAttribute('alt') || '', img.getAttribute('src'), fileNameOf(img.getAttribute('src'))).then(function (alt) {
                    if (alt !== null) {
                        img.setAttribute('alt', alt);
                        context.invoke('editor.afterCommand');
                    }
                });
            }
        }).render();
    }

    $content.summernote({
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']],
            ['insert', ['link', 'picture']],
            ['view', ['codeview', 'help']]
        ],
        styleTags: ['p', 'h2', 'h3', 'h4', 'blockquote'],
        popover: {
            image: [['image', ['imageAlt', 'removeMedia']]],
            link: [['link', ['linkDialogShow', 'unlink']]]
        },
        buttons: { imageAlt: altTextButton },
        acceptImageFileTypes: TYPES.join(','),
        placeholder: 'Write the page content',
        height: 400,
        callbacks: {
            onImageUpload: onImageUpload,
            onImageLinkInsert: onImageLinkInsert
        }
    });
    $content.next('.note-editor').find('.note-editable').css('font-weight', '400');

    // Code view edits only reach the textarea when code view is closed; close it before submitting.
    $content.closest('form').on('submit', function (event) {
        if (pending > 0) {
            event.preventDefault();
            notifyError('Wait for the image upload to finish.');
            return;
        }
        if ($content.summernote('codeview.isActivated')) {
            $content.summernote('codeview.deactivate');
        }
        $content.val($content.summernote('code'));
    });
}

$(function () {
    'use strict';

    var $form = $('#seo-page-form');
    if (!$form.length) {
        return;
    }

    var isEdit = $form.data('edit') === 1;
    var $slug = $('#slug');
    var $parent = $('#parent_id');

    // Matches Laravel's Str::slug, which the server applies on save: accents are
    // stripped, '@' becomes '-at-', other punctuation is deleted ("Beginner's" ->
    // "beginners") and runs of spaces, '_' and '-' become one '-'. The global
    // generateSlug() helper differs (e.g. "Tools & Tips" -> "tools--tips").
    function slugify(value) {
        return String(value || '')
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/@/g, '-at-')
            .replace(/[^a-z0-9\s_-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    // Slug follows the nav label until the admin edits it by hand (create form only).
    var slugTouched = isEdit || $slug.val() !== '';
    $('#nav_label').on('input', function () {
        if (!slugTouched) {
            $slug.val(slugify($(this).val()));
        }
    });
    $slug.on('input', function () {
        slugTouched = true;
        var changed = slugify($slug.val()) !== slugify($slug.data('original'));
        $('#slug-change-warning').toggleClass('d-none', !(changed && $form.data('has-children') === 1));
    });

    // Group suggestions: groups already used by the selected parent's children.
    var groupsByParent = $form.data('groups') || {};
    function refreshParentHints() {
        var key = $parent.val() || 'root';
        var $list = $('#nav-group-options').empty();
        (groupsByParent[key] || []).forEach(function (group) {
            $list.append($('<option>').attr('value', group));
        });
        $('#category-route-notice').toggleClass('d-none', isEdit || ($parent.val() || '') !== '');
    }
    $parent.on('change', refreshParentHints);
    refreshParentHints();

    // Character counters: warn above the recommended length, never block.
    $('[data-counter]').each(function () {
        var $input = $(this);
        var limit = parseInt($input.data('counter'), 10);
        var $output = $('<small class="form-text d-block"></small>').insertAfter($input);
        function update() {
            var length = $input.val().length;
            $output.text(length + ' / ' + limit + ' recommended')
                .toggleClass('text-warning', length > limit)
                .toggleClass('text-muted', length <= limit);
        }
        $input.on('input', update);
        update();
    });

    initContentEditor(slugify);

    // Featured image preview.
    $('#featured_image').on('change', function () {
        var file = this.files[0];
        if (file) {
            $('#featured-image-preview').attr('src', URL.createObjectURL(file)).removeClass('d-none');
        }
    });

    // FAQ rows: add, remove, reorder. PHP receives rows in DOM order.
    // Refilled rows can keep keys like n3, so new keys add a timestamp to stay unique.
    var faqIndex = 0;
    $('#faq-add').on('click', function () {
        var key = 'n' + Date.now().toString(36) + '_' + (faqIndex++);
        $('#faq-rows').append($('#faq-row-template').html().replace(/__INDEX__/g, key));
    });
    $('#faq-rows')
        .on('click', '.faq-remove', function () {
            $(this).closest('.faq-row').remove();
        })
        .on('click', '.faq-up', function () {
            var $row = $(this).closest('.faq-row');
            $row.prev('.faq-row').before($row);
        })
        .on('click', '.faq-down', function () {
            var $row = $(this).closest('.faq-row');
            $row.next('.faq-row').after($row);
        });
});
