<?php if ( ! defined( 'ABSPATH' ) ) die( 'Direct access forbidden.' );

/**
 * This function defines the theme constants
 */
function unysonplus_constants(): void {

    //* Define Directory Location Constants
    if (!defined('PARENT_DIR')) define( 'PARENT_DIR', get_template_directory() );
    if (!defined('CHILD_DIR')) define( 'CHILD_DIR', get_stylesheet_directory() );
    if (!defined('UNYSONPLUS_INC_DIR')) define( 'UNYSONPLUS_INC_DIR', PARENT_DIR . '/inc' );
    if (!defined('UNYSONPLUS_CHILD_INC_DIR')) define( 'UNYSONPLUS_CHILD_INC_DIR', CHILD_DIR . '/inc' );
    if (!defined('UNYSONPLUS_JS_DIR')) define( 'UNYSONPLUS_JS_DIR', UNYSONPLUS_INC_DIR . '/js' );
    if (!defined('UNYSONPLUS_CSS_DIR')) define( 'UNYSONPLUS_CSS_DIR', UNYSONPLUS_INC_DIR . '/css' );
    if (!defined('UNYSONPLUS_WIDGETS_DIR')) define( 'UNYSONPLUS_WIDGETS_DIR', UNYSONPLUS_INC_DIR . '/widgets' );

    //* Define URL Location Constants
    if (!defined('PARENT_URL')) define( 'PARENT_URL', get_template_directory_uri() );
    if (!defined('CHILD_URL')) define( 'CHILD_URL', get_stylesheet_directory_uri() );
    if (!defined('UNYSONPLUS_INC_URL')) define( 'UNYSONPLUS_INC_URL', PARENT_URL . '/inc' );
    if (!defined('UNYSONPLUS_CHILD_INC_URL')) define( 'UNYSONPLUS_CHILD_INC_URL', CHILD_URL . '/inc' );
    if (!defined('UNYSONPLUS_JS_URL')) define( 'UNYSONPLUS_JS_URL', UNYSONPLUS_INC_URL . '/js' );
    if (!defined('UNYSONPLUS_CLASSES_URL')) define( 'UNYSONPLUS_CLASSES_URL', UNYSONPLUS_INC_URL . '/classes' );
    if (!defined('UNYSONPLUS_CSS_URL')) define( 'UNYSONPLUS_CSS_URL', UNYSONPLUS_INC_URL . '/css' );
    if (!defined('UNYSONPLUS_WIDGETS_URL')) define( 'UNYSONPLUS_WIDGETS_URL', UNYSONPLUS_INC_URL . '/widgets' );
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // required for is_plugin_active to work properly.

class Theme_Includes {

    private static ?string $rel_path = null;
    private static bool $initialized = false;

    public static function init(): void {
        if (self::$initialized) return;
        self::$initialized = true;

        /**
         * Hard dependency: the UnysonPlus plugin (the Unyson+ framework, which
         * defines the `FW` constant). Most of the theme's `inc/includes/*.php`
         * files start with `if ( ! defined( 'FW' ) ) { die( 'Forbidden' ); }`,
         * so auto-including them with the plugin inactive halts the whole page
         * (the "Forbidden" white screen — e.g. when the theme is activated
         * BEFORE the plugin). Bail out before loading any framework-dependent
         * file: instead surface the TGMPA "required plugin" installer + an admin
         * notice so the user can install/activate UnysonPlus, then load fully on
         * the next request once `FW` is available.
         */
        if (!defined('FW')) {
            self::bootstrap_without_framework();
            return;
        }

        /**
         * Only frontend
         */
        if (!is_admin()) {
            add_action('wp_enqueue_scripts', [__CLASS__, '_action_enqueue_scripts'], 10);
        } else {
            add_action('admin_enqueue_scripts', [__CLASS__, '_action_admin_enqueue_scripts'], 20);
        }

        /**
         * Both frontend and backend
         */
        //self::include_child_first('/startup.php');
        //self::include_child_first('/helpers-unyson.php');
        self::include_child_first('/helpers.php');
        self::include_child_first('/helpers-shortcode-options.php');
        self::include_child_first('/helpers-shortcode-get-option.php');
        self::include_child_first('/hooks.php');
        self::include_child_first('/menus.php');
        self::include_child_first('/tgm-plugins.php');
        //self::include_child_first('/customizer.php');
        //self::include_child_first('/hooks-structure.php');
        //self::include_child_first('/template-tags.php');
        //self::include_child_first('/wp-sass/wp-sass.php');

        // Automatically include all PHP files from organized subfolders
        $subfolders = ['helpers', 'shortcodes', 'widgets', 'menus', 'includes', 'classes'];
        foreach ($subfolders as $folder) {
            self::include_all_child_first("/$folder");
        }

        add_action('init', [__CLASS__, '_action_init']);
        add_action('widgets_init', [__CLASS__, '_action_widgets_init']);
    }

    /**
     * Loaded in place of the normal theme includes when the UnysonPlus plugin
     * (the `FW` framework) is not active. Keeps wp-admin usable and prompts the
     * user to install/activate the required plugin instead of fatally dying.
     *
     * @internal
     */
    private static function bootstrap_without_framework(): void {
        // TGMPA already registers UnysonPlus as a required plugin (see
        // inc/classes/tgm-plugins.php). Load it directly so the one-click
        // installer + nag notice still appear even though the rest of the
        // (framework-dependent) theme is skipped. TGMPA itself has no FW deps.
        $tgmpa_class = self::get_parent_path('/classes/class-tgm-plugin-activation.php');
        $tgmpa_reg   = self::get_parent_path('/classes/tgm-plugins.php');
        if (file_exists($tgmpa_class)) require_once $tgmpa_class;
        if (file_exists($tgmpa_reg))   require_once $tgmpa_reg;

        // Belt-and-suspenders: a plain admin notice in case TGMPA is unavailable.
        add_action('admin_notices', [__CLASS__, '_notice_requires_unysonplus']);

        // The front-end templates call theme/framework helpers that we just
        // skipped, so rendering them would fatal (HTTP 500). Short-circuit with
        // a clean "requires plugin" screen (503) instead of a white error page.
        if (!is_admin()) {
            add_action('template_redirect', [__CLASS__, '_frontend_dependency_screen'], 0);
        }
    }

    /**
     * Minimal front-end screen shown when the UnysonPlus plugin is missing, in
     * place of the theme's (now un-loadable) templates. Admins get an actionable
     * link to the dashboard; visitors get a generic "temporarily unavailable".
     *
     * @internal
     */
    public static function _frontend_dependency_screen(): void {
        if (!headers_sent()) {
            status_header(503);
            nocache_headers();
            header('Content-Type: text/html; charset=utf-8');
            header('Retry-After: 3600');
        }

        $is_admin_user = current_user_can('install_plugins') || current_user_can('switch_themes');
        $title   = esc_html__('Site temporarily unavailable', 'unysonplus');
        $message = $is_admin_user
            ? esc_html__('The active theme (Unyson+) requires the UnysonPlus plugin, which is not active. Install or activate it to bring the site back online.', 'unysonplus')
            : esc_html__('The site is undergoing maintenance and will be back shortly.', 'unysonplus');
        $action  = $is_admin_user
            ? '<p style="margin-top:1.5rem"><a href="' . esc_url(admin_url('themes.php?page=tgmpa-install-plugins'))
                . '" style="display:inline-block;padding:.6rem 1.1rem;background:#0d6efd;color:#fff;border-radius:6px;text-decoration:none;font-weight:600">'
                . esc_html__('Install UnysonPlus', 'unysonplus') . '</a></p>'
            : '';

        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
            . '<title>' . $title . '</title></head>'
            . '<body style="margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:#f6f7f9;color:#212529;display:flex;min-height:100vh;align-items:center;justify-content:center">'
            . '<div style="max-width:30rem;padding:2rem;text-align:center">'
            . '<h1 style="font-size:1.4rem;margin:0 0 .75rem">' . $title . '</h1>'
            . '<p style="margin:0;color:#6c757d;line-height:1.6">' . $message . '</p>'
            . $action
            . '</div></body></html>';
        exit;
    }

    /**
     * Fallback admin notice shown when the UnysonPlus plugin is missing.
     *
     * @internal
     */
    public static function _notice_requires_unysonplus(): void {
        if (!current_user_can('install_plugins') && !current_user_can('activate_plugins')) {
            return;
        }
        $install_url = admin_url('themes.php?page=tgmpa-install-plugins');
        echo '<div class="notice notice-error"><p><strong>'
            . esc_html__('Unyson+ Theme', 'unysonplus') . '</strong> &mdash; '
            . esc_html__('this theme requires the UnysonPlus plugin (the Unyson+ framework) to be installed and active. The theme’s features stay disabled until then.', 'unysonplus')
            . ' <a href="' . esc_url($install_url) . '">'
            . esc_html__('Install UnysonPlus', 'unysonplus') . '</a></p></div>';
    }

    private static function get_rel_path(string $append = ''): string {
        if (self::$rel_path === null) {
            self::$rel_path = '/' . basename(dirname(__FILE__));
        }
        return self::$rel_path . $append;
    }

    private static function include_all_child_first(string $dir_rel_path): void {
        $paths = [];

        if (is_child_theme()) $paths[] = self::get_child_path($dir_rel_path);
        $paths[] = self::get_parent_path($dir_rel_path);

        foreach ($paths as $path) {
            if (!is_dir($path)) continue;
            $files = glob($path . '/*.php');
            if (!$files) continue;
            foreach ($files as $file) {
                include $file; // child files override parent automatically
            }
        }
    }

    private static function dirname_to_classname(string $dirname): string {
        $class_name = explode('-', $dirname);
        $class_name = array_map('ucfirst', $class_name);
        return implode('_', $class_name);
    }

    public static function get_parent_path(string $rel_path): string {
        return get_template_directory() . self::get_rel_path($rel_path);
    }

    public static function get_child_path(string $rel_path): ?string {
        if (!is_child_theme()) return null;
        return get_stylesheet_directory() . self::get_rel_path($rel_path);
    }

    public static function include_child_first(string $rel_path): void {
        if (is_child_theme()) {
            $child = self::get_child_path($rel_path);
            if ($child && file_exists($child)) {
                include $child;
                return;
            }
        }

        $parent = self::get_parent_path($rel_path);
        if ($parent && file_exists($parent)) {
            include $parent;
        }
    }

    /**
     * @internal
     */
    public static function _action_enqueue_scripts(): void {
        self::include_child_first('/static.php');
    }

    public static function _action_admin_enqueue_scripts($hook): void {
        self::include_child_first('/static-admin.php');
    }

    /**
     * @internal
     */
    public static function _action_init(): void {
        self::include_child_first('/menus.php');
        self::include_child_first('/post-types.php');
        //self::include_child_first('/optimization.php');
    }

    /**
     * @internal
     */
    public static function _action_widgets_init(): void {
        $paths = [];

        if (is_child_theme()) $paths[] = self::get_child_path('/widgets');
        $paths[] = self::get_parent_path('/widgets');

        $included_widgets = [];

        foreach ($paths as $path) {
            if (!is_dir($path)) continue;
            $dirs = glob($path . '/*', GLOB_ONLYDIR);
            if (!$dirs) continue;

            foreach ($dirs as $dir) {
                $dirname = basename($dir);
                if (!empty($included_widgets[$dirname])) continue; // child overrides parent
                $included_widgets[$dirname] = true;

                $widget_file = $dir . '/class-widget-' . $dirname . '.php';
                if (!file_exists($widget_file)) continue;

                include $widget_file;

                $widget_class = 'Widget_' . self::dirname_to_classname($dirname);
                if (class_exists($widget_class)) register_widget($widget_class);
            }
        }
    }
}

Theme_Includes::init();