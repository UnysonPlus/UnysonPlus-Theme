# Unyson+ Theme — Hook Reference

This is the canonical list of action and filter hooks exposed by the
Unyson+ theme. Use them from a plugin, an `mu-plugin`, a child theme's
`functions.php`, or the **Misc → Custom Header & Footer Scripts** option
(for snippet-style additions).

All hook names are prefixed `unysonplus_`. Naming convention:

| Pattern | Fires |
|---|---|
| `unysonplus_before_<thing>` | Just before `<thing>` opens |
| `unysonplus_after_<thing>` | Just after `<thing>` closes |
| `unysonplus_<thing>_top` | Inside `<thing>`, near the top |
| `unysonplus_<thing>_bottom` | Inside `<thing>`, near the bottom |

## Quick Reference

- [Page structure](#page-structure) — header, footer, body
- [Layout wrappers](#layout-wrappers) — main, sidebar
- [Loop](#loop) — post lists, archive titles
- [Entry / Article](#entry--article) — single posts, content, title
- [Filter hooks](#filter-hooks) — *(none currently exposed; future placeholder)*
- [Usage examples](#usage-examples)

---

## Page structure

| Hook | Fires | File |
|---|---|---|
| `unysonplus_before_header` | Just before the site header renders (after the skip-link, inside `#page`) | [header.php](header.php) |
| `unysonplus_header_top` | Inside `<header id="masthead">`, first child | [template-parts/header-builder.php](template-parts/header-builder.php) |
| `unysonplus_header_bottom` | Inside `<header id="masthead">`, last child | [template-parts/header-builder.php](template-parts/header-builder.php) |
| `unysonplus_after_header` | Just after the site header renders | [header.php](header.php) |
| `unysonplus_before_footer` | Just before the site footer renders | [footer.php](footer.php) |
| `unysonplus_after_footer` | Just after the site footer renders | [footer.php](footer.php) |
| `unysonplus_after` | Just before `wp_footer()` fires, after `</div>#page` | [footer.php](footer.php) |

## Layout wrappers

| Hook | Fires | File |
|---|---|---|
| `unysonplus_before_main` | Just before `<main id="main">` opens. Lives inside `#content > .container > .with-sidebar`. | [inc/includes/layout.php](inc/includes/layout.php) |
| `unysonplus_after_main` | Just after `</main>` closes, before the sidebar renders. | [inc/includes/layout.php](inc/includes/layout.php) |
| `unysonplus_before_sidebar` | Just before the sidebar `<aside>` opens (only fires when the chosen widget area is active). | [sidebar.php](sidebar.php), [sidebar-left.php](sidebar-left.php) |
| `unysonplus_after_sidebar` | Just after the sidebar `</aside>` closes. | [sidebar.php](sidebar.php), [sidebar-left.php](sidebar-left.php) |

## Loop

| Hook | Fires | File |
|---|---|---|
| `unysonplus_before_archive_title` | Just before `<header class="page-header">` on archive / search pages. | [archive.php](archive.php), [search.php](search.php) |
| `unysonplus_after_archive_title` | Just after the page-header closes. | [archive.php](archive.php), [search.php](search.php) |
| `unysonplus_before_loop` | Just before `while ( have_posts() )` starts. | [archive.php](archive.php), [index.php](index.php), [search.php](search.php) |
| `unysonplus_after_loop` | Just after `endwhile;`, before pagination. | [archive.php](archive.php), [index.php](index.php), [search.php](search.php) |

## Entry / Article

| Hook | Fires | File |
|---|---|---|
| `unysonplus_before_entry` | Just before the single-post template part loads. | [single.php](single.php) |
| `unysonplus_entry_top` | Inside `<article>`, first child — before any entry content. | All `template-parts/content-*.php` |
| `unysonplus_entry_header` | Inside `<header class="entry-header">` — fires `unysonplus_entry_title` and `unysonplus_breadcrumbs` by default. | All `template-parts/content-*.php` |
| `unysonplus_before_entry_title` | Just before the entry `<h1>` / `<h2>` prints. | [inc/hooks.php](inc/hooks.php) (via `unysonplus_entry_title()`), [template-parts/content-search.php](template-parts/content-search.php) |
| `unysonplus_after_entry_title` | Just after the entry `<h1>` / `<h2>` prints. | Same as above |
| `unysonplus_before_entry_content` | Just before `<div class="entry-content">` (or `.entry-summary` in search). | All `template-parts/content-*.php` |
| `unysonplus_after_entry_content` | Just after `</div><!-- .entry-content -->`. | All `template-parts/content-*.php` |
| `unysonplus_entry_bottom` | Inside `<article>`, last child — after the entry footer. | All `template-parts/content-*.php` |
| `unysonplus_after_entry` | Just after the single-post template part loads. | [single.php](single.php) |

## Filter hooks

The theme does not yet expose any custom `apply_filters()` hooks. This
section is a placeholder; filters will be added in a future release if
specific extension points emerge from user feedback.

---

## Usage examples

All examples below use anonymous closures for brevity. Wrap them in
`if ( ! function_exists() )` blocks for production code or move them
into a child theme / `mu-plugin`.

### 1. Inject a promo bar above the site header

```php
add_action( 'unysonplus_before_header', function () {
    if ( is_front_page() ) {
        echo '<div class="promo-bar">Free shipping this week — code FREE25</div>';
    }
} );
```

### 2. Add social share buttons after every post's content

```php
add_action( 'unysonplus_after_entry_content', function () {
    if ( ! is_singular( 'post' ) ) { return; }
    ?>
    <div class="social-share">
        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode( get_permalink() ); ?>">Tweet</a>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>">Share</a>
    </div>
    <?php
} );
```

### 3. Display a "Last updated" badge after the entry title

```php
add_action( 'unysonplus_after_entry_title', function () {
    if ( ! is_singular( 'post' ) ) { return; }
    $modified = get_the_modified_date();
    echo '<p class="entry-updated">Last updated: ' . esc_html( $modified ) . '</p>';
} );
```

### 4. Wrap the sidebar in a custom container

```php
add_action( 'unysonplus_before_sidebar', function () {
    echo '<div class="sidebar-shell sticky-top">';
} );
add_action( 'unysonplus_after_sidebar', function () {
    echo '</div>';
} );
```

### 5. Show an admin-only notice at the top of every page

```php
add_action( 'unysonplus_before_main', function () {
    if ( ! current_user_can( 'edit_posts' ) ) { return; }
    echo '<div class="alert alert-info">You are logged in as ' . esc_html( wp_get_current_user()->display_name ) . '.</div>';
} );
```

### 6. Inject a "Related posts" block at the bottom of single articles

```php
add_action( 'unysonplus_entry_bottom', function () {
    if ( ! is_singular( 'post' ) ) { return; }
    $related = new WP_Query( array(
        'category__in'        => wp_get_post_categories( get_the_ID() ),
        'posts_per_page'      => 3,
        'post__not_in'        => array( get_the_ID() ),
        'ignore_sticky_posts' => true,
    ) );
    if ( ! $related->have_posts() ) { return; }
    echo '<aside class="related-posts"><h3>Related</h3><ul>';
    while ( $related->have_posts() ) {
        $related->the_post();
        echo '<li><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></li>';
    }
    echo '</ul></aside>';
    wp_reset_postdata();
} );
```

### 7. Bracket each entry with `<section>` tags

```php
add_action( 'unysonplus_entry_top', function () {
    echo '<section class="entry-shell">';
} );
add_action( 'unysonplus_entry_bottom', function () {
    echo '</section>';
} );
```

### 8. Add a continue-reading prompt after every archive entry summary

```php
add_action( 'unysonplus_after_entry_content', function () {
    if ( ! is_archive() ) { return; }
    echo '<a class="read-more" href="' . esc_url( get_permalink() ) . '">Continue reading →</a>';
} );
```

### 9. Wrap the archive title in a colored band

```php
add_action( 'unysonplus_before_archive_title', function () {
    echo '<div class="archive-band">';
} );
add_action( 'unysonplus_after_archive_title', function () {
    echo '</div>';
} );
```

### 10. Add a "no results" hint above the loop on the search page

```php
add_action( 'unysonplus_before_loop', function () {
    if ( ! is_search() ) { return; }
    echo '<p class="search-hint">Tip: use quotes to find exact phrases.</p>';
} );
```

---

## Adding new hooks

When proposing a new hook, the bar is high: it should serve a use case
that's awkward without it (i.e. forking a template). Submit a pull
request or open an issue describing the customization that motivated
it. Keep the surface tight — Genesis-style density (hundreds of hooks)
makes a theme harder to maintain, not easier to extend.
