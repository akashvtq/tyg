import { readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { beforeAll, describe, expect, it } from 'vitest';

// Vite rewrites new URL('...', import.meta.url) into an http:// asset URL, so resolve paths with node:path.
const fromRoot = (path) => resolve(dirname(fileURLToPath(import.meta.url)), '../..', path);

beforeAll(() => {
    // Run the plain browser script the same way a <script> tag would.
    const code = readFileSync(fromRoot('assets/backend/js/seo-page-docx-clean.js'), 'utf8');
    (0, eval)(code);
});

const clean = (html) => window.cleanDocxHtml(html);
const parse = (html) => new DOMParser().parseFromString(`<div id="r">${html}</div>`, 'text/html').getElementById('r');

describe('cleanDocxHtml on the client sample (docs/samples/out.html)', () => {
    const fixture = readFileSync(fromRoot('docs/samples/out.html'), 'utf8');
    let result;
    let root;

    beforeAll(() => {
        result = clean(fixture);
        root = parse(result.html);
    });

    it('takes the H1 as the title and removes it from the body', () => {
        expect(result.title).toBe('27 Link Building Tools for Research, Outreach, Monitoring & Automation');
        expect(root.querySelectorAll('h1')).toHaveLength(0);
    });

    it('removes bookmark anchors', () => {
        expect(root.querySelectorAll('a:not([href])')).toHaveLength(0);
    });

    it('unwraps a lone <strong> inside headings', () => {
        const wrapped = [...root.querySelectorAll('h2, h3')].filter((h) =>
            h.children.length === 1 && h.firstElementChild.tagName === 'STRONG'
            && h.textContent.trim() === h.firstElementChild.textContent.trim());
        expect(wrapped).toHaveLength(0);
    });

    it('fixes the 5 tables: one header row, body cells as td, no lone <p> in cells, responsive wrapper', () => {
        const tables = root.querySelectorAll('table');
        expect(tables).toHaveLength(5);
        tables.forEach((table) => {
            expect(table.parentElement.classList.contains('table-responsive')).toBe(true);
            expect(table.querySelectorAll('thead tr')).toHaveLength(1);
            expect(table.querySelectorAll('tbody th')).toHaveLength(0);
            expect(table.querySelectorAll('tbody tr').length).toBeGreaterThan(0);
        });
        expect(root.querySelectorAll('td > p:only-child, th > p:only-child')).toHaveLength(0);
    });

    it('reports the document statistics', () => {
        expect(result.stats).toEqual({ h2: 36, h3: 71, tables: 5, listItems: 277 });
    });

    it('raises no H1 warnings for a document with one H1', () => {
        expect(result.warnings.filter((w) => w.includes('Heading 1'))).toEqual([]);
    });
});

describe('cleanDocxHtml rules', () => {
    it('demotes extra H1s to H2 with a warning', () => {
        const result = clean('<h1>Title</h1><p>a</p><h1>Second</h1>');
        expect(result.title).toBe('Title');
        expect(result.html).toBe('<p>a</p><h2>Second</h2>');
        expect(result.warnings.some((w) => w.includes('Heading 1'))).toBe(true);
    });

    it('removes images, including base64 ones, with a warning', () => {
        const result = clean('<p><img src="data:image/png;base64,AAAA"></p><p>Text</p>');
        expect(result.html).toBe('<p>Text</p>');
        expect(result.warnings.some((w) => w.includes('1 image'))).toBe(true);
        expect(result.warnings.some((w) => w.includes('picture button') && w.includes('alt text'))).toBe(true);
    });

    it('warns about skipped heading levels', () => {
        const result = clean('<h2>A</h2><h4>B</h4>');
        expect(result.warnings.some((w) => w.includes('H2 → H4'))).toBe(true);
    });

    it('drops unsafe hrefs but keeps the link text, and keeps safe links', () => {
        const result = clean('<p><a href="javascript:alert(1)">bad</a> <a href="https://example.com" id="x">good</a> <a href="/pricing">rel</a></p>');
        expect(result.html).toBe('<p>bad <a href="https://example.com">good</a> <a href="/pricing">rel</a></p>');
    });

    it.each([
        ['a tab', 'java\tscript:alert(1)'],
        ['a newline', 'java\nscript:alert(1)'],
        ['a leading control character', '\x01javascript:alert(1)'],
    ])('drops a javascript: href hidden with %s but keeps the link text', (_label, href) => {
        const doc = new DOMParser().parseFromString('<p><a>bad</a></p>', 'text/html');
        doc.querySelector('a').setAttribute('href', href);
        const result = clean(doc.body.innerHTML);
        expect(result.html).toBe('<p>bad</p>');
    });

    it('unwraps links to places inside the document with one warning', () => {
        const result = clean('<p><a href="#_heading=h.1">see</a> and <a href="#x">x</a></p>');
        expect(result.html).toBe('<p>see and x</p>');
        const warnings = result.warnings.filter((w) => w.includes('inside the document'));
        expect(warnings).toHaveLength(1);
        expect(warnings[0]).toContain('2 links');
    });

    it('removes scripts and disallowed attributes', () => {
        expect(clean('<p onclick="x()" style="color:red">t</p><script>alert(1)</script>').html).toBe('<p>t</p>');
        expect(clean('<style>p{}</style><iframe src="x"></iframe><p>t</p><object></object><embed><link rel="x"><meta name="x">').html)
            .toBe('<p>t</p>');
        expect(clean('<div class="evil"><p id="a" class="b">t</p></div>').html).toBe('<p>t</p>');
    });

    it('removes a template element with its content', () => {
        const result = clean('<p>t</p><template><img src=x onerror=alert(1)></template>');
        expect(result.html).toBe('<p>t</p>');
    });

    it('removes a noscript payload that would turn into markup when re-parsed', () => {
        const result = clean('<p>t</p><noscript><a href="https://x.com" title="</noscript><img src=x onerror=alert(1)>">l</a></noscript>');
        const reparsed = new DOMParser().parseFromString(result.html, 'text/html');
        expect(reparsed.querySelectorAll('img, noscript')).toHaveLength(0);
        const withHandlers = [...reparsed.querySelectorAll('*')].filter((el) =>
            [...el.attributes].some((attr) => attr.name.toLowerCase().startsWith('on')));
        expect(withHandlers).toHaveLength(0);
    });

    it('unwraps tags that are not allowed but keeps their text', () => {
        expect(clean('<p><span>text</span> <font>x</font></p>').html).toBe('<p>text x</p>');
    });

    it('counts an image inside a dropped empty H1', () => {
        const result = clean('<h1><img src="x"></h1><h1>Title</h1><p>a</p>');
        expect(result.title).toBe('Title');
        expect(result.html).toBe('<p>a</p>');
        expect(result.warnings.some((w) => w.includes('Removed 1 image'))).toBe(true);
    });

    it('keeps colspan and rowspan on cells and nothing else', () => {
        const result = clean('<table><tr><th colspan="2" style="x">H</th></tr><tr><td rowspan="1" class="c">a</td><td>b</td></tr></table>');
        expect(result.html).toBe('<div class="table-responsive"><table><thead><tr><th colspan="2">H</th></tr></thead>'
            + '<tbody><tr><td rowspan="1">a</td><td>b</td></tr></tbody></table></div>');
    });

    it('skips an empty first H1 and uses the next real one as the title', () => {
        const result = clean('<h1> </h1><h1>Real</h1><p>a</p>');
        expect(result.title).toBe('Real');
        expect(result.html).toBe('<p>a</p>');
    });

    it('does not use an H1 inside a table as the title', () => {
        const result = clean('<table><tr><td><h1>Cell</h1></td></tr></table><h1>Title</h1><p>a</p>');
        expect(result.title).toBe('Title');
        const root = parse(result.html);
        expect(root.querySelectorAll('h1')).toHaveLength(0);
        expect(root.querySelector('table h2').textContent).toBe('Cell');
    });

    it('removes empty paragraphs', () => {
        expect(clean('<p> </p><p>Text</p><p></p>').html).toBe('<p>Text</p>');
    });

    it('joins several paragraphs in one cell with <br>', () => {
        const result = clean('<table><tr><th><p>H</p></th></tr><tr><td><p>One</p><p>Two</p></td></tr></table>');
        expect(result.html).toBe('<div class="table-responsive"><table><thead><tr><th>H</th></tr></thead><tbody><tr><td>One<br>Two</td></tr></tbody></table></div>');
    });
});
