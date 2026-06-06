<?php
/**
 * Category archive.
 *
 * Delegates to the shared archive template so categories use the same
 * hook-driven listing, gated header, and Blog → Archives settings instead of
 * a separate, out-of-date layout.
 *
 * @package Unysonplus
 */

require locate_template( 'archive.php' );
