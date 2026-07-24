---
type: guide
name: theme-settings
audience: AI agents reading/generating theme-settings values and design export files; anyone extending the Theme Settings
last-verified-against: unysonplus-theme 2.1.43 / unysonplus framework 2.8.40 (site_background verified vs export 2.1.42, 2026-06-02)
canonical: parent theme (child themes document only their deltas — see "Child themes" below)
---

# Theme Settings — schema, storage, CSS mapping, export/import

This is the **canonical** doc for the `unysonplus-theme` Theme Settings (the global design
layer). Page content lives in the page builder (see the plugin's
`shortcodes/extensions/page-builder/AGENTS.md`); **this layer is the site chrome + design
tokens**: colors, typography, header, footer, spacing, custom CSS. A complete "design package"
for a new site = a **theme-settings design file** (this doc) + **builder template files** (page
builder doc).

## Where settings are defined

`framework-customizations/theme/options/` — `settings.php` aggregates the tabs:

> **Leaf-file naming pattern (REQUIRED for maintenance):** a leaf option file is prefixed with the
> **tab that owns it** — e.g. `general-*.php` for the General tab, `site-wide-ux-*.php` for the
> Site-wide UX tab, `header-*.php`, `footer-*.php`. When a feature MOVES to a different tab, rename
> its leaf file to the new prefix (and update the `get_options()` references) — but KEEP the internal
> `$options` array key / storage id unchanged (renaming the file never changes the DB key, so the
> render getters stay valid). This keeps files easy to find by tab.

- **General** — split into sub-tabs (order: Layout, Base, Typography, Colors, Sidebar):
  - **Layout** — `general-layout.php` (`general_layout`: site width/bg + pattern, container max-width/
    gutter, spacing scale, border **roundness** → `--radius`/`--radius-sm/md/lg`, prose reading width).
  - **Typography** — `general-typography.php` (`typography`): a **Typography Preset** (`typography_preset`
    — curated heading+body pairing + size scale, like the Color Presets) drives `--font-heading`/
    `--font-body` + the h1–h6 scale; **Custom** uses `heading_font` + `body` + optional per-heading
    `h1`–`h6` overrides, plus `body_link*`. Resolved by `inc/includes/css-tokens.php`
    (`unysonplus_typography_presets()` / `unysonplus_typography_config()`); headings are CONSUMED in
    style.css (`h1{font-size:var(--h1-font-size,revert);…}`). Google fonts for the effective families
    load via `inc/hooks.php`. **+ Custom Fonts box** `general-fonts.php`
    (`custom_fonts` addable-box: self-hosted family + .woff2/.woff + weight/style → @font-face &
    picker registration, see `inc/includes/custom-fonts.php`).
  - **Colors** — pointer only (`html-full` note): the palette / buttons / borders / spacing presets
    live in the plugin (Unyson+ → Extensions → Shortcodes → Settings); the tab links there for
    discoverability. No options stored.
  - **Sidebar** — `general-sidebar.php` (`general_sidebar`: default position, width, content/sidebar
    gap, sticky sidebar).
  - **Preloader** — `general-preloader.php` (`general_preloader`: preloader style + bg color).
  - **Scrolling** — `general-scroll.php` (`general_scroll`: smooth anchor scroll, scroll-progress bar
    + color).
  - **Image Sizes** — MOVED to the plugin. It's now **Miscellaneous → Media**, provided by the
    Unyson+ shortcodes extension (`includes/theme-settings/miscellaneous-media.php` schema +
    `-handlers.php` behaviour: `add_image_size()` on `init` + the `image_size_names_choose` picker
    filter). Stored under the same `theme_image_sizes` key, so no migration. The theme no longer
    ships `general-image-sizes.php` / `inc/includes/image-sizes.php`.

  All wrapped in `group` containers for editor organization — groups flatten, leaf keys stay flat.
  The Sidebar/Preloader/Scrolling split is **read-transparent**: `unysonplus_layout_get()` merges
  `general_layout` + `general_sidebar` + `general_preloader` + `general_scroll`, so reads keep the
  same `layout_*` key names. `unysonplus_migrate_layout_settings()` (in
  `inc/includes/layout.php`, run by the central schema-migration runner — see below) moves legacy
  saved values into the new keys once.
  (`general-colors.php`/`general-spacing.php`/`general-buttons.php` are now owned by the plugin's
  Shortcodes settings; Social is its own tab.)
