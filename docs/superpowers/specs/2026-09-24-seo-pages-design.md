# SEO Pages (Category / Subcategory / Topic) — Design

- **Date:** 2026-09-24
- **Status:** Draft, awaiting review
- **Reference:** https://vizem.net/almanya/vize/ (3-level content hub with breadcrumb, sibling/child navigation, Article + BreadcrumbList schema)

## 1. Goal

Add a hierarchical, admin-authored content section to improve organic search visibility. Each level is a full content page:

| Level | Example URL | Role |
|---|---|---|
| 1. Category | `/seo` | Hub page for the whole topic; lists its subcategories |
| 2. Subcategory | `/seo/link-building` | Section page; lists its topics |
| 3. Topic | `/seo/link-building/tools` | Detailed article |

The SEO value comes from long-form content, a strict URL hierarchy, and dense internal linking (parent ↔ child ↔ sibling), plus structured data.

### Source data

The client's topical map (`link building __ Topical Map - Topical Map.csv`) defines the pages. Its columns map to this design as follows:

| Sheet column | Field |
|---|---|
| URL | `path` (the host and trailing slash are dropped, e.g. `https://buildsaaslinks.com/seo/link-building/` → `seo/link-building`) |
| H1 | `title` |
| Title Tag | `meta_title` |
| Description | `meta_description` |
| Topical Map | `nav_label` |
| Image URL / Image Alt Tag | `featured_image` / `featured_image_alt` |
| Primary keyword from sheet | `primary_keyword` |
| Order (Core / Inner / Outer) | `cluster` |
| Rows with a heading but no URL (e.g. "Link Acquisition Methods") | `nav_group` on the pages listed under that heading |
| Publication Document | The Word file the content comes from |
| Publication Date / Published | `published_at` / `is_active` |
| Attribute family, Keyword SV, Family SV | Not stored; planning data that stays in the sheet |

In the current sheet that gives 1 category, 8 subcategories and 46 topics (55 pages). The sheet also lists 12 fourth-level URLs, such as `/seo/link-building/backlinks/quality`. **The client has confirmed only 3 levels are used**, so those rows are out of scope. All 12 carry notes like "Not needed for now" or "For later stages".

### Decisions already made

- Content is **written by hand in the admin panel** (no bulk import in this phase). The client supplies each page as a Word document, which the admin **imports into the editor** one page at a time (§4.4).
- Category slugs are **hardcoded in the route**. The first (and initially only) category has slug `seo`.
- **Exactly 3 levels, confirmed by the client.** No fourth level.
- **No trailing slash** in URLs, following the site's existing routing, even though the client's sheet writes URLs with one (§2.1).
- **One self-referencing table** for all three levels (approach A).
- Page extras: **FAQ, table of contents, featured image, author + last-updated date, call to action**.
- **Every page has a featured image with alt text.** Both are required.
- **Images inside the body** are uploaded from the editor, and every one needs alt text (§4.1, §4.3).
- Author comes from the existing **`admins`** table.
- **English only**, stored as plain columns (not the translation-JSON pattern used by `blogs`). Only `en` is active in `languages`.

### Out of scope

- Bulk import / programmatic generation (the table design must not prevent it later).
- Multi-language content.
- Public author profile pages.
- Draft preview for admins.
- Moving a page to a different level (depth is fixed once created; see §3.3).

## 2. URLs and routing

### 2.1 Route

Add to `routes/web.php`, in the frontend section:

```php
Route::get('/{category}/{subcategory?}/{topic?}', [SeoPageController::class, 'show'])
    ->where('category', 'seo')
    ->name('seo-page.show');
```

