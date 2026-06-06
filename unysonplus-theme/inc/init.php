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
        //self::include_child_first('/post-types.php');
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