- **Header** — sub-tabs (Identity, Layout, Top Bar, Main Header, Bottom Bar), mirroring the footer's
  per-section structure. Each row is its own storage key, so the preset/slot system reads them
  generically (see `unysonplus_preset_option_ids('header')`).
  - **Identity** — `header-identity.php` (`header_logo` incl. logo image + favicon, **two-way synced**
    with WP core `custom_logo` / `site_icon` via `inc/includes/identity-sync.php`).
  - **Layout** — `header-layout.php` (`header_layout`: the header *chrome* — **`header_mode`**
    (top / vertical L-R / off-canvas / overlay) + **`vertical_width`** (both moved here from
    General → Layout), container, min_height, mobile_min_height, mobile_breakpoint, bg_color,
    header_behavior). `header_mode`/`vertical_width` read via `unysonplus_header_layout_get()`
    (legacy `general_layout` keys are the fallback).
  - **Menu** — `header-menu.php` (`header_menu`: link color, hover/active color, link padding X/Y,
    dropdown bg → the `--menu-*` tokens; folded into the generated stylesheet by theme-vars, falling
    back to style.css defaults).
    - **Menu-item sub-labels (from the Description field).** A top-level header-menu item's WP
      **Description** (Appearance → Menus → item → Description; enable it via *Screen Options*) is
      rendered as a small sub-label under the link text — e.g. `Home` / "Start Here". Implemented in
      **`inc/menus.php`** as `unysonplus_nav_menu_item_sublabel()` (a `nav_menu_item_title` filter):
      it wraps the title in `.menu-label` and appends `<small class="menu-sublabel">`. Only fires for
      top-level items (`$depth === 0`) of the header nav locations — default `primary` + `secondary`,
      filterable via **`unysonplus_menu_sublabel_locations`** — and only when a Description is set, so
      existing menus are untouched. Base styling (stack + small/muted) lives in
      `assets/css/header-footer-builder.css` (`.menu-sublabel`); child themes just re-colour it.
  - **Top Bar** — `header-topbar.php` (`header_topbar`: bg/text + left/center/right columns).
  - **Main Header** — `header-main.php` (`header_main`: left/center/right columns; logo + primary
    menu defaults).
  - **Bottom Bar** — `header-bottombar.php` (`header_bottombar`: bg/text + left/center/right columns).

  **No Enable switch on the bars** — like the footer, Top/Bottom Bar render only when a column has an
  element (the template derives `*_enabled` from content). `unysonplus_get_active_header_config()`
  returns a config keyed by the four ids (resolved from the active preset or global settings, mirroring
  the footer loader), and `unysonplus_migrate_header_layout()` (in
  `inc/includes/header-footer-presets.php`, run by the central schema-migration runner) lifts the old single-blob `header_layout`
  (`{enabled, yes:{…}}` bars) into the four keys for both global settings and up_header preset
  post-meta — a bar that was explicitly disabled keeps its content withheld so it stays hidden. The old
  `layout_header_position` (superseded by `header_behavior`) and unused `layout_mobile_breakpoint`
  were dropped.
- **Social** — `social-settings.php` → `general-social.php` (`social_profiles` addable-box: name,
  URL, icon, new-tab). Its own top-level tab; consumed by the header Social Icons element + footer.
- **Footer** — `footer-layout.php` (`footer_bg_color/_image/_overlay`, `footer_text_color`,
  `footer_link_color`, padding, `footer_css_class`), `footer-pre/main/post.php`, `footer-copyright.php`.