- The `category` pattern is built from `SeoPage::ROUTED_CATEGORIES` (currently `['seo']`), the hardcoded category list. Adding a new category later means creating it in the admin **and** adding its slug to that constant. The sitemap only lists pages under routed categories. The admin create form shows a notice saying so when a level-1 page is created.
- Because the first segment only matches `seo`, the route cannot collide with `/service`, `/blog`, `/software`, `/login` or the `pages` slugs (`/{slug}` is already limited to known page slugs by `Page::getValidSlugs()`).
- URLs have no trailing slash. The root `.htaccess` already 301-redirects trailing slashes, so `/seo/link-building/` (as written in the client's sheet) permanently redirects to `/seo/link-building`. Canonical tags, the sitemap and all internal links use the form without the slash.
- `subcategory` and `topic` use Laravel's default segment pattern (no `/`), so deeper URLs return 404.

### 2.2 URL generation

`SeoPage::url()` returns `url($this->path)`. All internal links (breadcrumb, sidebar, child cards, sitemap, canonical) use it. Nothing builds URLs by concatenating segments in views.

## 3. Data model

### 3.1 Table `seo_pages`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `parent_id` | bigint, nullable, FK → `seo_pages.id` | `NULL` for categories. `restrictOnDelete` |
| `depth` | tinyint unsigned | 1 = category, 2 = subcategory, 3 = topic. Set on create, never changed |
| `title` | varchar(255) | Rendered as the H1 |
| `nav_label` | varchar(60) | Required. Short name used in the breadcrumb, the sidebar and the child-card headings, e.g. "Link Building Tools" (the H1 is "27 Link Building Tools for Research, Outreach, Monitoring & Automation"). Sheet column "Topical Map" |
| `nav_group` | varchar(60), nullable | Heading this page is listed under in its parent's sidebar and child cards, e.g. "Link Acquisition Methods". Pages without one are listed first, ungrouped |
| `slug` | varchar(60) | Lowercase, `Str::slug()`. Unique among siblings. Max 60, so the full 3-level `path` always fits in 191 |
| `path` | varchar(191), **unique** | Full path without leading slash, e.g. `seo/link-building/tools`. Used for the lookup |
| `excerpt` | text, nullable | Summary on the parent page's child cards; fallback meta description |
| `content` | longText | Rich HTML from Summernote (typed or imported from Word, §4.4), cleaned with `Purifier::clean($html, 'seo_content')`. That's a new profile that also allows tables |
| `faq` | json, nullable | `[{"question": "...", "answer": "..."}]`. Answers are plain text or simple HTML, purified |
| `featured_image` | varchar(512) | Required. Stored under `assets/general/images/seo-pages/{page-path}/` with a **descriptive file name built from the slug** (e.g. `seo/link-building/tools/tools.webp`), not the random name `FileManageTrait::uploadImage()` generates; `-2`, `-3` are appended on a clash. The extension is taken from the upload's **detected** content type (`UploadedFile::guessExtension()`), not the client-supplied file name, and is limited to `jpg`, `png` or `webp` (`jpeg` is normalised to `jpg`); anything else is rejected. The file is not renamed if the slug changes later. Also used as the OG image and the Article `image`. Body images uploaded from the editor share the page directory as `{slug}-body[-n].{ext}` and are referenced only from `content` (§4.1) |
| `featured_image_alt` | varchar(255) | Required. Alt text for the featured image; also output as `og:image:alt` |
| `author_id` | bigint, nullable, FK → `admins.id` | `nullOnDelete` |
| `meta_title` | varchar(255), nullable | Falls back to `title`. The client's title tags run up to 92 characters, so there is no hard limit below 255 |
| `meta_description` | varchar(500), nullable | Falls back to `excerpt`, then the first 160 characters of the stripped `content` |
| `canonical_url` | varchar(255), nullable | Falls back to the page's own URL |
| `show_toc` | boolean, default true | |
| `show_cta` | boolean, default true | |
| `primary_keyword` | varchar(255), nullable | Admin-only reference: the page's target keyword. Not displayed on the site |
| `cluster` | varchar(20), nullable | Admin-only: `core`, `inner` or `outer`, from the sheet's "Order" column. Used as an index filter; not displayed on the site |
| `sort_order` | int, default 0 | Order among siblings (sidebar, child cards) |
| `is_active` | boolean, default false | New pages start as drafts |
| `published_at` | timestamp, nullable | Set the first time `is_active` becomes true. Used as `datePublished` |
| `created_at`, `updated_at` | timestamps | `updated_at` is shown as "Last updated" and used as `dateModified` |

Indexes: unique `path`, unique `(parent_id, slug)`, index `(parent_id, is_active, sort_order)`.

MySQL does not treat `NULL`s as equal in a unique index, so uniqueness of **category** slugs (`parent_id = NULL`) is enforced in validation. The unique `path` index backs this up at the database level.

### 3.2 Model `App\Models\SeoPage`

- Casts: `faq` → array, `is_active`, `show_toc`, `show_cta` → boolean, `published_at` → datetime.
- Relations: `parent()`, `children()` (ordered by `sort_order`, then `nav_label`), `author()` → `Admin`.
- Scope: `active()`.
- Helpers:
  - `url(): string`
  - `ancestors(): Collection` (root first; at most 2 queries, or read from the cached tree)
  - `resolvedMetaTitle()`, `resolvedMetaDescription()`, `resolvedCanonical()`
  - `resolvedMetaDescription()` falls back to the excerpt, then to the stripped content. Both fallbacks go through the same helper: HTML entities are decoded, a space is inserted before block-level tags (so stripping them doesn't glue adjacent words together; inline tags like `a`/`strong` are left alone so punctuation stays attached), and the result is limited to 160 characters, cut back to the last word boundary (no ellipsis).
  - `hasFeaturedImageFile()`, `featuredImageSize()`: used by the frontend to render the image block, and the child/sidebar cards, only when the file actually exists on disk.
- `booted()` events:
  - `saving`: slugify `slug`; compute `depth` and `path` from the parent; set `published_at` on first activation. As a safety net behind the form request's own validation, it throws a `LogicException` if the computed depth would exceed `MAX_DEPTH` (3), or if an existing page's depth would change (a page may only move to a parent at the same level as its current parent).
  - `saved`: if `path` changed, recompute `path` for all descendants (done in PHP by re-saving each child, which cascades to grandchildren through the same hook); clear the tree cache. The cache clear runs via `DB::afterCommit()`, so a rolled-back save never clears a cache that still matches the database.
  - `deleted`: clear the tree cache, also via `DB::afterCommit()`.

### 3.3 Hierarchy rules

- A category has no parent. A subcategory's parent must be a category. A topic's parent must be a subcategory.
- Maximum depth is 3.
- Depth is fixed after creation. On edit, the parent can only be changed to another page at the **same depth as the current parent**, so descendants never exceed depth 3. (Example: a topic can move to a different subcategory. A subcategory can move to a different category.)
- A page is publicly visible only if it **and all of its ancestors** are active.
- A page with children cannot be deleted. The admin must delete or move the children first.

## 4. Admin panel

Follows the existing Blog module's patterns (`app/Http/Controllers/Backend/BlogController.php`, `resources/views/backend/blog/*`).

### 4.1 Routes and controller

- In `routes/admin.php`: `Route::resource('seo-pages', SeoPageController::class)->except('show');` Route names are `admin.seo-pages.*`. These are deliberately distinct from the existing `admin.seo` "SEO Manage" screen, which handles site-wide meta and is unrelated.
- Also `Route::post('seo-pages/images', [SeoPageController::class, 'storeImage'])->name('seo-pages.images.store')`, registered **before** the resource so it isn't read as a `{seo_page}` URL. It is the content editor's image upload (see *Body images* below).
- New file `app/Http/Controllers/Backend/SeoPageController.php`. Permissions go through `$this->permissionAuthorization()` like `BlogController`:
  - `index` → `seo-page-list`
  - everything else (including `storeImage`) → `seo-page-manage`
- Validation in a Form Request `app/Http/Requests/SeoPageRequest.php`. Rules:
  - `title` required, max 255.
  - `nav_label` required, max 60. `nav_group` nullable, max 60.
  - `primary_keyword` nullable, max 255. `cluster` nullable, one of `core`, `inner` or `outer`.
  - `slug` required, max 60, `alpha_dash` after slugify, unique among siblings.
  - `parent_id` nullable; must exist; must have depth ≤ 2; on edit, must obey §3.3.
  - Level-1 slug must not equal any first segment a category slug can't use. That list is built at validation time, not hand-kept: the first static segment of every registered route, the admin prefix (`setting('admin_prefix')`), the small hand-kept `SeoPage::RESERVED_CATEGORY_SLUGS` floor (`admin`, `api`, `blog`, `service`, `software`, `login`, `register`, `user`, `details`, `locale-set`, `contact`, `programmatic-seo-services`, `assets`, `build`), and every existing `pages.slug` — minus the categories `SeoPage::ROUTED_CATEGORIES` already serves.
  - A routed category (a level-1 page whose slug is in `SeoPage::ROUTED_CATEGORIES`) that has children can't be renamed: the error says to add the new slug to `ROUTED_CATEGORIES` first, since otherwise every page under it would go offline. In the other direction, the CMS Page admin (`Backend\PageController` store/update) rejects a page slug that slugifies to a routed category, so a CMS page can't shadow the SEO section.
  - `content` required, and not empty after purification with the `seo_content` profile: markup with no text, image or table (e.g. `<p><br></p>`) is rejected with "The content is empty."
  - Every `<img>` left in the purified `content` needs alt text: an alt that is missing, blank, a bare file name (`^[\w.-]+\.(jpe?g|png|webp|gif|svg)$`, e.g. `IMG_0042.JPG`) or equal to the file name of the image's `src` is rejected with "Every image in the content needs alt text (missing for: a-body.png, b.png)." naming each offending image's file name (reduced to URL-safe characters). HTML Purifier fills a missing `alt` with the `src` file name, hence the last check. A sentence that mentions a file name ("Growth shown in chart.png") is accepted. The editor applies the same rule. The editor already asks for alt text; this catches images added in code view or pasted as HTML.
  - `faq.*.question` / `faq.*.answer` required together.
  - `sort_order`: validated as an integer (`min:0`) before it is cast to `int` for saving.
  - `featured_image`: required on create; optional on edit, where it replaces the current image and the old file is removed. Types jpeg, png, jpg or webp, max 2 MB (the same size limit as the blog `cover`).
  - `featured_image_alt`: required, max 125 characters (around the point where screen readers cut off). It must not be just the file name. Reject values ending in an image extension such as `.jpg`, `.png` or `.webp`.
  - `author_id` exists in `admins`.
  - `meta_title` max 255; `meta_description` max 170; `canonical_url` nullable url. Recommended lengths (60 for the title, 160 for the description) are **warnings in the form, not validation errors**, because the client's approved title tags go up to 92 characters.
- Errors use the existing `notifyEvs('error', …)` flash pattern for the first error, and the form itself lists every validation error (`$errors->all()`) above the fields; toggles and FAQ rows are refilled from `old()` after a failed submission, not from the page's saved values.
- `store`/`update` run inside a `DB::transaction()` (an update's path cascade to descendants must commit as a whole); if the transaction throws, a newly-stored image is deleted so no orphaned file is left behind.
- **Body images** (`storeImage`): validates `image` (required, `image`, jpeg/jpg/png/webp, max 2 MB), an optional `slug` (the form's current slug, slugified again on the server; `image` if empty), and the selected `parent_id`. The file is stored through `SeoPageImageService` under `assets/general/images/seo-pages/{page-path}/` as `{slug}-body.{ext}`, then `{slug}-body-2.{ext}`, and so on; for example, a topic at `seo/analysis/analytics` stores in `assets/general/images/seo-pages/seo/analysis/analytics/`. The `-body` part tells them apart from featured images. Returns a **root-relative** URL, `{"url": parse_url(asset(path), PHP_URL_PATH)}` (e.g. `/build-saas-links/assets/general/images/seo-pages/seo/analysis/analytics/analytics-body.png` locally, `/assets/...` in production), so no host or scheme is saved in the content (the `seo_content` purifier keeps relative image sources). Because a local or staging subfolder prefix can still end up in the content, the public page doesn't trust the saved form: see *Body image URLs* in §5.2. The editor sends the request with `Accept: application/json`, so validation errors come back as JSON 422 and a missing permission as 403. The file isn't linked to a page row: an image uploaded and then removed from the editor, or never saved, stays on disk as an orphan (as do the body images of a deleted page). Cleaning these up is a follow-up, not part of this phase.
- `destroy` deletes the row first, then the image file; if the delete query fails (e.g. a child page was added after the "has children" check, and the foreign key restricts it), it reports the exception and returns a JSON error without touching the image.
- The admin index's search filter escapes SQL `LIKE` wildcards (`%`, `_`, and the escape character `\` itself) in the search term before building the query, so a search for a literal `%` or `_` doesn't act as a wildcard.

### 4.2 Permissions and menu

- Add `seo-page-list` and `seo-page-manage` to `database/seeders/PermissionTableSeeder.php` (category `site-essentials`, next to the `blog-*` entries).
- The live database is already seeded, so also ship a migration that inserts both permissions (guard `admin`) and grants them to the Super Admin role.
- `config/admin_menus.php`: add an "SEO Pages" single menu item after the Blog group, route `admin.seo-pages.index`, permission `seo-page-list`.

### 4.3 Screens

**Index:** a table in tree order (category → its subcategories → their topics), indented by depth, with these columns:
- Nav label (with the full title underneath in small text)
- Level badge
- Group (`nav_group`)
- Cluster
- Path (links to the public page)
- Status
- Last updated
- Actions: edit, delete, and "Add child" (prefills `parent_id`)

Filters: status, cluster, and search by title, nav label or primary keyword.

**Create / Edit form**, in this order:
1. Parent: a select grouped by depth, showing only valid parents; "None (category)" for level 1.
2. Title (H1), then nav label, then slug (auto-filled from the nav label, editable).
3. Group: a text input with suggestions (`datalist`) listing the groups already used by the selected parent's children, so the same heading is reused rather than retyped with variations.
4. Excerpt.
5. Content: Summernote, set up by `seo-page-form.js` rather than the global `.summernote` init in `custom.js` (the textarea has the class `seo-page-editor` instead; its id stays `content` for the Word import). The toolbar only offers what the `seo_content` profile keeps: style (paragraph, H2, H3, H4, blockquote), bold, italic, underline, clear formatting, bulleted and numbered lists, link, picture, code view and help. There is no H1 (the title is the H1) and no colour (inline styles are stripped).
   **Images:** the picture dialog's file picker, drag and drop and paste all upload the file to `admin.seo-pages.images.store` (JPG, PNG or WebP up to 2 MB, checked in the browser first) instead of embedding it as a base64 `data:` URI, which the purifier would silently strip. Before each upload a small Bootstrap modal asks for the alt text, with a preview; it can't be empty, a bare file name or the image's own file name (the §4.1 rule), and cancelling skips the image. All prompts go through one queue, so a paste or drop during another prompt or upload waits its turn instead of replacing the open prompt; a file's preview blob URL is created when its prompt opens and revoked when it closes. On success `<img src="{url}" alt="{alt}">` is inserted at the cursor; on failure the error is shown with `notifyEvs` (escaped). While an upload runs the picture button is disabled and "Uploading image…" shows under the editor, and the form won't submit until it finishes. The dialog's image URL field accepts only `http(s)` URLs and asks for alt text the same way. Clicking an image in the editor shows a popover with **Alt text** (edit the alt) and remove; Summernote's resize and float buttons are left out because the purifier strips the inline styles they set. If code view is open when the form is submitted, it is closed first so its edits are saved.
6. FAQ repeater: add, remove and reorder question/answer rows.
7. Featured image upload with preview, and an **alt text** field directly below it (character counter; hint: "Describe what the image shows, e.g. 'Dashboard showing referring domains in Ahrefs'").
8. Author: a select of admins, defaulting to the logged-in admin.
9. SEO box: meta title (character counter, warns above 60), meta description (counter, warns above 160), canonical URL, primary keyword, cluster.
10. Toggles: show TOC, show CTA, active.
11. Sort order.

When editing a page whose slug has changed and that has children, show a warning that the child URLs will change too. There is no automatic redirect; see §8.

### 4.4 Import from Word (.docx)

The client delivers each page's content as a Word document (the sheet's "Publication Document" column), exported from Google Docs. The admin imports it into the form instead of copying and pasting.

**Flow**
1. The create/edit form has an **Import .docx** button above the content editor. The admin picks the file (`.docx` only, max 10 MB).
2. The file is converted **in the admin's browser**. It is never uploaded; the server only receives the resulting HTML when the form is saved, like any other edit.
3. If the editor already has content, ask for confirmation before replacing it.
4. The first H1 goes into **Title** (only if Title is empty; otherwise it is left alone) and is removed from the body. The cleaned body goes into the Summernote editor.
5. A short report appears under the button. Example: "Imported: 36 H2, 71 H3, 5 tables, 277 list items. Title set from the document's H1." Any warnings are listed below it (see *Cleanup rules*).
6. The admin reviews and edits the content, then saves. The server purifies it as usual (see *Server-side purification*).

**Converter:** [mammoth.js](https://github.com/mwilliamson/mammoth.js) (browser build). It maps Word's built-in styles (Heading 1–6, lists, tables, bold, italic, links) to semantic HTML with no inline styles.
- The minified file `mammoth.browser.min.js` is copied from the npm package's `mammoth/mammoth.browser.min.js` into `assets/backend/js/`, pinned to a 1.x version. It is loaded only on the SEO page form, the same way `summernote-lite.min.js` is loaded. No CDN, and nothing new on the server.
- Tested on the client's sample *27 Link Building Tools for Research, Outreach, Monitoring & Automation.docx*: 1 H1, 36 H2, 71 H3, 277 list items and 5 tables were converted with 0 converter warnings. The raw output is in `docs/samples/out.html`.

**Cleanup rules:** applied in the browser to mammoth's output, before it goes into the editor. They live in their own file, `assets/backend/js/seo-page-docx-clean.js`, as a single function `cleanDocxHtml(html) → { html, title, stats, warnings }`, so the rules can be tested separately from the form. The file is a plain browser script, like the other admin JS: the site has a Vite setup, but no view loads a Vite bundle, and using one would add a build step to deployment. The function uses only standard DOM APIs (`DOMParser`) and is exposed as `window.cleanDocxHtml`. The button wiring (file input → mammoth → `cleanDocxHtml` → Summernote and Title) goes in a separate `seo-page-docx-import.js`.

| # | Problem in Google Docs exports | Rule |
|---|---|---|
| 1 | Bookmark anchors on every heading (`<a id="_o3llzo200q35"></a>`, 108 in the sample) | Remove every `<a>` that has no `href`. Links that point at one of those bookmarks (`href="#_o3llzo200q35"`) are also unwrapped (kept as text), with a single counted warning ("Removed N links to places inside the document") rather than one per link |
| 2 | Headings wrapped in `<strong>` | Unwrap `<strong>`/`<b>` when it is the only child of a heading |
| 3 | Every table row output as a header row (`<th>` inside `<thead>`) | Keep the first row as the header (`<thead>` with `<th>`). Move the other rows into `<tbody>` and turn their cells into `<td>` |
| 4 | Each table cell wrapped in `<p>` | Unwrap a single `<p>` inside a cell. Keep multiple paragraphs, separated by `<br>` |
| 5 | Wide tables | Wrap each table in `<div class="table-responsive">` (Bootstrap class, already on the site) |
| 6 | Document title is an H1 | Take the text of the **first non-empty H1 that isn't inside a table** as `title` and remove it (an empty H1 is dropped outright). **Any further H1s become H2**, with a warning, so the page keeps one H1 |
| 7 | Empty paragraphs used as spacing | Remove `<p>` elements with no text and no image |
| 8 | Images in the document | mammoth is called with `convertImage` set so it never embeds them as base64 `data:` URIs in the first place — cleanup then also removes any `<img>` that slips through, with a warning naming how many and telling the admin to add them again with the editor's picture button, which uploads each image and asks for its alt text (§4.3) |
| 9 | Skipped heading levels (an H2 followed directly by an H4) | Not changed. A warning lists them, because the heading structure drives the table of contents |
| 10 | Links | Kept. `href`s that aren't `http(s)://` or `mailto:` are dropped (the link text stays); before checking the scheme, the href is normalised by stripping control characters and spaces (so `java\tscript:` is caught the same as `javascript:`). External links get no extra attributes; the editor can add them |

Anything mammoth reports (for example an unrecognised custom style) is added to the warnings.

**Defence in depth**, applied before the rules above: elements outside a fixed allowlist (`p`, `h1`–`h6`, `strong`, `b`, `em`, `i`, `u`, `s`, `sub`, `sup`, `a`, `ul`, `ol`, `li`, `table`, `thead`, `tbody`, `tr`, `th`, `td`, `br`, `img`, `blockquote`) are unwrapped (their content kept); active elements (`script`, `style`, `template`, `iframe`, `object`, `embed`, `svg`, `math`, `form`, and similar) are removed together with their content; and every attribute not on a small per-tag allowlist (e.g. `href`/`title` on `a`; `colspan`/`rowspan` on `th`/`td`) is stripped. The server-side Purifier profile enforces the same boundaries independently, so this is belt-and-suspenders, not the only safeguard.

The import button also shows "Importing…" (and is disabled) while the file is being read and converted, so a slow conversion can't be triggered twice.

**Server-side purification:** required for the import to work. The current `default` profile in `config/purifier.php` **does not allow tables**, so an imported page would lose all its tables on save. Add a separate `seo_content` profile, and use it for `content` and FAQ answers in `SeoPageController`. The `default` profile is unchanged, so blogs and other content aren't affected. The profile allows:
- `h2`–`h6`, `p`, `br`, `strong`, `b`, `em`, `i`, `u`, `s`, `sub`, `sup`
- `ul`, `ol`, `li`
- `a[href|title|target|rel]`
- `img[src|alt|width|height|class]`
- `div[class]`, `span[class]`
- `table[class]`, `thead`, `tbody`, `tr`, `th[colspan|rowspan|scope]`, `td[colspan|rowspan]`
- `blockquote`, `code`, `pre`, `hr`

`h1` is deliberately **not** allowed in the body, because the page's H1 is the title. Inline `style` attributes are not allowed. The `seo_content` profile has no `Attr.AllowedFrameTargets` entry of its own; the pre-existing global `custom_attributes` entry (`a[target]`, allowing `_blank`/`_self`/`_target`/`_top`) applies across every profile including this one, so `target="_blank"` on links is kept without needing a profile-specific setting.

Building this profile required fixing a pre-existing bug: the global `custom_elements` entry for `<u>` in `config/purifier.php` was malformed (missing its content-model arguments) and crashed **every** call to `Purifier::clean()`, on any profile, not just the new one. It's fixed as part of this feature.

**Guidance for the client's documents** (to share with them):
- Use Word or Google Docs **heading styles** (Heading 1/2/3) for headings, not bold text in a larger font. Headings build the page structure and the table of contents.
- Put **exactly one Heading 1**, the page's H1 as written in the sheet, at the top.
- Don't put images in the document; supply them separately with their alt text (the sheet's Image URL and Image Alt Tag columns).
- Use real tables and real bulleted/numbered lists, not tabs or typed "•" characters.

**Not included:** detecting an FAQ section and moving it into the FAQ fields (the sample has none), importing several documents at once, and extracting images.

## 5. Public page

### 5.1 Controller

New file `app/Http/Controllers/Frontend/SeoPageController.php`. `show($category, $subcategory = null, $topic = null)` does the following:
1. Build `path` from the non-null segments and look it up with `SeoPage::active()->where('path', $path)`.
2. If there's no match, or any ancestor is inactive, return `abort(404)`.
3. Load `author`, active `children` (for levels 1 and 2), and the sidebar tree for the page's category (from cache).
4. Return `view('frontend.seo-page.show', …)` with `$metaTitle` and `$metaDescription` (the variables `_head.blade.php` already reads), plus the new `$canonicalUrl` and `$metaImage`.

### 5.2 Layout (`resources/views/frontend/seo-page/show.blade.php`)

It extends the existing frontend layout with `isPageBreadcrumb = false`. The site's generic breadcrumb banner only knows "Home › Page", so this page renders its own multi-level breadcrumb.

- **Breadcrumb:** Home › Category › Subcategory › Topic, using each page's `nav_label` (the current page is not a link). Example: Home › SEO › SEO Link Building › Link Building Tools.
- **H1:** title, then a byline with the author's name and avatar, "Last updated {updated_at, M d, Y}", and the featured image.
- **Featured image markup:** `<img src="…" alt="{{ featured_image_alt }}" width="…" height="…" fetchpriority="high">`. The width and height are read from the stored file, so the layout doesn't shift while the image loads. The image isn't lazy-loaded because it sits near the top of the page. The block (and the width/height attributes) is only rendered when `hasFeaturedImageFile()` finds the file on disk, so a page whose image was deleted from disk still renders cleanly instead of a broken `<img>`.
- **Child cards** show each child's `nav_label` as the heading, its excerpt, and its featured image with its own alt text and `loading="lazy"` — the image tag is likewise only rendered when that child's own file exists. Cards are grouped under their `nav_group` headings (see *Grouping* below).
- **Two-column body:**
  - **Main column:**
    - Table of contents (if `show_toc` and the content has at least 2 H2s)
    - content
    - **child cards** for levels 1 and 2 (described above)
    - FAQ accordion (if there are entries)
    - CTA block (if `show_cta`)
  - **Sidebar:** the category's subcategories, by `nav_label`. The current branch is expanded to list its topics, grouped by `nav_group`, and the current page is highlighted. This supplies the sibling links the reference site relies on.
- **Grouping** (sidebar and child cards): pages without a `nav_group` come first, in `sort_order`. Then each group appears as a heading followed by its pages in `sort_order`. Groups are ordered by the lowest `sort_order` among their pages, so reordering pages also reorders groups and there is no separate group table. Example under `/seo/link-building`: SEO Backlinks, Benefits … (no group), then "Link Building Types & Strategies", then "Link Acquisition Methods", and so on.
- **CTA block:** a reusable partial `frontend/seo-page/_cta.blade.php` with the site's "Book a meeting" link and pricing link. The copy is fixed in the partial for this phase.

**Body image URLs:** after the table-of-contents pass (outside its cache, so the cache key stays the content hash), `ContentImageUrlService::rewrite()` points every `<img src>` whose path ends in `assets/general/images/seo-pages/{file}.{jpg|jpeg|png|webp}`, with any host or prefix, absolute or relative, at `asset('general/images/seo-pages/{file}')`. Only `src` attributes of `img` tags are touched; other images, links and text are left as they are. This makes pages authored on a local or staging install (or saved with an absolute URL) show this install's image, over this install's scheme.

### 5.3 Table of contents

- Built at render time: parse `content` with `DOMDocument`, take the H2 and H3 elements, and give each one an `id` (slug of its text, de-duplicated with `-2`, `-3`, and so on). Output the modified HTML plus a nested list of anchors.
- The ids are added **after** purification, so Purifier's attribute rules don't matter.
- Wrapped in a small service class `App\Services\SeoPage\TableOfContentsService` (`build(string $html): array{html: string, items: array}`), so it can be unit-tested independently.
- The result (the id-tagged HTML and the TOC items together) is cached, keyed by `'seo_page_toc_v1_'.md5($page->content)` with a 30-day TTL — not by `id` + `updated_at`, and not forever. Keying on the content itself (rather than the row's id/`updated_at`, whose one-second resolution can't tell apart two saves in the same second) means the cache is naturally correct after an edit without needing to be cleared, and the TTL bounds how long a stale entry could survive after a content-only DB write outside the model (there is none today, but the TTL costs nothing).

## 6. SEO output

### 6.1 Head tags

`resources/views/frontend/layouts/partials/_head.blade.php` currently prints `$metaTitle`, `$metaDescription` and a site-wide `og:image`, and has no canonical tag. Changes, all backward compatible (the defaults keep today's output for every other page):
- `og:image` uses `$metaImage ?? setting('meta_image')`.
- Add `og:image:alt` when `$metaImageAlt` is set.
- Add `<link rel="canonical" href="{{ $canonicalUrl }}">` when `$canonicalUrl` is set.
- Add `og:title`, `og:description`, `og:url` and `og:type` (`article` for SEO pages) when those variables are set.
- Add `@stack('schema')` so pages can push JSON-LD.

SEO pages set `$fullTitle`; `_head.blade.php` prints it as the whole `<title>`, without the site-name suffix other pages get.

### 6.2 Structured data (JSON-LD, pushed to `schema`)

- **BreadcrumbList** on every level.
- **Article** on every level:
  - `headline`
  - `description`
  - `image`: the featured image's absolute URL
  - `datePublished` from `published_at`
  - `dateModified` from `updated_at`
  - `author` as a `Person` with the admin's full name
  - `publisher` as an `Organization` with the site title and logo from settings
  - `mainEntityOfPage` set to the canonical URL
- **FAQPage** when the page has FAQ entries.

Built by one class, `App\Services\SeoPage\SchemaBuilderService`, which returns arrays. The view prints them with `json_encode(..., JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)`, with `<` and `>` escaped so JSON-LD content can't close the surrounding `<script>` element.

### 6.3 Sitemap

The site has no sitemap. Add `GET /sitemap-seo.xml`, which lists every publicly visible SEO page (per §3.3), with `<lastmod>` set to `updated_at`. It is cached and the cache is cleared by the model events. Reference it in `robots.txt`. A full site-wide sitemap is a separate task.

## 7. Caching

- `seo_pages_tree`: all active pages as a nested structure (id, nav_label, nav_group, path, depth, sort_order, parent_id). Used for the sidebar, ancestor lookup and the sitemap. Stored with `Cache::rememberForever` (the existing pattern, as in `Services::getAllWithCache`) and cleared in `SeoPage::booted()`, via `DB::afterCommit()` so a rolled-back write never invalidates a cache that's still correct.
- The TOC (and the id-tagged body HTML) is cached by content hash with a 30-day TTL, not per page id (§5.3).
- The page itself is a single indexed query on `path`, so it isn't cached.

## 8. Error handling and edge cases

| Case | Behaviour |
|---|---|
| Unknown path, inactive page or inactive ancestor | 404, using the site's existing error page |
| Topic URL under the wrong subcategory | 404: the lookup is by full `path` |
| Category slug not in the route pattern | Route doesn't match; falls through to 404. The admin form warns about this when a category is created |
| Slug renamed | Paths of the page and its descendants are recomputed. Old URLs return 404. Adding 301 redirects for changed paths is a follow-up (a `seo_page_redirects` table), not part of this phase |
| Delete with children | Blocked with an error message |
| Author admin deleted | `author_id` becomes null; the byline and schema omit the author |
| Content with no H2 | TOC is not shown |
| Featured image file deleted from disk | Page still renders; `og:image` falls back to `setting('meta_image')`. The admin index flags the page with a "missing image" badge |
| Images inside the body content | Uploaded from the editor (picture button, drag and drop or paste) to `assets/general/images/seo-pages/` as `{slug}-body[-n].{ext}`, or inserted by `http(s)` URL. Alt text is required in the editor and again on save (§4.1, §4.3). Purifier keeps `src` and `alt`; `seo-page.css` gives body images `max-width: 100%`. They aren't lazy-loaded (that would need a server-side pass over the content). The Word import removes images from documents (§4.4, rule 8), so they are always added by hand |
| Body image uploaded but not kept | Stays on disk as an orphan file (removed from the editor, form never saved, or page deleted). Harmless; a clean-up job is a follow-up |
| Content copied between environments (e.g. local database to production) | The public page is correct: `ContentImageUrlService` points every body image under `assets/general/images/seo-pages/` at this environment's `asset()` URL. The admin editor shows the stored `src` as-is, so such images can look broken there until they are re-inserted; the page still renders correctly |
| Image URL from another host that also uses `/assets/general/images/seo-pages/` | Also rewritten to this site's folder (the rewrite matches the path on any host). It can only ever point at this site's own image folder, never elsewhere |
| Alt text that is the original upload's file name | Rejected in the editor (it compares with the uploaded file's name). The server only knows the stored name, so file names with spaces (e.g. "Screenshot 2024-01-01 at 10.00.00.png") pasted via code view are not caught server-side |
| `data:` image in the content (e.g. pasted HTML) | Stripped by the purifier on save, as before; the editor's own image paths never produce one |

## 9. Testing

The project uses Pest (`tests/`). Feature tests run against a dedicated MySQL database, `tygcrm_test` (not SQLite), configured via `.env.testing`; a guard test (`TestDatabaseTest`) fails every feature test run if the active connection isn't `tygcrm_test`, so a misconfigured environment can't accidentally run against the dev database `tygcrm`.

**Setting up `tygcrm_test` on a new machine.** The app reads the database while it boots (`setting()` in service providers, `Page::getValidSlugs()` when registering routes), so the test database can't start empty: it is a copy of the dev database. From the project root in Git Bash (WAMP's MySQL is `root` with no password by default; add `-p` / change `-u root` to match `DB_USERNAME` in `.env`, and adjust the MySQL version in the path):

```bash
MYSQL=/c/wamp64/bin/mysql/mysql8.0.31/bin
# 1. Create the database
"$MYSQL/mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS tygcrm_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
# 2. Copy the dev database into it
"$MYSQL/mysqldump.exe" -u root --single-transaction --no-tablespaces tygcrm > storage/app/tygcrm-test-seed.sql
"$MYSQL/mysql.exe" -u root tygcrm_test < storage/app/tygcrm-test-seed.sql
rm storage/app/tygcrm-test-seed.sql
# 3. .env.testing (git-ignored) so artisan commands can target it with --env=testing
sed -e 's/^APP_ENV=.*/APP_ENV=testing/' -e 's/^DB_DATABASE=.*/DB_DATABASE=tygcrm_test/' .env > .env.testing
# 4. Bring its schema up to date (the dev copy may predate this branch's migrations)
php artisan migrate --env=testing
```

Then `php artisan test` (or `./vendor/bin/pest`). `phpunit.xml` pins `DB_CONNECTION=mysql`, `DB_DATABASE=tygcrm_test` and `APP_URL=http://localhost` for the test run itself, whatever `.env`/`.env.testing` say. `APP_URL` matters: `.env` has `http://localhost/build-saas-links/`, and the test client builds request URLs from it, so every request would get a `/build-saas-links/...` path and match no route. Each feature test runs inside a transaction that is rolled back (`DatabaseTransactions` in `tests/Pest.php`), so the copy stays unchanged; never run DDL, `migrate:fresh`/`refresh`/`rollback`, `db:wipe` or `db:seed` against it. If `php artisan config:cache` was run, clear it (`php artisan config:clear`) or the guard test will fail. When a later branch adds a migration, apply it here with `php artisan migrate --env=testing`.

**`robots.txt`:** its `Sitemap: https://buildsaaslinks.com/sitemap-seo.xml` line is production-only (an absolute production URL). Locally and in tests, check the sitemap at `/sitemap-seo.xml` directly; nothing tests `robots.txt`.

**Feature tests:**
- `/seo`, `/seo/{sub}` and `/seo/{sub}/{topic}` return 200 with the right H1.
- 404 for: an unknown path; an inactive page; an active topic under an inactive subcategory; a topic requested under the wrong subcategory; a 4-segment URL; a first segment other than `seo`.
- Renaming a subcategory's slug updates its topics' `path`.
- Validation rejects:
  - a depth-4 parent
  - a duplicate slug among siblings
  - a missing `nav_label`, or a `cluster` value other than core, inner or outer
  - content that is empty after purification (`<p><br></p>`); content that is only an image or a table is accepted
  - renaming the routed `seo` category while it has children (allowed once it has none)
  - a CMS page (Page admin store/update) with a routed category slug such as `seo` or `SEO`
- Validation accepts a 92-character meta title (the client's longest).
- The sidebar and child cards group pages under their `nav_group` headings in the order described in §5.2, and the breadcrumb shows `nav_label`s.
- An uploaded featured image is saved under a file name built from the slug, with `-2` appended on a clash.
- (Manual check, because Apache performs it:) a URL with a trailing slash returns a 301 to the form without the slash.
  - a level-1 slug equal to an existing page slug or a reserved word
  - moving a page to a parent at a different depth
  - creating a page without a featured image or without alt text
  - alt text that is a file name (e.g. `image1.jpg`)
- The featured image renders with its alt text, and `og:image:alt` is set.
- Body image upload (`BodyImageUploadTest`): 403 for an admin with only `seo-page-list`; JSON 422 for a missing file, a non-image, a GIF and a file over 2 MB (nothing stored); success stores `{slug}-body.png` and returns its root-relative URL (the path of `asset()`, no host); a second upload gets `-body-2`; the slug is slugified on the server and falls back to `image`.
- Content images: saving is rejected when an `<img>` has no alt, an empty or blank alt, a bare file-name alt, or an alt equal to its `src` file name, and the error names the images ("… (missing for: b-body.png, c-body.webp)."); an alt that mentions a file name in a sentence, and descriptive alt text, are saved as is.
- Body image URLs (`ContentImageUrlServiceTest`, `PublicPageTest`): a page whose content has `<img src="http://staging.example/assets/general/images/seo-pages/a-body.png">` (or a `/build-saas-links/assets/...` or `assets/...` source) renders it as `asset('general/images/seo-pages/a-body.png')`; other image sources, nested paths, links and alt text are untouched.
- The create form's content textarea has the class `seo-page-editor` (not `summernote`) and the upload URL.
- Deleting a page that has children is refused.
- A page with FAQ entries outputs FAQPage JSON-LD. Every page outputs BreadcrumbList and Article JSON-LD, plus a canonical tag.
- The admin routes need the `seo-page-*` permissions.
- The sitemap lists active pages only.

**Unit tests:**
- `TableOfContentsService`: ids assigned, duplicates de-duplicated, H3s nested under H2s, no H2 gives no items.
- `SchemaBuilderService`: output shape for each type.
- `seo_content` Purifier profile: keeps `table`/`thead`/`tbody`/`th`/`td`, `h2`, `h3`, lists, `img[alt]` and `s`/`sub`/`sup`. Strips `h1`, inline `style`, `<script>` and `data:` image sources. The `default` profile still strips tables, so blogs are unchanged.

**Word import (JavaScript):** the project has no JS test runner. Add **Vitest with jsdom** as dev dependencies (`npm run test:js`) for `cleanDocxHtml` only. The test reads `seo-page-docx-clean.js`, runs it in the jsdom window the same way a `<script>` tag would, and calls `window.cleanDocxHtml`. That keeps the file a plain script with no module syntax. The fixture is `docs/samples/out.html`, the raw mammoth output of the client's sample. Expected results on that fixture:
- 0 `<a>` without `href`; no heading contains a lone `<strong>`.
- 5 tables, each wrapped in `div.table-responsive`, each with exactly 1 header row and the rest in `<tbody>` as `<td>`; no `<p>` directly inside a single-paragraph cell.
- The returned `title` is "27 Link Building Tools for Research, Outreach, Monitoring & Automation", and there's no `<h1>` in the HTML.
- `stats` reports 36 H2, 71 H3, 5 tables and 277 list items.

Additional small cases: a second H1 is demoted to H2 with a warning; a `data:` image is removed with a warning that points to the editor's picture button; a skipped heading level produces a warning; a `javascript:` link loses its `href` but keeps its text.

**Manual check** (once, before release): import the sample `.docx` in the admin form, save, and confirm the public page shows all 5 tables and a table of contents built from the 36 H2s. Then add a body image each way (picture dialog file, drag and drop, paste, image URL), check the alt dialog blocks an empty or file-name alt, edit an alt through the image popover, save, and confirm the images and their alt text appear on the public page.

## 10. Files touched

| New | Changed |
|---|---|
| `database/migrations/xxxx_create_seo_pages_table.php` | `routes/web.php` (public route, sitemap) |
| `database/migrations/xxxx_add_seo_page_permissions.php` | `routes/admin.php` (resource, body image upload route) |
| `app/Models/SeoPage.php` | `config/admin_menus.php` (menu item) |
| `app/Http/Controllers/Backend/SeoPageController.php` | `database/seeders/PermissionTableSeeder.php` |
| `app/Http/Controllers/Frontend/SeoPageController.php` | `resources/views/frontend/layouts/partials/_head.blade.php` (canonical, OG, schema stack) |
| `app/Http/Requests/SeoPageRequest.php` | `robots.txt` (sitemap line) |
| `app/Services/SeoPage/TableOfContentsService.php` | |
| `app/Services/SeoPage/SchemaBuilderService.php` | |
| `app/Services/SeoPage/NavGroupService.php` | |
| `app/Services/SeoPage/SeoPageImageService.php` | |
| `app/Services/SeoPage/ContentImageUrlService.php` | |
| `resources/views/backend/seo-page/{index,create,edit,_form}.blade.php` | |
| `resources/views/frontend/seo-page/{show,_sidebar,_toc,_faq,_cta,_children}.blade.php` | |
| `resources/views/frontend/seo-page/sitemap.blade.php` | |
| `tests/Feature/SeoPage*Test.php`, `tests/Unit/SeoPage*Test.php` | `config/purifier.php` (new `seo_content` profile) |
| `assets/backend/js/mammoth.browser.min.js` (vendored, pinned 1.x) | `package.json` (Vitest + jsdom dev dependencies, `test:js` script) |
| `assets/backend/js/seo-page-docx-clean.js` (`cleanDocxHtml`) | |
| `assets/backend/js/seo-page-docx-import.js` (button wiring) | |
| `assets/backend/js/seo-page-form.js` (admin form behaviour) | |
| `assets/frontend/css/seo-page.css` (public page styles) | |
| `tests/js/docx-import.test.js` | |
| `tests/Feature/SeoPage/BodyImageUploadTest.php` | |
| `tests/Feature/SeoPage/ContentImageUrlServiceTest.php` | |

## 11. Deployment

Production needs these steps, in order:
1. `php artisan migrate` — runs the two new migrations: `2026_09_25_000001_create_seo_pages_table` (creates `seo_pages`) and `2026_09_25_000002_add_seo_page_permissions` (inserts `seo-page-list` and `seo-page-manage` for the `admin` guard, grants them to `super-admin` and resets Spatie's permission cache).
2. `php artisan optimize:clear` — clears the config, route, view and application caches, so the new routes, the admin menu entry (read from `config/admin_menus.php`) and the new `seo_content` Purifier profile are picked up. If production runs with cached routes/config, rebuild them afterwards with `php artisan optimize` (or the project's own `php artisan app:optimize`, which wraps it).
3. Nothing to do for the Purifier definition cache. HTML Purifier caches the definitions built from `custom_definition`/`custom_elements`/`custom_attributes` as files under `storage/app/purifier`, and `cache:clear`/`optimize:clear` do **not** touch that directory. This feature changed `custom_elements` (the `<u>` fix), so it bumps `custom_definition.rev` from 1 to 2 in `config/purifier.php`; the new revision is built fresh and the old cached files are simply no longer used. Removing them is optional housekeeping: `rm storage/app/purifier/*` (the directory must stay writable by the web server).
4. Upload the plain (non-built) asset files: `assets/backend/js/mammoth.browser.min.js`, `seo-page-docx-clean.js`, `seo-page-docx-import.js`, `seo-page-form.js` (which also sets up the content editor and its image upload), and `assets/frontend/css/seo-page.css`. No `npm run build` is needed — these are loaded directly, the same way `summernote-lite.min.js` is. (The new npm dev dependencies, Vitest and jsdom, are only for `npm run test:js`; production doesn't need them.)
5. Make sure `assets/general/images/seo-pages/` is writable by the web server. It holds both featured images and body images (`{slug}-body[-n].{ext}`), so body images need nothing else to deploy. It's created on first upload, and git ignores everything under `assets/general/images/`, so page images must be backed up separately from the repository.
6. Make sure `robots.txt` is deployed. It now ends with `Sitemap: https://buildsaaslinks.com/sitemap-seo.xml`, a production-only absolute URL.
