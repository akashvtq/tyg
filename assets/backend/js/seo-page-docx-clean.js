/*
 * Cleans mammoth.js output from Google Docs / Word exports before it goes into the editor.
 * Rules: docs/superpowers/specs/2026-09-24-seo-pages-design.md §4.4.
 * Plain browser script: exposes window.cleanDocxHtml(html) -> { html, title, stats, warnings }.
 */
(function (root) {
    'use strict';

    function unwrap(el) {
        var parent = el.parentNode;
        while (el.firstChild) {
            parent.insertBefore(el.firstChild, el);
        }
        parent.removeChild(el);
    }

    function rename(el, tagName) {
        var replacement = el.ownerDocument.createElement(tagName);
        Array.prototype.forEach.call(el.attributes, function (attr) {
            replacement.setAttribute(attr.name, attr.value);
        });
        while (el.firstChild) {
            replacement.appendChild(el.firstChild);
        }
        el.parentNode.replaceChild(replacement, el);
        return replacement;
    }

    // Tags kept; any other element is unwrapped (children kept). div only survives the allowlist pass because
    // the cleaner creates the table-responsive wrappers after it; divs from the input are unwrapped.
    var ALLOWED_TAGS = ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'strong', 'b', 'em', 'i', 'u', 's', 'sub', 'sup',
        'a', 'ul', 'ol', 'li', 'table', 'thead', 'tbody', 'tr', 'th', 'td', 'br', 'img', 'blockquote'];

    // Elements removed together with their content.
    var REMOVED_ELEMENTS = 'script, style, template, noscript, noembed, noframes, xmp, textarea, title, iframe, object, '
        + 'embed, svg, math, form, base, link, meta, select, option, button, input';

    // Attributes kept per tag; every other attribute (on* handlers, style, id, class...) is dropped.
    var ALLOWED_ATTRIBUTES = {
        A: ['href', 'title'],
        TH: ['colspan', 'rowspan'],
        TD: ['colspan', 'rowspan']
    };

    function isSafeHref(href) {
        // Browsers ignore tabs, newlines and leading control characters in a URL, so "java\tscript:" still runs.
        var normalised = href.replace(/[\u0000- \u007F]+/g, '');
        var scheme = /^([a-z][a-z0-9+.-]*):/i.exec(normalised);
        return !scheme || /^(https?|mailto)$/i.test(scheme[1]);
    }

    function all(rootEl, selector) {
        return Array.prototype.slice.call(rootEl.querySelectorAll(selector));
    }

    function cleanDocxHtml(html) {
        var doc = new DOMParser().parseFromString('<div id="docx-root">' + html + '</div>', 'text/html');
        var body = doc.getElementById('docx-root');
        var warnings = [];
        var title = null;

        // Defence in depth (the server purifies too): drop active elements with their content, unwrap every
        // other tag not allowlisted, then drop every attribute not allowlisted.
        all(body, REMOVED_ELEMENTS).forEach(function (el) {
            if (el.parentNode) {
                el.parentNode.removeChild(el);
            }
        });
        all(body, '*').forEach(function (el) {
            if (ALLOWED_TAGS.indexOf(el.localName) === -1) {
                unwrap(el);
            }
        });
        all(body, '*').forEach(function (el) {
            var allowed = ALLOWED_ATTRIBUTES[el.tagName] || [];
            Array.prototype.slice.call(el.attributes).forEach(function (attr) {
                if (allowed.indexOf(attr.name) === -1) {
                    el.removeAttribute(attr.name);
                }
            });
        });

        // Rule 8: images are added by hand (uploaded from the editor with alt text), never imported. Counted before the H1 step,
        // which can drop an empty H1 together with an image inside it.
        var images = all(body, 'img');
        images.forEach(function (img) {
            img.parentNode.removeChild(img);
        });
        if (images.length) {
            warnings.push('Removed ' + images.length + ' image' + (images.length === 1 ? '' : 's')
                + ' from the document. Add them again with the editor\'s picture button, which uploads each image and asks for its alt text.');
        }

        // Rules 1 and 10: bookmark anchors and links to them go; unsafe links lose their href but keep their text.
        var internalLinks = 0;
        all(body, 'a').forEach(function (a) {
            var href = a.getAttribute('href');
            if (!href) {
                unwrap(a);
                return;
            }
            if (href.trim().charAt(0) === '#') {
                internalLinks++;
                unwrap(a);
                return;
            }
            if (!isSafeHref(href)) {
                warnings.push('Removed an unsafe link: ' + href.slice(0, 40));
                unwrap(a);
            }
        });
        if (internalLinks) {
            warnings.push('Removed ' + internalLinks + ' link' + (internalLinks === 1 ? '' : 's')
                + ' to places inside the document (they can\'t work on the web page).');
        }

        // Rule 2: headings wrapped in a lone <strong>/<b>.
        all(body, 'h1, h2, h3, h4, h5, h6').forEach(function (heading) {
            var only = heading.children.length === 1 ? heading.firstElementChild : null;
            if (only && /^(STRONG|B)$/.test(only.tagName) && heading.textContent.trim() === only.textContent.trim()) {
                unwrap(only);
            }
        });

        // Rule 6: the first non-empty H1 outside a table is the title; empty H1s go; any other H1 becomes an H2.
        var extraH1s = 0;
        all(body, 'h1').forEach(function (h1) {
            var text = h1.textContent.trim();
            if (text === '') {
                h1.parentNode.removeChild(h1);
            } else if (title === null && !h1.closest('table')) {
                title = text;
                h1.parentNode.removeChild(h1);
            } else {
                rename(h1, 'h2');
                extraH1s++;
            }
        });
        if (extraH1s) {
            warnings.push(extraH1s + ' extra Heading 1 converted to Heading 2 (a page has one H1: its title).');
        }

        // Rules 3, 4 and 5: tables.
        all(body, 'table').forEach(function (table) {
            var rows = all(table, ':scope > thead > tr, :scope > tbody > tr, :scope > tr');
            if (!rows.length) {
                return;
            }
            var thead = doc.createElement('thead');
            var tbody = doc.createElement('tbody');

            rows.forEach(function (tr, rowIndex) {
                var cellTag = rowIndex === 0 ? 'th' : 'td';
                Array.prototype.slice.call(tr.children).forEach(function (cell) {
                    if (cell.tagName.toLowerCase() !== cellTag) {
                        cell = rename(cell, cellTag);
                    }
                    var paragraphs = Array.prototype.slice.call(cell.children).filter(function (child) {
                        return child.tagName === 'P';
                    });
                    if (paragraphs.length === 1 && cell.children.length === 1) {
                        unwrap(paragraphs[0]);
                    } else if (paragraphs.length > 1) {
                        paragraphs.forEach(function (p, index) {
                            if (index > 0) {
                                cell.insertBefore(doc.createElement('br'), p);
                            }
                            unwrap(p);
                        });
                    }
                });
                (rowIndex === 0 ? thead : tbody).appendChild(tr);
            });

            while (table.firstChild) {
                table.removeChild(table.firstChild);
            }
            table.appendChild(thead);
            if (tbody.children.length) {
                table.appendChild(tbody);
            }

            var wrapper = doc.createElement('div');
            wrapper.className = 'table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        });

        // Rule 7: empty paragraphs.
        all(body, 'p').forEach(function (p) {
            if (p.textContent.trim() === '' && !p.querySelector('img, br')) {
                p.parentNode.removeChild(p);
            }
        });

        // Rule 9: skipped heading levels (warning only). The title counts as level 1.
        var previousLevel = 1;
        all(body, 'h2, h3, h4, h5, h6').forEach(function (heading) {
            var level = parseInt(heading.tagName.charAt(1), 10);
            if (level > previousLevel + 1) {
                warnings.push('Heading level skipped: H' + previousLevel + ' → H' + level
                    + ' ("' + heading.textContent.trim().slice(0, 50) + '").');
            }
            previousLevel = level;
        });

        return {
            html: body.innerHTML,
            title: title,
            stats: {
                h2: body.querySelectorAll('h2').length,
                h3: body.querySelectorAll('h3').length,
                tables: body.querySelectorAll('table').length,
                listItems: body.querySelectorAll('li').length
            },
            warnings: warnings
        };
    }

    root.cleanDocxHtml = cleanDocxHtml;
})(typeof window !== 'undefined' ? window : globalThis);