- **WooCommerce** — `woocommerce-settings.php` is a **pointer tab only** (`html-full` note → the
  WooCommerce extension settings). The actual shop settings are **owned by the WooCommerce
  *extension*** (`unysonplus/framework/extensions/woocommerce/settings-options.php`), which is a
  superset (columns, per-page, sidebar, related, gallery thumbs + gallery zoom/lightbox/slider,
  catalog mode, sale badge, AJAX cart, breadcrumb) and bridges to the theme's
  `unysonplus_woocommerce_*` filters via `register_catalog_settings_bridge()`. Do **not** re-add
  theme-side Woo options — it double-feeds those filters and conflicts. The theme only defines the
  filter defaults in `inc/includes/woocommerce.php`. The pointer tab is **conditionally registered**
  in `settings.php` only when `class_exists('WooCommerce')`.
- **Misc** — `misc.php`: `misc_scroll_top`, `misc_dark_mode`, **`misc_custom_css`** (global CSS),
  `misc_custom_scripts`, `misc_analytics`, `misc_performance`, `misc_404`, `misc_maintenance`,
  `misc_dev_tools` (Developer Tools → `dev_show_demo` switch, off by default; `settings.php` reads it
  and only appends the `demo-box` showcase tab when it's `yes`).
- **Blog** — `blog-settings.php` → `blog-index.php` (`blog_index` multi): posts-listing layout
  (list/grid/masonry), columns, featured image + ratio, excerpt-vs-full + length, meta toggles,
  read-more text, pagination. Consumed by **`inc/includes/blog.php`** via the loop hooks
  (`unysonplus_before_loop`/`_after_loop`/`_entry_header`) + the `template-parts/content.php` card.
  Phased: Blog Index + Single Post + Archives/Search all done.
  **Archives & Search** (`blog-archives.php` → `blog_archives` multi): archive header show/hide, term
  description, author bio (author archives), archive layout/columns (inherit Blog Index or own),
  archive sidebar, search-results layout, custom "no results" message. Wired in `inc/includes/blog.php`:
  `unysonplus_render_archive_header()` (called from `archive.php` & `search.php`),
  `unysonplus_blog_listing_layout()` (context-aware grid/list resolver used by the loop wrapper +
  `content.php`), and a `wp`-hook sidebar override. **`category.php` now delegates to `archive.php`**
  (`require locate_template('archive.php')`) so all archives share one path.
  **Single Post** (`blog-single.php` → `blog_single` multi): sidebar, featured image (+ position),
  meta, author box, related posts (count + by category/tag), prev/next, all overridable per-post via
  the Post Settings meta box (`post-options.php` → `post_options` multi, "Default = use global").
  Wired in `inc/includes/blog.php` (featured image on `unysonplus_entry_top`/`_before_entry_content`,
  meta on `_entry_header`, author box on `_after_entry_content`, related + nav on `_after_entry`,
  sidebar via a `wp`-hook `unysonplus_set_layout_override`).

Each tab/box is a `multi` container, so its top-level option id (e.g. `general_layout`,
`header_layout`, `misc_custom_css`) stores a nested array of that group's fields.

## Storage & read/write API

- One wp_option: **`fw_theme_settings_options:{theme-id}`** (theme id from `fw()->theme->manifest->get_id()`).
- Read: `fw_get_db_settings_option( $id = null, $default = null )` — `null` returns the full values
  array keyed by top-level option id; supports multi-key paths (`'general_layout/site_bg_color'`).
- Write: `fw_set_db_settings_option( $id = null, $value )`.
- Validate posted input against the schema: `fw_get_options_values_from_input( fw()->theme->get_settings_options(), $input )`.

### Schema migrations (versioned runner)

When you change the **shape** of stored settings/preset data (split an option, rename a key, drop a
field), add a migration rather than relying on read-time fallbacks forever. They run through ONE
versioned runner in **`inc/includes/migrations.php`**:

- `UNYSONPLUS_SCHEMA_VERSION` (constant) = the current target version; `unysonplus_schema_migrations()`
  maps `version => callback`; `unysonplus_run_schema_migrations()` (admin_init) runs every callback
  whose version is newer than the stored `unysonplus_schema_version` option, in order, then advances it.
- Callbacks live next to the code they migrate and **must be idempotent** (the version gate is the fast
  path; the per-migration guards are the correctness backstop, so a re-run / fresh install is a no-op).
- To add one: write the idempotent callback, register `<n> => 'callback'`, bump the constant to `<n>`.
- Current: `1` = General → Layout split (`unysonplus_migrate_layout_settings`); `2` = header_layout
  blob split (`unysonplus_migrate_header_layout`).

## How settings become CSS (the design → tokens map)

- **`inc/includes/hf-custom-css.php` → the single generated front-end stylesheet.** On the front end
  ALL settings-driven CSS is compiled into one file at `uploads/unysonplus/unysonplus-generated.css`
  (enqueued after `parent-style`, `filemtime()` version) — the front end ships **no inline `<style>`
  blocks or per-element inline styles**. `unysonplus_generated_css()` concatenates, in order:
  (1) typography tokens (`unysonplus_css_tokens_css()`), (2) the theme-var `:root` block
  (`unysonplus_theme_vars_css()`), (3) the per-section **Custom Styling** rules (Header → Top Bar /
  Main Header / Bottom Bar via `*_custom_styling` inside `header_topbar`/`header_main`/`header_bottombar`;
  Footer → Pre / Main / Post via `{prefix}_custom_styling`) plus global rules (site title/tagline color,
  scroll-to-top, per-instance CTA buttons + footer logos). **Freshness:** the CSS is rebuilt every
  front-end load and written to disk only when its **content hash** (`unysonplus_hf_css_hash` option)
  changes — no staleness, no added compute vs the old inline emit — plus proactive rebuilds on
  `fw_settings_form_saved` / `customize_save_after`, and a `wp_add_inline_style` fallback if uploads
  isn't writable. **Class-based bits are NOT in the file:** `padding` rides the `spacing` Bootstrap
  utility classes, `container` + Custom CSS Class ride wrapper classes (`unysonplus_hf_section_render_attrs()`).
  Section Google fonts (typography-v2 `google_font`) enqueue via `unysonplus_hf_enqueue_google_fonts()`.
  Shared field set: `unysonplus_hf_custom_styling($prefix)`.
- `inc/includes/css-tokens.php` → `unysonplus_css_tokens_css()` builds the typography tokens
  (`--h1-font-family/-size/-line-height/-letter-spacing/-color` for h1–h6 + body, incl. mobile tiers).
  Front end: folded into the generated file (above). Admin: `unysonplus_emit_css_tokens()` still emits
  `<style id="unysonplus-tokens">` on `admin_head` for the live page-builder editor preview.
- `inc/includes/theme-vars.php` → `unysonplus_collect_theme_vars()` + `unysonplus_theme_vars_css()`
  read `general_layout`, `header_layout`, footer options, `typography` and build the `:root { … }`
  block (`--site-bg-color`, `--container-gutter`, `--header-bg`, `--header-min-height`,
  `--footer-*`, `--color-text`, `--font-body`, `--font-heading`, …). Front end: folded into the
  generated file. Admin: `unysonplus_emit_theme_vars()` emits inline on `admin_head` for the editor.
  (Top/Bottom Bar styling lives in the per-section rules, not here.)
  The **site background** vars (`--site-bg-color`, `--site-bg-image`, plus
  `--site-bg-position/-repeat/-size/-attachment`) are derived here from the General Layout
  **`site_background`** field — a `background-pro` value (color + gradient + image layers; video is
  not applied site-wide). Resolved via `unysonplus_get_option_color_picker()` for the color and
  `FW_Option_Type_Gradient_V2::to_css()` for the gradient. (The legacy `site_bg_color` /
  `site_bg_image` fields were removed; `site_bg_pattern` remains as a separate overlay.)
  **Verified** against a real export (theme 2.1.42, 2026-06-02): `color.value.predefined` carries a
  hex (not a slug), so the resolver returns it directly; `image.src` is `[]` until set; gradient is
  on at ≥2 `stops`. The full Background Pro value contract lives in the option-type doc
  `unysonplus/framework/includes/option-types/background-pro/AGENTS.md`.
- **Global custom CSS:** `inc/includes/misc.php` → `unysonplus_emit_custom_css()` outputs
  `misc_custom_css['custom_css']` in `<style id="unysonplus-custom-css">` at wp_head **999** (after
  all stylesheets — wins the cascade). **This is where bespoke per-design CSS goes** (e.g. a mockup's
  custom header/footer/token overrides that aren't expressible as structured settings).
- Header markup: `template-parts/header-builder.php` (reads `header_layout`). Footer: `footer.php`
  + `template-parts/footer-*.php`. Per-page CSS: `page_custom_css` (post option) via
  `inc/includes/layout.php`.

So an AI-built design lands in two places: structured settings (colors/fonts/header/footer) **and**
the global Custom CSS field — both captured by the export below.

## Export / Import (the design-distribution feature)

Implemented in **`inc/includes/settings-export-import.php`** (parent theme; auto-loaded from
`inc/includes/`, admin-only). The control lives in **Appearance → Theme Settings → Miscellaneous →
Export / Import** (a sub-tab placed just before *Reset Settings*; wired up from `misc.php` as an
`html-full` option whose markup is returned by `unysonplus_settings_io_misc_field_html()`). Actions
run through `admin-post.php` with nonce + `manage_options`; a redirect afterwards surfaces a
success/error notice (`unysonplus_settings_io_result_notice()`).

Because this markup sits **inside** the (non-multipart) settings `<form>`, the file input carries no
`name` and the button is `type="button"` — on Import, a small inline script builds a throwaway
multipart form, moves the chosen file into it, and submits that to the import handler. So nothing in
the control is posted by the form's own Save / Reset.

**Export file shape** (`.json`):
```json
{
  "_fw_settings_export": {
    "format_version": 1,
    "scope": "design",
    "theme_id": "unysonplus",
    "theme_version": "2.1.40",
    "exported_at": 1781000000,
    "excluded": ["misc_analytics","misc_performance","misc_maintenance","misc_404","misc_custom_scripts"],
    "media_stripped": true
  },
  "values": { /* top-level settings option ids → their multi-container values */ }
}
```

Rules baked in:
- **Design-only scope.** Operational keys (`misc_analytics`, `misc_performance`, `misc_maintenance`,
  `misc_404`, `misc_custom_scripts`) are excluded from export **and ignored on import** (so a design
  file can't overwrite a site's tracking/ops or inject `<script>`). The list is the filter
  `unysonplus_settings_io_exclude_keys` — a future per-tab checkbox UI narrows/widens it without
  breaking the format.
- **Media stripped.** Any value array carrying `attachment_id` (logos, bg images, favicon) is blanked
  on export (source-site ids/urls don't exist on the target). Colors/fonts/layout/CSS transfer; the
  user re-adds images.
- **Import = overlay.** Imported top-level keys overlay current values (whole multi-containers
  replaced); keys the file doesn't carry are preserved. Cross-theme files import the recognized keys
  and show a warning. Values are written in stored form (symmetric round-trip; no re-encode).

### Generating a design settings file (for AI)
To produce a design `.json` without the admin UI: build the `values` map (top-level option ids →
multi-container arrays matching each tab's `options.php`), wrap it in the envelope above with
`kind`-free `_fw_settings_export` (`scope:"design"`, correct `theme_id`/`theme_version`), omit the
excluded operational keys, and blank media. The user imports it via the panel. Pair it with builder
section/full templates for a full site design.

## Presets + Preset Library — KEEP IN SYNC WHEN OPTIONS CHANGE (REQUIRED)

Many tabs carry a **"Quick start" preset picker** (the `preset-loader` option type). Presets are
registered in **`inc/includes/settings-presets.php`** — `unysonplus_settings_preset_groups()` returns
one entry per group: `{ label, allowed_keys, presets: { key => { label, desc, values } } }`. Applying a
preset (AJAX `unysonplus_apply_settings_preset`) whitelists its `values` to `allowed_keys` and overlays
them onto the saved group values. **Preset-backed groups** (the `preset_group` ids): `header_layout`,
`general_pages`, `header_menu`, `header_topbar`, `header_main`, `header_bottombar`, `pre_footer_columns`,
`main_footer_columns`, `post_footer_columns`, `copyright_settings`, `typography`, `social_style`,
`blog_index`, `blog_card`, `portfolio_archive`.

On top of that sits the **Preset Library** (**`inc/includes/settings-presets-library.php`**) — a "Browse
Library" modal on each picker that downloads presets from the shared content repo
**`UnysonPlus-Library/presets/`** (catalog + one `values` JSON per preset) into
`uploads/unysonplus-presets/<group>/`, then injects them into the registry via the
`unysonplus_settings_preset_groups` filter so they render as cards and Apply through the same flow.
Installed library cards (`lib_` key) get a delete "×"; built-ins don't.

> **A preset stores option VALUES keyed by leaf option id.** So whenever you **add / remove / rename an
> option, or change its value shape** in a preset-backed group, the stored presets can go stale (dangling
> keys, missing new keys, wrong shape). When you touch such an option you MUST also:
>
> 1. **`allowed_keys`** — add/remove the leaf id in that group's `allowed_keys` in `settings-presets.php`
>    (an unlisted key is silently dropped on Apply; a stale listed key is harmless but should be cleaned).
> 2. **Built-in preset `values`** — update every affected preset's `values` in `settings-presets.php` so
>    they still produce the intended result under the new option shape.
> 3. **Library preset files** — regenerate the affected downloadable presets in the data repo
>    `UnysonPlus-Library/presets/<slug>.json` (+ `catalog.json`). The reliable way is to **re-dump from
>    the live registry** (a WP-loaded script that reads `unysonplus_settings_preset_groups()` and writes
>    each chosen preset's `values` to `presets/<slug>.json`), then push the repo. Hand-editing value maps
>    risks drift from the real option shape.
> 4. If the change is a **value-shape** change (not just add/remove), add a **schema migration** (see the
>    versioned runner above) so already-saved settings AND already-installed library presets are corrected.
>
> Same idea for the **page-builder Template Library** (`UnysonPlus-Library/templates/`): if you change a
> shortcode's atts, its stored template trees can go stale — regenerate the affected template exports.

## Child themes

A child theme (e.g. `payforituk`) does **not** copy this doc. If it adds or overrides settings
(extra options, changed defaults, overridden header/footer templates, its own CSS-token map), give
the child its own thin `framework-customizations/theme/options/AGENTS.md` documenting **only those
deltas**, linking back here. The export/import feature is inherited from the parent automatically
(the include lives in the parent's `inc/includes/`). Knowledge lives where the code lives.

## Verification

1. Appearance → Theme Settings → Miscellaneous → an "Export / Import" sub-tab appears just before "Reset Settings".
2. **Export design** → downloads `{theme-id}-settings-design-{date}.json`; open it: envelope present,
   `values` has design keys, no `misc_analytics`/`misc_custom_scripts`, image fields blank.
3. Change a color/font on a fresh site → **Import design** that file → setting applies, success notice.
4. Confirm analytics/maintenance on the target were untouched by the import.
5. Import a non-export `.json` → "not a valid export" error; oversized file → size error.

## Files

- `framework-customizations/theme/options/*.php` — settings schema (this folder).
- `inc/includes/settings-export-import.php` — the export/import feature.
- `inc/includes/css-tokens.php`, `inc/includes/theme-vars.php`, `inc/includes/misc.php` — settings → CSS.
- `template-parts/header-builder.php`, `footer.php`, `template-parts/footer-*.php` — chrome markup.
- Storage/API: framework `fw_get_db_settings_option()` / `fw_set_db_settings_option()`.
