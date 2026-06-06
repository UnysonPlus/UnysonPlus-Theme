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

- **General** — `general-layout.php` (`general_layout`: site width/bg, container, spacing scale,
  border **roundness** → `--radius`/`--radius-sm/md/lg`, sticky sidebar, content/sidebar gap, prose
  reading width; options are wrapped in `group` containers for editor organization — groups flatten,
  keys stay flat),
  `general-typography.php` (`typography`: h1–h6 + body family/size/line-height/letter-spacing/color,
  `body_link*`, `font_sizes`), `general-colors.php` (`theme_colors` palette), `general-spacing.php`
  (`spacing_scale`, `gap_scale`, `default_gap*`), `general-buttons.php`. (Social moved to its own tab.)
- **Header** — `header-identity.php` (`header_logo` incl. logo image + favicon, **two-way synced**
  with WP core `custom_logo` / `site_icon` via `inc/includes/identity-sync.php` — set it in Theme
  Settings or in Customize/Settings→General and the other follows), `header-layout.php`
  (`header_layout`: container, min_height, bg_color, sticky, topbar + main builder columns).
- **Social** — `social-settings.php` → `general-social.php` (`social_profiles` addable-box: name,
  URL, icon, new-tab). Its own top-level tab; consumed by the header Social Icons element + footer.
- **Footer** — `footer-layout.php` (`footer_bg_color/_image/_overlay`, `footer_text_color`,
  `footer_link_color`, padding, `footer_css_class`), `footer-pre/main/post.php`, `footer-copyright.php`.
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

## How settings become CSS (the design → tokens map)

- `inc/includes/css-tokens.php` → `unysonplus_emit_css_tokens()` reads `typography` and emits
  `<style id="unysonplus-tokens">` with `--h1-font-family/-size/-line-height/-letter-spacing/-color`
  (h1–h6 + body), incl. mobile font-size tiers. (wp_head priority 1.)
- `inc/includes/theme-vars.php` → `unysonplus_collect_theme_vars()` + `unysonplus_emit_theme_vars()`
  read `general_layout`, `header_layout`, footer options, `typography` and emit `:root { … }`
  (`--site-bg-color`, `--container-gutter`, `--header-bg`, `--header-min-height`, `--topbar-*`,
  `--footer-*`, `--color-text`, `--font-body`, `--font-heading`, …). (wp_head priority 20.)
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
