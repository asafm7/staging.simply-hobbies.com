<?php
/**
 * Functions.php.
 *
 * @author   WooThemes
 *
 * @since    1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/*
 * functions.php
 * Add PHP snippets here
 */

function filemtime_ver($file)
{
    if (wp_get_environment_type() === 'staging') {
        return null;
    }

    return filemtime(plugin_dir_path(__FILE__) . $file);
}

//
// HACK: [-2-] Remove dashboard widgets

add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

function remove_dashboard_widgets()
{
    global $wp_meta_boxes;

    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'], $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'], $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'], $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'], $wp_meta_boxes['dashboard']['normal']['core']['jetpack_summary_widget']);

    remove_meta_box('yith_dashboard_products_news', 'dashboard', 'normal');
    remove_meta_box('yith_dashboard_blog_news', 'dashboard', 'normal');
    remove_meta_box('woocommerce_dashboard_status', 'dashboard', 'normal');
    remove_meta_box('wsal', 'dashboard', 'normal');
    remove_meta_box('searchwp_statistics', 'dashboard', 'normal');
}

//
// HACK: [-2-] Filter entire HTML
// #blog

add_action('wp_head', 'buffer_start');

function buffer_start()
{
    if (!wp_doing_ajax()) {
        ob_start('callback');
    }
}

add_action('wp_footer', 'buffer_end');

function buffer_end()
{
    if (!wp_doing_ajax()) {
        ob_end_flush();
    }
}

function callback($buffer)
{
    if (!is_front_page()) {
        // HACK: [-2-] Defer YouTube videos
        $buffer = str_replace(' src="https://www.youtube.com/embed/', ' src="" data-src="https://www.youtube.com/embed/', $buffer);
    }

    return $buffer;
}

//
// HACK: [-9-] Disable WooCommerce Admin

add_filter('woocommerce_admin_disabled', '__return_true');

//
// HACK: [-2-] Dequeue Styles

add_action('wp_print_styles', 'dequeue_styles');

function dequeue_styles()
{
    wp_dequeue_style('custom-css');
    wp_deregister_style('custom-css');

    wp_dequeue_style('storefront-icons');
    wp_deregister_style('storefront-icons');

    wp_dequeue_style('storefront-fonts');
    wp_deregister_style('storefront-fonts');

    wp_dequeue_style('jquery-swiper');
    wp_deregister_style('jquery-swiper');

    wp_dequeue_style('photoswipe');
    wp_deregister_style('photoswipe');

    wp_dequeue_style('photoswipe-default-skin');
    wp_deregister_style('photoswipe-default-skin');

    wp_dequeue_style('venobox_style');
    wp_deregister_style('venobox_style');

    wp_dequeue_style('woocommerce_prettyPhoto_css');
    wp_deregister_style('woocommerce_prettyPhoto_css');

    wp_dequeue_style('ywcfav_owl_carousel_style');
    wp_deregister_style('ywcfav_owl_carousel_style');

    wp_dequeue_style('videojs');
    wp_deregister_style('videojs');

    if (is_product() || is_front_page() || is_archive()) {
        wp_dequeue_style('wc-block-style');
        wp_deregister_style('wc-block-style');

        wp_dequeue_style('wp-block-library');
        wp_deregister_style('wp-block-library');

        wp_dequeue_style('wp-block-library-theme');
        wp_deregister_style('wp-block-library-theme');

        wp_dequeue_style('select2');
        wp_deregister_style('select2');

        wp_dequeue_style('storefront-gutenberg-blocks');
        wp_deregister_style('storefront-gutenberg-blocks');
    }
}

//
// HACK: [-2-] Enqueue and Dequeue Scripts

add_action('wp_enqueue_scripts', 'dequeue_enqueue_scripts', 9999);

function dequeue_enqueue_scripts()
{
    global $storefront_version;

    wp_dequeue_script('soundcloud');
    wp_deregister_script('soundcloud');

    wp_dequeue_script('custom-js');
    wp_deregister_script('custom-js');

    wp_dequeue_script('jquery-swiper');
    wp_deregister_script('jquery-swiper');

    wp_dequeue_script('photoswipe');
    wp_deregister_script('photoswipe');

    wp_dequeue_script('photoswipe-ui-default');
    wp_deregister_script('photoswipe-ui-default');

    wp_dequeue_script('storefront-header-cart');
    wp_deregister_script('storefront-header-cart');

    wp_dequeue_script('wc-add-to-cart');
    wp_deregister_script('wc-add-to-cart');

    wp_dequeue_script('wc-cart-fragments');
    wp_deregister_script('wc-cart-fragments');

    wp_dequeue_script('venobox_api');
    wp_deregister_script('venobox_api');

    wp_dequeue_script('prettyPhoto');
    wp_deregister_script('prettyPhoto');

    wp_dequeue_script('prettyPhoto-init');
    wp_deregister_script('prettyPhoto-init');

    //wp_dequeue_script('wp-components');
    //wp_deregister_script('wp-components');

    wp_dequeue_script('ywcfav_owl_carousel');
    wp_deregister_script('ywcfav_owl_carousel');

    wp_dequeue_script('videojs');
    wp_deregister_script('videojs');

    wp_dequeue_script('ywcfav_content_manager');
    wp_deregister_script('ywcfav_content_manager');

    wp_dequeue_script('ywcfav_slider');
    wp_deregister_script('ywcfav_slider');

    //wp_dequeue_script('wp-rich-text');
    //wp_deregister_script('wp-rich-text');

    wp_dequeue_style('storefront-woocommerce-style');
    wp_deregister_style('storefront-woocommerce-style');

    wp_enqueue_style('storefront-woocommerce-style', get_template_directory_uri() . '/assets/css/woocommerce/woocommerce.css', [], $storefront_version);

    //wp_enqueue_style('sh-custom-style', plugins_url('/style.css', __FILE__), [], filemtime_ver('style.css'));
    //wp_enqueue_script('sh-custom-script', plugins_url('/custom.js', __FILE__), [], filemtime_ver('custom.js'));

    wp_enqueue_style('sh-custom-style', plugins_url('/style.css', __FILE__), [], null);
    wp_enqueue_script('sh-custom-script', plugins_url('/custom.js', __FILE__), [], null);

    wp_localize_script('sh-custom-script', 'customJs', ['ajaxurl' => admin_url('admin-ajax.php')]);
}

add_action('admin_enqueue_scripts', 'enqueue_admin_scripts', 999);

function enqueue_admin_scripts()
{
    wp_register_script('custom-admin-script', plugins_url('/custom-admin.js', __FILE__), ['jquery', 'jquery-ui-dialog'], null, true);

    wp_enqueue_script('custom-admin-script');
}

//
// HACK: [-2-] Remove theme support

remove_theme_support('wp-block-styles');
remove_theme_support('wc-product-gallery-zoom');
remove_theme_support('wc-product-gallery-lightbox');
remove_theme_support('wc-product-gallery-slider');

//
// HACK: [-2-] Disable TI WooCommerce Wishlist cart fragments dependency

add_filter('tinvwl_wc_cart_fragments_refresh', '__return_false');
add_filter('tinvwl_wc_cart_fragments_enabled', '__return_false');

//
// HACK: [-2-] Don't implode Jetpack CSS
// https://github.com/Automattic/jetpack/issues/16494#issuecomment-659175470

add_filter('jetpack_implode_frontend_css', '__return_false', 99);

//
// HACK: [-2-] Import AMP Gist

add_action('wp_head', 'import_amp_gist');

function import_amp_gist()
{
    if (is_amp_endpoint()) {
        ?>

<script async custom-element="amp-gist" src="https://cdn.ampproject.org/v0/amp-gist-0.1.js"></script>

<?php
    }
}

//
// HACK: [-9-] Bugsnag browser tracking

add_action('wp_head', 'load_bugsnag_browser_tracking');

function load_bugsnag_browser_tracking()
{
    if (wp_get_environment_type() === 'production') {
        ?>

<script src="//d2wy8f7a9ursnm.cloudfront.net/v7/bugsnag.min.js"></script>

<script>
    Bugsnag.start('<?php echo BUGSNAG_NOTIFIER_API_KEY; ?>');
</script>

<?php
        global $bugsnagWordpress;

        global $storefront_version;

        $bugsnagWordpress->setAppVersion($storefront_version);
    }
}

//add_action('wp_head', 'hide_admin_bar');

function hide_admin_bar()
{
    if (wp_get_environment_type() === 'production') {
        add_filter('show_admin_bar', '__return_false');
    }
}

//
// HACK: [-2-] Preload key requests

add_action('wp_head', 'preload_key_requests');

function preload_key_requests()
{
    $filemtime_ver = filemtime_ver('custom.js'); ?>

<!--
<link rel="preload" href="<?php echo plugins_url('/custom.js', __FILE__) . '?ver=' . $filemtime_ver; ?>" as="script">
-->

<link rel="preload" href="<?php echo plugins_url('/custom.js', __FILE__) ?>" as="script">

<?php

if (is_product()) {
    ?>

<link rel="preload" href="/wp-content/plugins/woocommerce/assets/fonts/WooCommerce.woff" as="font" type="font/woff" crossorigin>

<?php
}

    if (wp_script_is('addtoany')) {
        ?>

<link rel="preload" href="/wp-content/plugins/add-to-any/addtoany.min.js?ver=1.1" as="script">

<?php
    }
}

//
// HACK: [-2-] Defer non-critical CSS

add_filter('style_loader_tag', 'defer_non_critical_css', 10, 4);

function defer_non_critical_css($html, $handle, $src, $media)
{
    if ('sh-custom-style' === $handle) {
        $html = preg_replace("/ rel=['\"]stylesheet['\"]/", ' rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"', $html);
    }

    return $html;
}

//
// HACK: [-2-] Maybe disable tracking

add_action('wp_head', 'maybe_disable_tracking');

function maybe_disable_tracking()
{
    if (current_user_can('edit_others_pages') || wp_get_environment_type() === 'staging') {
        // NOTE: Goolge Analytics
        if (class_exists('WC_Google_Analytics_Pro_Integration')) {
            add_filter('wc_google_analytics_pro_do_not_track', '__return_true', 100);
        }

        // NOTE: Mixpanel
        if (class_exists('WC_Mixpanel_Integration')) {
            add_filter('wc_mixpanel_disable_tracking', '__return_true', 100);

            $mixpanel_integration = new WC_Mixpanel_Integration();
            $mixpanel_integration->disable_tracking();
        }
    }
}

//
// HACK: [-2-] Link Google Fonts

add_action('wp_head', 'link_google_fonts');

function link_google_fonts()
{
    if (is_singular()) {
        ?>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons|Material+Icons+Outlined&display=swap" rel="stylesheet">

<?php
    } else {
        ?>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons&display=swap" rel="stylesheet">

<?php
    } ?>

<link href="https://fonts.googleapis.com/css2?family=Spartan&display=swap" rel="stylesheet">

<?php
}

//
// HACK: [-3-] Change JS tracker options for the create method

add_filter('googlesitekit_gtag_opt', function ($gtag_opt) {
    $gtag_opt['site_speed_sample_rate'] = 100;
    return $gtag_opt;
}, 10, 1);

//
// HACK: [-2-] FullStory

add_action('wp_head', 'fullstory');

function fullstory()
{
    if (!current_user_can('edit_others_pages') && wp_get_environment_type() === 'production') {
        ?>

<script>
    window['_fs_debug'] = false;
    window['_fs_host'] = 'fullstory.com';
    window['_fs_script'] = 'edge.fullstory.com/s/fs.js';
    window['_fs_org'] = 'T6ZFP';
    window['_fs_namespace'] = 'FS';
    (function(m, n, e, t, l, o, g, y) {
        if (e in m) {
            if (m.console && m.console.log) {
                m.console.log('FullStory namespace conflict. Please set window["_fs_namespace"].');
            }
            return;
        }
        g = m[e] = function(a, b, s) {
            g.q ? g.q.push([a, b, s]) : g._api(a, b, s);
        };
        g.q = [];
        o = n.createElement(t);
        o.async = 1;
        o.crossOrigin = 'anonymous';
        o.src = 'https://' + _fs_script;
        y = n.getElementsByTagName(t)[0];
        y.parentNode.insertBefore(o, y);
        g.identify = function(i, v, s) {
            g(l, {
                uid: i
            }, s);
            if (v) g(l, v, s)
        };
        g.setUserVars = function(v, s) {
            g(l, v, s)
        };
        g.event = function(i, v, s) {
            g('event', {
                n: i,
                p: v
            }, s)
        };
        g.anonymize = function() {
            g.identify(!!0)
        };
        g.shutdown = function() {
            g("rec", !1)
        };
        g.restart = function() {
            g("rec", !0)
        };
        g.log = function(a, b) {
            g("log", [a, b])
        };
        g.consent = function(a) {
            g("consent", !arguments.length || a)
        };
        g.identifyAccount = function(i, v) {
            o = 'account';
            v = v || {};
            v.acctId = i;
            g(o, v)
        };
        g.clearUserCookie = function() {};
        g.setVars = function(n, p) {
            g('setVars', [n, p]);
        };
        g._w = {};
        y = 'XMLHttpRequest';
        g._w[y] = m[y];
        y = 'fetch';
        g._w[y] = m[y];
        if (m[y]) m[y] = function() {
            return g._w[y].apply(this, arguments)
        };
        g._v = "1.3.0";
    })(window, document, window['_fs_namespace'], 'script', 'user');

    // NOTE: Identify Your Users & Record Custom Data
    <?php

    if (is_user_logged_in()) {
        ?>
    // This is an example script - don't forget to change it!
    FS.identify('<?php echo wp_get_current_user()->user_email; ?>', {
        displayName: '<?php echo wp_get_current_user()->user_email; ?>',
        email: '<?php echo wp_get_current_user()->user_email; ?>',
        // TODO: Add your own custom user variables here, details at
        // https://help.fullstory.com/hc/en-us/articles/360020623294-FS-setUserVars-Recording-custom-user-data
        //reviewsWritten_int: 14
    });
    <?php
    } ?>
</script>

<?php
    }
}

//
// HACK: [-2-] Remove actions

add_action('wp_head', 'remove_actions_and_filters');

function remove_actions_and_filters()
{
    remove_action('storefront_header', 'storefront_header_container', 0);
    remove_action('storefront_header', 'storefront_secondary_navigation', 30);
    remove_action('storefront_header', 'storefront_product_search', 40);
    remove_action('storefront_header', 'storefront_header_container_close', 41);
    remove_action('storefront_header', 'storefront_primary_navigation_wrapper', 42);
    remove_action('storefront_header', 'storefront_header_cart', 60);
    remove_action('storefront_header', 'storefront_primary_navigation_wrapper_close', 68);

    remove_action('storefront_content_top', 'storefront_shop_messages', 15);

    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);

    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);

    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
    remove_action('woocommerce_before_shop_loop', 'storefront_woocommerce_pagination', 30);

    remove_action('woocommerce_after_shop_loop', 'woocommerce_result_count', 20);

    remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

    remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
    remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);

    remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);
    remove_action('woocommerce_archive_description', 'woocommerce_product_archive_description', 10);

    if (!has_term('essentials', 'product_tag')) {
        remove_action('woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30);
    }

    if (has_term('polls', 'product_tag')) {
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
        remove_action('woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20);
    }
}

add_action('woocommerce_single_product_summary', 'woocommerce_show_product_images', 1);

add_action('storefront_header', 'col_full_open', 0);
add_action('storefront_header', 'col_full_close', 100);

add_action('woocommerce_single_product_summary', 'open_single_product_title_and_excerpt_container', 2);
add_action('woocommerce_single_product_summary', 'close_single_product_title_and_excerpt_container', 21);

function open_single_product_title_and_excerpt_container()
{
    ?>

<div class="title-and-excerpt-container">

    <?php
}

function close_single_product_title_and_excerpt_container()
{
    ?>

</div>

<?php
}

//
// HACK: [-2-] Disable purchase functionality

add_filter('woocommerce_is_purchasable', '__return_false');
add_filter('woocommerce_add_to_cart_validation', '__return_false');

//
// HACK: [-2-] Relocate Storefront secondary navigation

add_action('storefront_header', 'storefront_secondary_navigation', 60);

//
// HACK: [-3-] Add the content to single product ('woocommerce_template_single_excerpt' removed in 'remove_actions_and_filters')

add_action('woocommerce_single_product_summary', 'add_the_content_to_single_product', 20);

function add_the_content_to_single_product()
{
    the_content();
}

//
// HACK: [-2-] Relocate Storefront shop messages

add_action('storefront_before_content', 'col_full_open', 4);
add_action('storefront_before_content', 'storefront_shop_messages', 5);
add_action('storefront_before_content', 'col_full_close', 6);

function col_full_open()
{
    ?>

<div class="col-full">

    <?php
}

function col_full_close()
{
    ?>

</div>

<?php
}

//
// HACK: [-3-] Add global search form to header (instead of just for products)

add_action('storefront_header', 'add_site_search', 55);

function add_site_search()
{
    ?>

<div class="site-search">
    <?php get_search_form(); ?>
    <div class="swpparentel"></div> <!-- SearchWP live search results container ('parent_el' config) -->
</div>

<label class="desktop-search-toggle-label" for="desktop-search-toggle"><span class="material-icons">
        search
    </span>
</label>

<?php
}

//
// HACK: [-3-] Add #id to search form (to enable external submit button)

add_filter('get_search_form', 'add_id_to_search_form');

function add_id_to_search_form($form)
{
    $form = str_replace('class="search-form"', 'id="searchform" class="search-form"', $form);

    return $form;
}

//
// HACK: [-2-] Override Storefront's native storefront_handheld_footer_bar_search()

function storefront_handheld_footer_bar_search()
{
    ?>

<label class="mobile-search-toggle-label" for="mobile-search-toggle"></label>

<?php
}

//
// HACK: [-3-] Change SearchWP live search configs

add_filter('searchwp_live_search_configs', 'change_searchwp_live_search_configs');

function change_searchwp_live_search_configs($configs)
{
    // override some defaults
    $configs['default'] = [
        'engine' => 'default',
        'input'  => [
            'delay'     => 400,
            'min_chars' => 3,
        ],
        'parent_el' => '.swpparentel',
        'results'   => [
            'position' => 'bottom',
            'width'    => 'auto',
            'offset'   => [
                'x' => 0,
                'y' => 5,
            ],
        ],
        'spinner' => [ // Powered by http://spin.js.org/
            'lines'     => 10,
            'length'    => 8,
            'width'     => 4,
            'radius'    => 8,
            'scale'     => 1,
            'corners'   => 1,
            'color'     => '#000',
            'fadeColor' => 'transparent',
            'speed'     => 1,
            'rotate'    => 0,
            'animation' => 'searchwp-spinner-line-fade-quick',
            'direction' => 1,
            'zIndex'    => 2e9,
            'className' => 'spinner',
            'top'       => '50%',
            'left'      => '50%',
            'shadow'    => '0 0 1px transparent',
            'position'  => 'absolute',
        ],
    ];
    // add an additional config called 'my_config'
    $configs['my_config'] = [
        'engine' => 'supplemental',
        'input'  => [
            'delay'     => 300,
            'min_chars' => 2,
        ],
        'results' => [
            'position' => 'top',
            'width'    => 'css',
            'offset'   => [
                'x' => 0,
                'y' => 0,
            ],
        ],
        'spinner' => [ // Powered by http://spin.js.org/
            'lines'     => 13,
            'length'    => 38,
            'width'     => 17,
            'radius'    => 45,
            'scale'     => 1,
            'corners'   => 1,
            'color'     => '#ffffff',
            'fadeColor' => 'transparent',
            'speed'     => 1,
            'rotate'    => 0,
            'animation' => 'searchwp-spinner-line-fade-quick',
            'direction' => 1,
            'zIndex'    => 2e9,
            'className' => 'spinner',
            'top'       => '50%',
            'left'      => '50%',
            'shadow'    => '0 0 1px transparent',
            'position'  => 'absolute',
        ],
    ];

    return $configs;
}

//
// HACK: [-3-] Change SearchWP live search posts per page

add_filter('searchwp_live_search_posts_per_page', 'change_searchwp_live_search_posts_per_page');

function change_searchwp_live_search_posts_per_page()
{
    return 20;
}

//
// HACK: [-2-] Remove Storefront breadcrumbs

add_action('init', 'wc_remove_storefront_breadcrumbs');

function wc_remove_storefront_breadcrumbs()
{
    remove_action('storefront_before_content', 'woocommerce_breadcrumb', 10);
}

//
// HACK: [-3-] Add Yoast breadcrumbs

add_action('storefront_content_top', 'add_yoast_seo_breadcrumbs');

function add_yoast_seo_breadcrumbs()
{
    if (function_exists('yoast_breadcrumb')) {
        if (!is_front_page()) {
            yoast_breadcrumb('<p id="breadcrumbs" class="yoast-breadcrumb">', '</p>');
        }
    }
}

//
// HACK: [-2-] Customize catalog sorting

add_filter('woocommerce_get_catalog_ordering_args', 'custom_woocommerce_get_catalog_ordering_args');

function custom_woocommerce_get_catalog_ordering_args($args)
{
    if (!is_search()) {
        $orderby_value = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));

        if ('view_count' === $orderby_value) {
            $args['orderby']  = ['meta_value_num' => 'DESC'];
            $args['meta_key'] = 'view_count';

            return $args;
        }

        if (isset($_GET['shuffle'])) {
            $args['orderby']  = ['meta_value_num' => 'DESC'];
            $args['meta_key'] = 'custom_menu_order';

            return $args;
        }
    }

    return $args;
}

add_filter('woocommerce_default_catalog_orderby_options', 'custom_woocommerce_catalog_orderby');
add_filter('woocommerce_catalog_orderby', 'custom_woocommerce_catalog_orderby');

function custom_woocommerce_catalog_orderby($sortby)
{
    unset($sortby['popularity'] , $sortby['price'] , $sortby['price-desc']);

    if (is_front_page() || is_product_tag('hobbies')) {
        unset($sortby['rating']);
    }

    if (!is_search()) {
        $popularity = ['view_count' => __('Sort by popularity', 'theme-customisations')];

        $sortby += $popularity;
    }

    return $sortby;
}

//
// HACK: [-3-] Change Jetpack infinite scroll allowed_vars
// TODO: Check if needed

add_filter('infinite_scroll_allowed_vars', 'change_jetpack_infinite_scroll_allowed_vars', 100, 2);

function change_jetpack_infinite_scroll_allowed_vars($allowed_vars, $query_args)
{
    $allowed_vars[] = 'meta_key';

    return $allowed_vars;
}

//
// HACK: [-3-] Change Jetpack infinite scroll query args

add_filter('infinite_scroll_query_args', 'change_jetpack_infinite_scroll_query_args', 100);

function change_jetpack_infinite_scroll_query_args($args)
{
    $meta_key = isset($_REQUEST['query_args']['meta_key']) ? $_REQUEST['query_args']['meta_key'] : '';

    if (!empty($meta_key)) {
        if ('view_count' === $meta_key) {
            $args['meta_key'] = 'view_count';
            $args['orderby']  = 'meta_value_num';
        } elseif ('custom_menu_order' === $meta_key) {
            $args['meta_key'] = 'custom_menu_order';
            $args['orderby']  = 'meta_value_num';
        }
    }

    return $args;
}

//
// HACK: [-2-] Support view count infinite scroll

//add_action('pre_get_posts', 'support_view_count_infinite_scroll');

function support_view_count_infinite_scroll($query)
{
    if (is_ajax() && $query->is_main_query()) {
        $meta_key = isset($query->query['meta_key']) ? $query->query['meta_key'] : '';

        if (!empty($meta_key)) {
            if ('view_count' === $meta_key) {
                $query->set('meta_key', 'view_count');
                $query->set('order', 'DESC');

                return;
            }

            if ('custom_menu_order' === $meta_key) {
                $query->set('meta_key', 'custom_menu_order');
                $query->set('order', 'DESC');

                return;
            }
        }
    }
}

//
// HACK: [-2-] Set hobbies tag archive as front page
// TODO: Make sure mimics the best way the hobbies tag archive

add_action('woocommerce_product_query', 'query_hobbies_tag_on_homepage');

function query_hobbies_tag_on_homepage($q)
{
    if (is_front_page() && $q->is_main_query()) {
        $q->set('product_tag', ['hobbies']);
    }
}

//
// HACK: [-3-] Add data to sidebar

add_action('dynamic_sidebar_before', 'add_data_before_sidebar');

function add_data_before_sidebar($index)
{
    ?>

<?php

    $limit = 6; // number of posts

    // NOTE: product page
    if ('sidebar-1' === $index && is_product()) {
        global $product;

        $product_categories              = get_product_categories('objects');
        $product_categories_slugs        = get_product_categories('slugs');
        $product_categories_slugs_string = implode(',', $product_categories_slugs);
        // NOTE: Hobby page
        if (has_term('hobbies', 'product_tag')) {
            ?>

<div class="widget widget_text" id="hobbys-essentials" tabindex="-1"><span class="gamma widget-title">Hobby's Essentials</span>

    <?php
    echo do_shortcode("[products limit='{$limit}' category='{$product_categories_slugs_string}' columns='1' orderby='rand' tag='essentials']");

            contact_for_missing_essentials_link(); ?>
</div>

<?php
        // NOTE: Poll page
        } elseif (has_term('polls', 'product_tag')) {
            ?>

<div class="widget widget_text"><span class="gamma widget-title">Poll in...</span>

    <?php

              foreach ($product_categories as $key => $category) {
                  ?>

    <a href="/?product_cat=<?php echo $product_categories_slugs[$key]; ?>&product_tag=polls">»
        <?php echo $category['name']; ?></a>

    <?php
              } ?>
</div>

<?php

$cross_sell_ids = $product->get_cross_sell_ids();

            if ($cross_sell_ids) {
                $cross_sell_ids_string = implode(',', $cross_sell_ids); ?>
<div class="widget widget_text"><span class="gamma widget-title">Related Essentials</span>

    <?php
    echo do_shortcode("[products columns='1' tag='essentials' orderby='rand' ids='{$cross_sell_ids_string}']"); ?>
</div>

<div class="widget widget_text"><span class="gamma widget-title">Related Polls</span>
    <?php
/*
              if ($polls) {
                  echo do_shortcode("[products limit='{$limit}' columns='1' orderby='rand' ids='{$polls_ids}']");
              }

                if (count($polls) >= $limit) {
                    ?>

    <a href="/?product_cat=<?php echo $product_categories_slugs[$key]; ?>&product_tag=polls" title="All Hobby's Polls">All Hobby's Polls »</a>

    <?php
                } else {
                    contact_for_suggested_polls_link();
                }
*/
                ?>
</div>

<?php
            }
            // NOTE: Articles page
        } elseif (has_term('articles', 'product_tag')) {
            ?>

<div class="widget widget_text"><span class="gamma widget-title">Related hobbies...</span>

    <?php

              foreach ($product_categories as $key => $category) {
                  ?>

    <a href="/?product_cat=<?php echo $product_categories_slugs[$key]; ?>&product_tag=articles">»
        <?php echo $category['name']; ?></a>

    <?php
              } ?>
</div>

<?php

$cross_sell_ids = $product->get_cross_sell_ids();

            if ($cross_sell_ids) {
                $cross_sell_ids_string = implode(',', $cross_sell_ids); ?>
<div class="widget widget_text"><span class="gamma widget-title">Related Essentials</span>

    <?php echo do_shortcode("[products columns='1' tag='essentials' orderby='rand' ids='{$cross_sell_ids_string}']"); ?>
</div>

<div class="widget widget_text"><span class="gamma widget-title">Related Polls</span>

    <?php

              if ($articles) {
                  echo do_shortcode("[products limit='{$limit}' columns='1' orderby='rand' ids='{$polls_ids}']");
              }
                if (count($articles) >= $limit) {
                    ?>

    <a href="/?product_cat=<?php echo $product_categories_slugs[$key]; ?>&product_tag=articles" title="All Hobby's Articles">All Hobby's Articles »</a>

    <?php
                } else {
                    contact_for_suggested_articles_link();
                } ?>

</div>

<?php
            }
        }
        // NOTE: Essentials archive
    } elseif ('sidebar-1' === $index && 'essentials' === get_query_var('product_tag')) {
        if (!empty(get_query_var('product_cat'))) {
            $title = 'More Hobbies';
        } else {
            $title = 'Hobbies';
        } ?>

<div class="widget widget_text"><span class="gamma widget-title"><a href="/#primary"><?php echo $title; ?></a></span>

    <?php
        echo do_shortcode("[products limit='{$limit}' columns='1' orderby='rand' tag='hobbies']"); ?>

    <a href="/#primary">All Hobbies »</a>
</div>

<?php
    }
}

//
// HACK: [-2-] Exclude terms from single product pagination
// NOTE: Only include the same 'product' tag ('hobbies', 'essentials', etc.)
// FIXME: Not working if the displayed product is the last

add_filter('storefront_single_product_pagination_excluded_terms', 'exclude_terms_from_single_product_pagination');

function exclude_terms_from_single_product_pagination()
{
    $product_tags_ids = get_terms([
        'taxonomy'   => 'product_tag',
        'fields'     => 'ids',
        'hide_empty' => false,
    ]);

    foreach ($product_tags_ids as $key => $product_tag_id) {
        if (has_term($product_tag_id, 'product_tag')) {
            if (false !== ($key = array_search($product_tag_id, $product_tags_ids))) {
                unset($product_tags_ids[$key]);
            }
        }
    }

    $excluded_terms = implode(',', $product_tags_ids);

    return $excluded_terms;
}

//
// HACK: [-2-] Maybe limit product pagination to same category

add_filter('storefront_single_product_pagination_same_category', 'maybe_limit_product_pagination_to_same_category');

function maybe_limit_product_pagination_to_same_category()
{
    if (has_term('hobbies', 'product_tag')) {
        return false;
    }

    return true;
}

//
// HACK: [-2-] Count views

add_action('wp_head', 'count_views');

function count_views()
{
    if (!current_user_can('edit_others_pages') && wp_get_environment_type() === 'production' && is_singular()) {
        $count = (int) get_field('view_count');

        ++$count;

        update_field('view_count', $count);
    }
}

//
// HACK: [-3-] Add active filters titles CSS

add_action('wp_head', 'add_active_filters_titles_css');

function add_active_filters_titles_css()
{
    if (is_archive()) {
        $attributes = wc_get_attribute_taxonomies(); ?>

<style>
    <?php
        foreach ($attributes as $attribute) {
            ?>

    <?php echo ".widget_layered_nav_filters ul li.chosen-{$attribute->attribute_name}::before"; ?>
        {
        <?php echo "content: '{$attribute->attribute_label}';"; ?>
    }

    <?php echo ".widget_layered_nav_filters ul li.chosen-{$attribute->attribute_name} ~ li.chosen-{$attribute->attribute_name}::before"; ?>
        {
        display: none;
    }

    <?php
        } ?>
</style>

<?php
    }
}

//
// HACK: [-2-] Mark WP admin bar red

add_action('admin_notices', 'mark_wp_admin_bar_red', 0, 0);

function mark_wp_admin_bar_red()
{
    if (wp_get_environment_type() === 'production') {
        ?>

<style>
    #wpadminbar {
        background-color: #fe4c4c;
    }
</style>

<?php
    }
}

//
// HACK: [-2-] Support sorting shortcode products by custom meta field

add_filter('woocommerce_shortcode_products_query', 'support_sorting_shortcode_products_by_custom_meta_field');

function support_sorting_shortcode_products_by_custom_meta_field($args)
{
    $standard_array = ['menu_order', 'title', 'date', 'rand', 'id'];

    // NOTE: If $orderby value is not standard, set it as meta_key
    if (isset($args['orderby']) && !in_array($args['orderby'], $standard_array)) {
        $args['meta_key'] = $args['orderby'];
        $args['orderby']  = 'meta_value_num';
    }

    return $args;
}

//
// HACK: [-2-] Remove Storefront sticky single add to cart from hobbies

add_action('wp_head', 'remove_storefront_sticky_single_add_to_cart_from_hobbies');

function remove_storefront_sticky_single_add_to_cart_from_hobbies()
{
    if (has_term(['hobbies', 'polls', 'articles'], 'product_tag')) {
        remove_action('storefront_after_footer', 'storefront_sticky_single_add_to_cart', 999);
    }
}

//
// HACK: [-2-] Remove single product related products

remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

//
// HACK: [-3-] Add hobby's essentials after single hobby

add_action('woocommerce_after_single_product_summary', 'add_hobbys_essentials_after_single_product', 20);

function add_hobbys_essentials_after_single_product()
{
    $limit = 6; // number of posts

    // NOTE: product page
    if (is_product()) {
        // NOTE: Hobby page
        if (has_term('hobbies', 'product_tag')) {
            $product_categories_slugs        = get_product_categories('slugs');
            $product_categories_slugs_string = implode(',', $product_categories_slugs); ?>

<section class="hobbys-essentials" id="bottom-hobbys-essentials">
    <h2><a href="/?product_cat=<?php echo $product_categories_slugs[0]; ?>&product_tag=essentials" title="All Hobby's Essentials">Hobby's Essentials</a></h2>

    <?php

    echo do_shortcode("[products limit='{$limit}' category='{$product_categories_slugs_string}' columns='3' orderby='view_count' order='DESC' tag='essentials']");

            contact_for_missing_essentials_link(); // TODO: Make a generic "missing content link"??>
</section>

<?php
        }
    }
}

//
// HACK: [-3-] Add more hobbies after single hobby

add_action('woocommerce_after_single_product_summary', 'add_more_hobbies_after_single_hobby', 20);

function add_more_hobbies_after_single_hobby()
{
    global $product;

    if (has_term(['hobbies', 'essentials'], 'product_tag')) {
        ?>

<section class="more-hobbies">
    <h2><a href="/" title="More Hobbies">More Hobbies</a></h2>

    <?php

  echo do_shortcode("[products limit='3' columns='3' orderby='rand' tag='hobbies']"); ?>

    <a href="/#primary">All
        Hobbies »</a>
</section>

<?php
    }
}

//
// HACK: [-2-] Remove single product meta

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

//
// HACK: [-Z-] Add register to save wishlist link

//add_action('tinvwl_after_wishlist', 'add_register_to_save_wishlist_link');

function add_register_to_save_wishlist_link()
{
    if (!is_user_logged_in()) {
        ?>

<p>You may have created this list as a guest. If so, it might be lost when your browser's history refreshes. <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" title="Register or Log In">Register
        or log in</a> to make
    sure your list is saved.</p>

<?php
    }
}

//
// HACK: [-3-] Add contact for missing essential link
// TODO: Replace hard coded similarities?

add_action('woocommerce_after_shop_loop', 'add_contact_for_missing_essentials_link', 100);

function add_contact_for_missing_essentials_link()
{
    if ('essentials' === get_query_var('product_tag')) {
        contact_for_missing_essentials_link();
    }
}

function contact_for_missing_essentials_link()
{
    ?>

<p class="contact-link">Missing essentials? <a href="/contact" title="Contact Us">Contact us</a>.
</p>

<?php
}

function contact_for_suggested_polls_link()
{
    ?>

<p class="contact-link">Have a poll to suggest? <a href="/contact" title="Contact Us">Contact
        us</a>.</p>

<?php
}

function contact_for_suggested_articles_link()
{
    ?>

<p>Have an article to suggest? <a href="/contact" title="Contact Us">Contact us</a>.</p>

<?php
}

//
// HACK: [-3-] Add wishlist button to shop loop

add_action('woocommerce_after_shop_loop_item', 'add_wishlist_button_to_shop_loop', 97);

function add_wishlist_button_to_shop_loop()
{
    if (has_term(['hobbies'], 'product_tag')) {
        echo do_shortcode('[ti_wishlists_addtowishlist]');
    }
}

//
// HACK: [-3-] Add attribute tags

add_action('woocommerce_after_shop_loop_item', 'add_attribute_tags', 97);
add_action('woocommerce_single_product_summary', 'add_attribute_tags', 2);

function add_attribute_tags()
{
    global $product;

    if (has_term(['essentials'], 'product_tag')) {
        $essential_tags = $product->get_attribute('pa_essentials-tags');

        if (!empty($essential_tags)) {
            $essential_tags = explode(', ', $essential_tags); ?>

<div class="attribute-tags-container">
    <?php
foreach ($essential_tags as $essential_tag) {
                ?>

    <span class='attribute-tag <?php echo strtolower($essential_tag) ?>'><?php echo $essential_tag; ?></span>

    <?php
            } ?>
</div>

<?php
        }
    }
}

//
// HACK: [-3-] Add custom product tabs

add_filter('woocommerce_product_tabs', 'custom_product_tabs', 98);

function custom_product_tabs($tabs)
{
    unset($tabs['description']);

    if (has_term(['hobbies', 'essentials', 'polls'], 'product_tag')) {
        $tabs['useful_links'] = [
            'title'    => '<span>🔗</span> Useful Links',
            'priority' => 10,
            'callback' => 'useful_links_tab_content',
        ];
        $tabs['articles'] = [
            'title'    => '<span>— 📰</span> Articles',
            'priority' => 30,
            'callback' => 'articles_tab_content',
        ];
        $tabs['books'] = [
            'title'    => '<span>— 📚</span> Books',
            'priority' => 41,
            'callback' => 'books_tab_content',
        ];
        $tabs['apps'] = [
            'title'    => '<span>— 📱</span> Apps',
            'priority' => 42,
            'callback' => 'apps_tab_content',
        ];
        $tabs['films_and_tv'] = [
            'title'    => '<span>— 🎬</span> Films and TV',
            'priority' => 65,
            'callback' => 'films_and_tv_tab_content',
        ];
        $tabs['websites'] = [
            'title'    => '<span>— 🌐</span> Websites',
            'priority' => 70,
            'callback' => 'websites_tab_content',
        ];
        $tabs['videos'] = [
            'title'    => '<span>📼</span> Videos',
            'priority' => 80,
            'callback' => 'videos_tab_content',
        ];
    }

    if (has_term(['hobbies'], 'product_tag')) {
        $tabs['essentials'] = [
            'title'    => '<span>— ✔️</span> Essentials',
            'priority' => 40,
            'callback' => 'essentials_tab_content',
        ];
        $tabs['near_you'] = [
            'title'    => '<span>— 👫</span> Near You',
            'priority' => 30,
            'callback' => 'near_you_tab_content',
        ];
        $tabs['courses'] = [
            'title'    => '<span>— ⛳</span> Courses',
            'priority' => 50,
            'callback' => 'courses_tab_content',
        ];
        $tabs['podcasts'] = [
            'title'    => '<span>— 🎙️</span> Podcasts',
            'priority' => 20,
            'callback' => 'podcasts_tab_content',
        ];
        $tabs['blogs'] = [
            'title'    => '<span>— 👩‍💻</span> Blogs',
            'priority' => 60,
            'callback' => 'blogs_tab_content',
        ];
        $tabs['all_links'] = [
            'title'    => '<span></span> All Links',
            'priority' => 1000,
            'callback' => 'all_links_tab_content',
        ];
    }

    if (has_term(['hobbies', 'essentials', 'articles'], 'product_tag')) {
        $tabs['polls'] = [
            'title'    => '<span>📊</span> Polls',
            'priority' => 90,
            'callback' => 'polls_tab_content',
        ];
    }

    $tabs['questions']['title']              = '<span>❓</span>Questions & Answers';
    $tabs['additional_information']['title'] = '<span>❗</span> Additional Information';

    $tabs['reviews']['priority']                = 100;
    $tabs['questions']['priority']              = 110;
    $tabs['additional_information']['priority'] = 120;

    return $tabs;
}

function all_links_tab_content()
{
    $content_types = ['sh_podcasts', 'sh_articles', 'essentials', 'sh_courses', 'sh_blogs', 'sh_films_and_tv', 'sh_websites', 'sh_near_you_links', ];

    if (has_term('essentials', 'product_tag')) {
        $content_types = ['sh_articles', 'sh_courses', 'sh_podcasts', 'sh_blogs'];
    }

    echo_item_list('All Links', $content_types);
}

function useful_links_tab_content()
{
    ?>

<h2>Useful Links</h2>

<?php
}

function essentials_tab_content()
{
    ?>

<h2>Essentials</h2>

<?php
}

function articles_tab_content()
{
    ?>

<h2>Articles</h2>

<?php
}

function books_tab_content()
{
    ?>

<h2>Books</h2>

<?php
}

function apps_tab_content()
{
    ?>

<h2>Apps</h2>

<?php
}

function courses_tab_content()
{
    ?>

<h2>Courses</h2>

<?php
}

function near_you_tab_content()
{
    ?>

<h2>Near You</h2>

<?php
}

function podcasts_tab_content()
{
    ?>

<h2>Podcasts</h2>

<?php
}

function blogs_tab_content()
{
    ?>

<h2>Blogs</h2>

<?php
}

function films_and_tv_tab_content()
{
    ?>

<h2>Films and TV</h2>

<?php
}

function websites_tab_content()
{
    ?>

<h2>Websites</h2>

<?php
}

// TODO: Add ItemList https://schema.org/
function videos_tab_content()
{
    ?>

<h2>Videos</h2>

<?php

global $product;

    $videos = get_post_meta($product->get_id(), '_ywcfav_video', true);

    if ($videos) {
        ?>

<div class="videos-container">
    <?php
        foreach ($videos as $video) {
            echo wp_oembed_get($video['content']);
        } ?>
</div>

<?php
    }

    wc_print_notice(__('<a href="/contact/" title="Contact Us">Suggest videos »</a>', 'theme-customisations'), 'notice');
}

// TODO: Add ItemList https://schema.org/
function polls_tab_content()
{
    global $product;

    $polls = get_product_related_content('polls', -1, 'objects'); ?>
<h2>Polls</h2>

<?php

    if ($polls) {
        ?>
<ul>

    <?php
          if (has_term('hobbies', 'product_tag')) {
              foreach ($polls as $poll) {
                  ?>
    <li>📊 <a class="poll" href="<?php echo get_permalink($poll->get_id()); ?>"><?php echo get_the_title($poll->get_id()); ?></a>
    </li>

    <?php
              }
          } elseif (has_term(['essentials', 'articles'], 'product_tag')) {
              foreach ($polls as $poll) {
                  $cross_sell_ids = $poll->get_cross_sell_ids();
                  if (in_array($product->get_id(), $cross_sell_ids)) {
                      ?>
    <li>📊 <a class="poll" href="<?php echo get_permalink($poll->get_id()); ?>"><?php echo get_the_title($poll->get_id()); ?></a>
    </li>

    <?php
                  }
              }
          } ?>
</ul>
<?php
    }

    wc_print_notice(__('<a href="/contact/" title="Contact Us">Suggest polls »</a>', 'theme-customisations'), 'notice');
}

function echo_item_list($type_title, $content_types)
{
    global $product;

    $page_title = $product->get_title(); ?>
<div itemscope itemtype="http://schema.org/ItemList">

    <h2 itemprop="name"><span style="display: none;"><?php echo $page_title; ?></span> Useful Links</h2>

    <link itemprop="itemListOrder" href="https://schema.org/ItemListUnordered" />

    <?php
            ?>
    <ul>
        <?php
        foreach ($content_types as $content_type) {
            echo_content_type_list($content_type);
        } ?>
    </ul>

    <?php
        $suggest_text = strtolower($type_title);

    wc_print_notice(__("<a href='/contact/' title='Contact Us'>Suggest more links »</a>", 'theme-customisations'), 'notice'); ?>
</div>

<?php
}

//
// HACK: [-2-] Echo content type list

function echo_content_type_list($content_type)
{
    global $product;

    static $sh_articles;
    static $sh_courses;
    static $sh_podcasts;
    static $sh_blogs;
    static $sh_films_and_tv;
    static $sh_websites;
    static $sh_near_you_links;
    static $essentials;

    if (isset(${$content_type})) {
        echo ${$content_type};

        return;
    }

    if ($content_type === 'essentials') {
        $essentials_objects = get_product_related_content('essentials', -1, 'objects');

        if (!empty($essentials_objects)) {
            $title_echoed = false;

            foreach ($essentials_objects as $key => $essential_object) {
                $args = [];

                $args['url'] = $essential_object->get_product_url();

                $post_id = $essential_object->get_id();

                $broken_links_urls = get_broken_links_urls(null, null, $post_id);

                if (is_array($broken_links_urls) && in_array($args['url'], $broken_links_urls)) {
                    continue;
                }

                if ($title_echoed === false) {
                    ?>

<li class="section essentials">
    <h3 class="section-title">Essentials</h3>
    <ul>
        <?php
$title_echoed = true;
                }

                $args['classes'] = [$content_type];

                $essentials_tags = get_the_terms($essential_object->get_id(), 'pa_essentials-tags');

                if (!empty($essentials_tags)) {
                    $essentials_tags_classes = array_column($essentials_tags, 'slug');

                    $args['classes'] = array_merge($args['classes'], $essentials_tags_classes);
                }

                if (get_field('recommended', $post_id)) {
                    $args['classes'][] = 'recommended';
                }

                $app_store_html_badge = get_field('app_store_html_badge', $post_id);
                $app_store_html_badge = add_app_store_image($app_store_html_badge);

                $play_store_html_badge = get_field('play_store_html_badge', $post_id);
                $play_store_html_badge = add_play_store_image($play_store_html_badge);

                $args['site_name']        = get_field('site_name', $post_id);
                $args['site_title']       = get_field('site_title', $post_id);
                $args['site_description'] = get_field('site_description', $post_id);
                $args['author']           = get_field('author', $post_id);
                $args['brand']            = get_field('brand', $post_id);

                $args['product_id'] = $post_id;

                $args['helpful_vote_meta_key'] = 'helpful';

                // TODO: Switch back after all posts updated with this field
                //$args['helpful_count'] = get_field('helpful', $post_id)['yes'];
                if ($helpful_count = get_post_meta($post_id, 'helpful_yes', true)) {
                    $args['helpful_count'] = (int) $helpful_count[0];
                } else {
                    $args['helpful_count'] = 0;
                }

                if ($app_store_html_badge || $play_store_html_badge) {
                    $args['apps'] = "<div class='app-stores-badges-container'>$app_store_html_badge $play_store_html_badge</div>";
                }

                ${$content_type} .= get_content_type_list_item($args, $content_type, $key);
            }
        }
    } else {
        $content_type_object = get_field_object($content_type);

        if ($content_type_object) {
            $content_type_sub_field_name = $content_type_object['sub_fields'][0]['name'];

            if (have_rows($content_type)) {
                $post_id = $product->get_id();

                $broken_links_urls = get_broken_links_urls('acf_field', 'acf', $post_id);

                $title_echoed = false;
                while (have_rows($content_type)) {
                    the_row();

                    $args = [];

                    $content_item = get_row(true);
                    $content_item = reset($content_item);

                    $args['url'] = $content_item['url'];

                    if (is_array($broken_links_urls) && in_array($args['url'], $broken_links_urls)) {
                        continue;
                    }

                    $index = get_row_index();
                    $index--;

                    $args['classes'] = [$content_type];

                    $args['site_name']        = $content_item['site_name'];
                    $args['site_title']       = $content_item['site_title'];
                    $args['site_description'] = $content_item['site_description'];

                    $args['product_id'] = $post_id;

                    // TODO: Switch back after all posts updated with this field
                    //$args['helpful_count'] = $content_item['helpful']['yes'];
                    $args['helpful_vote_meta_key'] = "{$content_type}_{$index}_{$content_type_sub_field_name}_helpful";

                    if ($helpful_count = get_post_meta($args['product_id'], $args['helpful_vote_meta_key'] . '_yes', true)) {
                        $args['helpful_count'] = (int) $helpful_count[0];
                    } else {
                        $args['helpful_count'] = 0;
                    }

                    if (isset($content_item['recommended']) && $content_item['recommended']) {
                        $args['classes'][] = 'recommended';
                    }

                    if (isset($content_item['hide_from_useful_links']) && $content_item['hide_from_useful_links']) {
                        $args['classes'][] = 'useful-links-hidden';
                    }

                    if ($content_type === 'sh_podcasts') {
                        if ($rss_feed_url = $content_item['rss_feed_url']) {
                            $podcast_player = get_podcast_player($rss_feed_url);

                            if (is_wp_error($podcast_player)) { // NOTE: Skip if RSS link isn't valid
                                $product_name      = $product->get_name();
                                $product_permalink = $product->get_permalink();
                                $message           = $podcast_player->get_error_message();

                                wp_mail('asafm7@gmail.com', 'Podcast RSS issue', "Podcast RSS {$rss_feed_url} is not working in {$product_name}: {$product_permalink}. Message: {$message}");

                                continue;
                            }

                            $unique_id = wp_unique_id();

                            $toggle = "<input type='checkbox' class='toggle podcast-toggle-input' id='podcast-toggle-{$unique_id}'><label class='podcast-toggle-label' for='podcast-toggle-{$unique_id}'><span class='material-icons'>play_circle_filled</span></label>";

                            $args['podcast_player'] = $toggle . $podcast_player;
                        }
                    }

                    if ($title_echoed === false) {
                        $content_type_label = $content_type_object['label']; ?>
        <li class="section <?php echo $content_type ?>">
            <h3 class="section-title"><?php echo $content_type_label ?>
            </h3>
            <ul>

                <?php
 $title_echoed = true;
                    }

                    ${$content_type} .= get_content_type_list_item($args, $content_type, $index);
                }
            }
        }
    }

    if (!empty(${$content_type})) {
        echo ${$content_type}; ?>
            </ul>
        </li>

        <?php
    }
}

function add_play_store_image($html)
{
    $favicon_url = get_favicon_url('https://play.google.com/store');

    $html = str_replace(' data-src=', ' src=', $html);

    $html = str_replace(' src=', " loading='lazy' src='{$favicon_url}' data-src=", $html);

    return $html;
}

function add_app_store_image($html)
{
    $favicon_url = get_favicon_url('https://apps.apple.com');

    if (strpos($html, '<img')) {
        $html = str_replace(' data-src=', ' src=', $html);
        $html = str_replace(' src=', " loading='lazy' src='{$favicon_url}' data-src=", $html);
    } else {
        $html = str_replace('url', 'data-url', $html);
        $html = str_replace('</a>', "<img loading='lazy' src='{$favicon_url}' ></a>", $html);
    }

    return $html;
}

function get_content_type_list_item($args, $content_type, $index)
{
    $url            = (!empty($args['url']) ? $args['url'] : '');
    $apps           = (!empty($args['apps']) ? $args['apps'] : '');
    $podcast_player = (!empty($args['podcast_player']) ? $args['podcast_player'] : '');
    $classes        = (!empty($args['classes']) ? $args['classes'] : '');

    $domain = get_url_domain($url);

    $site_name  = (!empty($args['site_name']) ? "<span class='site_name'> {$args['site_name']} </span>" : '');
    $site_title = (!empty($args['site_title']) ? "<span class='site_title'> {$args['site_title']} </span>" : '');

    $author = (!empty($args['author']) ? "<span class='author'> {$args['author']}</span> -" : '');
    $brand  = (!empty($args['brand']) ? "<span class='brand'> {$args['brand']}</span> -" : '');

    $product_id            = $args['product_id'];
    $helpful_vote_meta_key = $args['helpful_vote_meta_key'];

    $helpful_count = $args['helpful_count'];

    if ($helpful_count) {
        $helpful_count_badge = "<span class='helpful_count_badge' title='{$helpful_count} found the link helpful. Visit it and come back to vote.'><span class='material-icons-outlined yes'>thumb_up</span><span class='count'>+{$helpful_count}</span></span>";
    } else {
        $helpful_count_badge = '';
    }

    if ($author || $brand) {
        $site_name = '';
    }

    if ($podcast_player) {
        $site_description = (!empty($args['site_description']) ? "<span class='site_description'><span class='text'> {$args['site_description']}. Click <span class='inline-podcast-toggle material-icons'>play_circle_filled</span> to listen to an episode</span><span class='site_description-hide material-icons'>cancel</span></span>" : '');

        $search  = ['….', '..', '. .', '!.', '?.', ' .'];
        $replace = ['…', '.', '.', '!', '?', '.'];

        $site_description = str_ireplace($search, $replace, $site_description);
    } else {
        $site_description = (!empty($args['site_description']) ? "<span class='site_description'><span class='text'> {$args['site_description']} </span><span class='site_description-hide material-icons'>cancel</span></span>" : '');
    }

    $site_description_toggle = (!empty($site_description) ? "<input type='checkbox' class='toggle site_description_toggle-input' id='{$content_type}-{$index}' name='site_description_toggle'><label class='site_description_toggle-label' for='{$content_type}-{$index}'></label>" : '');

    $favicon_url = get_favicon_url($args['url']);

    $classes = implode(' ', $classes);

    // TODO: Remove " . " operator
    return '<li itemprop="itemListElement" class="' . $classes . ' content_type_list_item" data-product_id="' . $product_id . '"  data-helpful_vote_meta_key="' . $helpful_vote_meta_key . '">' . $site_description_toggle . '<a href="' . $url . '" target="_blank" rel="noopener" class="link" title="Come back to vote if helpful."><img loading="lazy" width="24" height="24" src="' . $favicon_url . '" alt="" onerror="this.src=\'https://www.google.com/s2/favicons?domain=' . $domain . '\';"><span class="link_text">' . $author . $brand . $site_name . $site_title . $site_description . '</span>' . $helpful_count_badge . '</a>' . $apps . $podcast_player . '</li>';
}

//
// HACK: [-3-] Change reviews tab title

add_filter('woocommerce_product_tabs', 'change_reviews_tab_title', 500);

function change_reviews_tab_title($tabs)
{
    if (has_term('hobbies', 'product_tag')) {
        $tabs['reviews']['title'] = '<span>💡</span> Tips';
    } elseif (has_term('essentials', 'product_tag')) {
        $tabs['reviews']['title'] = '<span>⭐</span> Reviews & Tips';
    } elseif (has_term(['polls', 'articles'], 'product_tag')) {
        $tabs['reviews']['title'] = '<span>💬</span> Comments';
    }

    return $tabs;
}

//
// HACK: [-3-] Change reviews summary title

add_filter('ywar_reviews_summary_title', 'change_reviews_summary_title');

function change_reviews_summary_title($title)
{
    if (has_term('hobbies', 'product_tag')) {
        $title = 'Tips';
    } elseif (has_term('essentials', 'product_tag')) {
        $title = 'Reviews and Tips';
    } elseif (has_term('polls', 'product_tag')) {
        $title = 'Comments';
    }

    return $title;
}

//
// HACK: [-2-] Maybe enable review rating

add_filter('pre_option_woocommerce_enable_review_rating', 'maybe_enable_review_rating');

function maybe_enable_review_rating()
{
    if (has_term('essentials', 'product_tag')) {
        return 'yes';
    }

    return 'no';
}

//
// HACK: [-2-] Maybe require review rating

add_filter('pre_option_woocommerce_review_rating_required', 'maybe_require_review_rating');

function maybe_require_review_rating()
{
    if (has_term('hobbies', 'product_tag')) {
        return 'no';
    }

    return 'yes';
}

// TODO: Check if both needed

//
// HACK: [-2-] Remove count from YITH ywqa tab title

add_filter('yith_ywqa_tab_title', 'remove_count_from_yith_ywqa_tab_title');

function remove_count_from_yith_ywqa_tab_title($tab_title)
{
    $ywqa_tab_label = get_option('ywqa_tab_label', 'Questions & Answers');

    return $ywqa_tab_label;
}

//
// HACK: [-Z-] Add hobby's page link

//add_action('woocommerce_before_main_content', 'add_hobby_page_link', 99);

function add_hobby_page_link()
{
    $product_tag = get_query_var('product_tag');
    $product_cat = get_query_var('product_cat');

    if (('essentials' === $product_tag || 'polls' === $product_tag || 'articles' === $product_tag) && !empty($product_cat)) {
        $args = [
            'limit'    => 1,
            'category' => [$product_cat],
            'tag'      => ['hobbies'],
            'status'   => 'publish',
        ];

        $hobby = wc_get_products($args)[0];

        $hobby_link  = $hobby->get_permalink();
        $hobby_title = strtolower($hobby->get_title()); ?>

        <a href="<?php echo $hobby_link; ?>">« To <?php echo $hobby_title; ?> hobby
            page</a>

        <?php
    }
}

//
// HACK: [-3-] Change WooCommerce page title

add_filter('woocommerce_page_title', 'change_woocommerce_page_title');

function change_woocommerce_page_title($page_title)
{
    $product_tag = get_query_var('product_tag');
    $product_cat = get_query_var('product_cat');

    if ('essentials' === $product_tag && !empty($product_cat)) {
        return $page_title . ' Essentials';
    }

    if ('polls' === $product_tag && !empty($product_cat)) {
        return $page_title . ' Polls';
    }

    if ('articles' === $product_tag && !empty($product_cat)) {
        return $page_title . ' Articles';
    }

    $taxonomy = get_query_var('taxonomy');

    if (is_tax() && taxonomy_is_product_attribute($taxonomy)) {
        return $page_title . ' Hobbies';
    }

    return $page_title;
}

//
// HACK: [-2-] Customize handheld footer links

add_filter('storefront_handheld_footer_bar_links', 'customize_handheld_footer_links');

function customize_handheld_footer_links($links)
{
    $links = [
        'menu' => [
            'priority' => 1,
            'callback' => 'storefront_handheld_footer_bar_menu',
        ],
        'home' => [
            'priority' => 10,
            'callback' => 'storefront_handheld_footer_bar_home',
        ],
        'search' => [
            'priority' => 20,
            'callback' => 'storefront_handheld_footer_bar_search',
        ],
        'filter' => [
            'priority' => 30,
            'callback' => 'storefront_handheld_footer_bar_filter',
        ],
        'useful-links' => [
            'priority' => 40,
            'callback' => 'storefront_handheld_footer_bar_useful_links',
        ],
        'essentials' => [
            'priority' => 50,
            'callback' => 'storefront_handheld_footer_bar_essentials',
        ],
        'wishlist' => [
            'priority' => 60,
            'callback' => 'storefront_handheld_footer_bar_wishlist',
        ],
        'to-site' => [
            'priority' => 70,
            'callback' => 'storefront_handheld_footer_bar_to_site',
        ],
    ];

    if (is_front_page() || is_product_tag('hobbies')) {
        unset($links['essentials'], $links['useful-links']);
    }

    if (!is_archive() || is_search()) {
        unset($links['filter']);
    }

    if (!is_product()) {
        unset($links['essentials'], $links['useful-links'], $links['to-site'], $links['wishlist']);
    }

    if (has_term('essentials', 'product_tag')) {
        unset($links['essentials']);
    } else {
        unset($links['to-site']);
    }

    if (has_term(['polls', 'articles'], 'product_tag')) {
        unset($links['essentials'], $links['to-site'], $links['wishlist']);
    }

    return $links;
}

function storefront_handheld_footer_bar_wishlist()
{
    echo do_shortcode('[ti_wishlists_addtowishlist]');
}

function storefront_handheld_footer_bar_filter()
{
    $class = '';

    $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();

    if (count($_chosen_attributes) > 0) {
        $class = 'active-filter';
    } ?> <label class="<?php echo $class; ?>" for="mobile-filter-toggle">

            <span class="material-icons">
                filter_list
            </span>
        </label>

        <?php
}

function storefront_handheld_footer_bar_useful_links()
{
    ?>

        <a href="#tab-useful_links">
            <span class="material-icons">
                link
            </span>
            Useful Links
        </a>

        <?php
}

function storefront_handheld_footer_bar_essentials()
{
    ?>

        <a href="#bottom-hobbys-essentials"></a>

        <?php
}

function storefront_handheld_footer_bar_to_site()
{
    $app_store_html_badge  = get_field('app_store_html_badge');
    $play_store_html_badge = get_field('play_store_html_badge');

    if ($app_store_html_badge || $play_store_html_badge) {
        ?>
        <a href="#get-essential-links"></a>
        <?php
    } else {
        woocommerce_external_add_to_cart();
    }
}

function storefront_handheld_footer_bar_home()
{
    ?>

        <a href="/"></a>

        <?php
}

function storefront_handheld_footer_bar_menu()
{
    ?>

        <label for="mobile-menu-toggle"></label>

        <?php
}

//
// HACK: [-3-] Add toggles

add_action('storefront_before_header', 'add_toggles', 50);

function add_toggles()
{
    ?>

        <input type="checkbox" class='toggle' id="mobile-filter-toggle">
        <input type="checkbox" class='toggle' id="mobile-search-toggle">
        <input type="checkbox" class='toggle' id="mobile-menu-toggle">

        <input type="checkbox" class='toggle' id="desktop-search-toggle">

        <?php
}

// TODO: Check if is the best hook

add_action('template_redirect', 'template_redirect_actions');

function template_redirect_actions()
{
    // HACK: [-2-] Don't reload just last infinite scroll page
    $request_uri = $_SERVER['REQUEST_URI'];

    if (false !== strpos($request_uri, '/page/')) {
        $page_trimmed_request_uri = strstr($request_uri, '/page/', true);

        if (empty($page_trimmed_request_uri)) {
            $page_trimmed_request_uri = '/';
        }

        wp_redirect($page_trimmed_request_uri, 301);

        exit;
    }

    // HACK: [-2-] Randomize hobbies custom menu order on sidebar link click (not the native menu_order field)
    // NOTE: To enable random infinite scroll
    // TODO: Do it without redirecting?

    if (is_page('shuffle')) {
        $args = [
            'tag'    => ['hobbies'],
            'limit'  => -1,
            'return' => 'ids',
            'status' => 'publish',
        ];

        $hobbies = wc_get_products($args);

        shuffle($hobbies);

        foreach ($hobbies as $key => $hobby) {
            update_field('custom_menu_order', $key, $hobby);
        }

        wp_redirect('/?shuffle=' . time(), 307); // NOTE: 307 to prevent redirect caching

        exit;
    }
}

//
// HACK: [-3-] Add wishlist and share buttons to single product

add_action('woocommerce_share', 'add_wishlist_and_share_buttons_to_single_product');

function add_wishlist_and_share_buttons_to_single_product()
{
    if (has_term(['hobbies', 'essentials'], 'product_tag')) {
        echo '<div class="save-and-share-container">';

        echo do_shortcode('[ti_wishlists_addtowishlist]');
        echo do_shortcode('[addtoany]');

        echo '</div>';
    }
}

//
// HACK: [-3-] Change hobbies og:type to article

add_filter('wpseo_opengraph_type', 'change_hobbies_og_type_to_article', 100);

function change_hobbies_og_type_to_article($type)
{
    if (has_term(['hobbies', 'polls', 'articles'], 'product_tag')) {
        $type = 'article';
    }

    return $type;
}

//
// HACK: [-3-] Add contact for couldn't find

add_action('storefront_loop_after', 'add_contact_for_couldnt_find');

function add_contact_for_couldnt_find()
{
    if (is_product_tag()) {
        wc_print_notice(__('Couldn\'t find what you were looking for? <a href="/contact/" title="Contact Us">Let us know!</a>', 'theme-customisations'), 'notice');
    }
}

//
// HACK: [-3-] Change structured data product

add_filter('woocommerce_structured_data_product', 'change_structured_data_product', 10, 2);

function change_structured_data_product($markup, $product)
{
    if (has_term(['hobbies', 'polls', 'articles'], 'product_tag')) {
        $markup['@type'] = 'Article';

        //unset($markup['sku']);
        //unset($markup['offers']);
    }

    return $markup;
}

//
// HACK: [-2-] Remove infinite scroll credit

add_filter('infinite_scroll_credit', 'remove_infinite_scroll_credit');

function remove_infinite_scroll_credit($credits)
{
    if ('' !== get_privacy_policy_url()) {
        $credits = get_the_privacy_policy_link() . '<span role="separator" aria-hidden="true"></span>';
    } else {
        $credits = '';
    }

    return $credits;
}

//
// HACK: [-3-] Add image title attr for tooltips
// #blog

add_filter('wp_get_attachment_image_attributes', 'add_image_title_attr', 10, 3);

function add_image_title_attr($attr, $attachment, $size)
{
    $attr += ['title' => $attachment->post_content];

    return $attr;
}

//
// HACK: [-3-] Add app-stores badges to single essential

add_action('woocommerce_before_add_to_cart_form', 'add_app_stores_badges');

function add_app_stores_badges()
{
    $app_store_html_badge  = add_app_store_image(get_field('app_store_html_badge'));
    $play_store_html_badge = add_play_store_image(get_field('play_store_html_badge'));

    if ($app_store_html_badge || $play_store_html_badge) {
        ?>

        <div class="get-essential-links-container" id="get-essential-links" tabindex="-1">
            <div class="app-stores-badges-container">

                <?php

        echo $app_store_html_badge;
        echo $play_store_html_badge; ?>

            </div>

            <?php
    }
}

add_action('woocommerce_after_add_to_cart_form', 'close_get_essential_links_container_tag');

function close_get_essential_links_container_tag()
{
    $app_store_html_badge  = get_field('app_store_html_badge');
    $play_store_html_badge = get_field('play_store_html_badge');

    if ($app_store_html_badge || $play_store_html_badge) {
        ?>

        </div>

        <?php
    }
}

//
// HACK: [-2-] Sort previous and next posts by menu_order (for storefront_woocommerce_pagination)
// #blog
// NOTE: https://gist.github.com/navnit-viradiya/243d8b2a6fb81d7b85b2705d9d4f525e

add_filter('get_previous_post_where', 'change_previous_post_where');

function change_previous_post_where()
{
    global $post, $wpdb;

    return $wpdb->prepare("WHERE p.menu_order < %s AND p.post_type = %s AND p.post_status = 'publish'", $post->menu_order, $post->post_type);
}

add_filter('get_next_post_where', 'change_next_post_where');

function change_next_post_where()
{
    global $post, $wpdb;

    return $wpdb->prepare("WHERE p.menu_order > %s AND p.post_type = %s AND p.post_status = 'publish'", $post->menu_order, $post->post_type);
}

add_filter('get_previous_post_sort', 'previous_post_sort_by_menu_order');

function previous_post_sort_by_menu_order($orderby)
{
    return 'ORDER BY p.menu_order DESC LIMIT 1';
}

add_filter('get_next_post_sort', 'next_post_sort_by_menu_order');

function next_post_sort_by_menu_order($orderby)
{
    return 'ORDER BY p.menu_order ASC LIMIT 1';
}

//
// HACK: [-2-] Get product related content

function get_product_related_content($type, $limit = 1, $return)
{
    static $product_essentials_objects; // TODO: Maybe use ${} to declare once?
    static $product_essentials_ids;

    static $product_polls_objects;
    static $product_polls_ids;

    static $product_articles_objects;
    static $product_articles_ids;

    static $product_categories;
    static $product_categories_slugs;

    if (isset(${"product_{$type}_{$return}"})) {
        return ${"product_{$type}_{$return}"};
    }

    if (!isset($product_objects)) {
        global $product;

        if (!isset($product_categories_slugs)) {
            $product_categories_slugs = get_product_categories('slugs');
        }

        $args = [
            'limit'    => -1, //NOTE: Maybe make more efficient?
            'orderby'  => 'rand',
            'category' => $product_categories_slugs,
            //'tag' => [$type],
            'exclude' => [$product->get_id()],
            'status'  => 'publish',
        ];

        $product_objects = wc_get_products($args);
    }

    $term_object = get_term_by('slug', $type, 'product_tag');
    $term_id     = $term_object->term_id;

    ${"product_{$type}_objects"} = array_filter($product_objects, function ($product) use ($term_id) {
        $tag_ids = $product->get_tag_ids();

        foreach ($tag_ids as $tag_id) {
            if ($tag_id === $term_id) {
                return true;
            }
        }

        return false;
    });

    // TODO: Maybe use switch
    if ('objects' === $return) {
        return ${"product_{$type}_objects"};
    }

    if ('ids' === $return) {
        ${"product_{$type}_ids"} = [];

        foreach (${"product_{$type}_objects"} as $content_object) {
            ${"product_{$type}_ids"}[] = $content_object->get_id();
        }

        return ${"product_{$type}_ids"};
    }

    if ('slugs' === $return) {
        ${"product_{$type}_slugs"} = [];

        foreach (${"product_{$type}_objects"} as $content_object) {
            ${"product_{$type}_slugs"}[] = $content_object->get_slug();
        }

        return ${"product_{$type}_slugs"};
    }
}

//
// HACK: [-2-] Get product categories

function get_product_categories($return)
{
    static $product_categories_slugs;
    static $product_categories_ids;
    static $product_categories_objects;

    if (isset(${"product_categories_{$return}"})) {
        return ${"product_categories_{$return}"};
    }

    if (!isset($product_categories_ids)) {
        global $product;

        $product_categories_ids = $product->get_category_ids();

        if (class_exists('WPSEO_Primary_Term')) {
            $primary_product_cat    = new WPSEO_Primary_Term('product_cat', $product->get_id());
            $primary_product_cat_id = $primary_product_cat->get_primary_term();

            if (false !== ($key = array_search($primary_product_cat_id, $product_categories_ids))) {
                unset($product_categories_ids[$key]);

                array_unshift($product_categories_ids, $primary_product_cat_id);
            }
        }
    }

    if ('ids' === $return) {
        return $product_categories_ids;
    }

    foreach ($product_categories_ids as $key => $id) {
        $product_categories_objects[] = get_term_by('id', $id, 'product_cat', 'ARRAY_A');
        $product_categories_slugs[]   = $product_categories_objects[$key]['slug'];
    }

    return ${"product_categories_{$return}"};
}

//
// HACK: [-2-] Helpful vote AJAX action

add_action('wp_ajax_helpful_vote', 'helpful_vote');
add_action('wp_ajax_nopriv_helpful_vote', 'helpful_vote');

// TODO: Create a version to logged-in users

function helpful_vote()
{
    $product_id = sanitize_text_field($_POST['product_id']);

    $is_essential = filter_var($_POST['is_essential'], FILTER_VALIDATE_BOOLEAN);

    if ($is_essential) {
        $helpful_meta_key     = 'helpful_yes';
        $not_helpful_meta_key = 'helpful_no';

        $vote_type = sanitize_text_field($_POST['vote_type']);

        $cookie_name = "sh_{$product_id}_helpful";
    } else {
        $helpful_vote_meta_key = sanitize_text_field($_POST['helpful_vote_meta_key']);

        $vote_type = sanitize_text_field($_POST['vote_type']);

        $helpful_meta_key     = "{$helpful_vote_meta_key}_yes";
        $not_helpful_meta_key = "{$helpful_vote_meta_key}_no";

        $cookie_name = "sh_{$product_id}_{$helpful_vote_meta_key}";
    }

    $cookie_value = isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : '';

    $current_helpful_count     = (int) get_post_meta($product_id, $helpful_meta_key, true);
    $current_not_helpful_count = (int) get_post_meta($product_id, $not_helpful_meta_key, true);

    $updated = '';

    if ($vote_type === 'thumb_down') {
        if ($cookie_value !== 'no') {
            $updated = update_post_meta($product_id, $not_helpful_meta_key, $current_not_helpful_count + 1);
        }

        if ($cookie_value === 'yes') {
            update_post_meta($product_id, $helpful_meta_key, $current_helpful_count - 1);
        }
    } elseif ($vote_type === 'thumb_up') {
        if ($cookie_value !== 'yes') {
            $updated = update_post_meta($product_id, $helpful_meta_key, $current_helpful_count + 1);
        }

        if ($cookie_value === 'no') {
            update_post_meta($product_id, $not_helpful_meta_key, $current_not_helpful_count - 1);
        }
    }

    wp_schedule_single_event(time() + 600, 'purge_relevant_urls_event', [$product_id]);

    if (wp_doing_ajax()) {
        $response['updated'] = $updated;

        $response['cookieName']  = $cookie_name;
        $response['cookieValue'] = ($vote_type === 'thumb_up') ? 'yes' : 'no';

        $response = json_encode($response);

        echo $response;

        die();
    }
}

//
// HACK: [-2-] Purge relevant URLs

add_action('purge_relevant_urls_event', 'purge_relevant_urls', 10, 1);

function purge_relevant_urls($product_id)
{
    $post_permalink = get_the_permalink($product_id);

    do_action('litespeed_purge_url', $post_permalink);

    $essential_related_hobbies_urls = get_essential_related_hobbies_urls($product_id);

    foreach ($essential_related_hobbies_urls as $url) {
        do_action('litespeed_purge_url', $url);
    }

    //$urls[] = $post_permalink;

    //$cloudflare_purge_result = cloudflare_purge_files_by_url($urls);

    $cloudflareHooks = new \CF\WordPress\Hooks();

    $cloudflareHooks->purgeCacheByRelevantURLs($product_id);
}

//
// HACK: [-2-] Wrong Link Ajax Action

add_action('wp_ajax_wrong_link', 'wrong_link');
add_action('wp_ajax_nopriv_wrong_link', 'wrong_link');

function wrong_link()
{
    $link_href = filter_var($_POST['link_href'], FILTER_VALIDATE_URL);

    $product_id = sanitize_text_field($_POST['product_id']);

    $product = wc_get_product($product_id);

    $product_name      = $product->get_name();
    $product_permalink = $product->get_permalink();

    if ($link_href) {
        wp_mail('asafm7@gmail.com', 'Wrong Link', "Wrong link on {$product_name} [{$product_permalink}]: {$link_href}");
    }

    if (wp_doing_ajax()) {
        $response[] = 'Success';
        $response   = json_encode($response);

        echo $response;

        die();
    }
}

//
// HACK: [-3-] Add product expert functionality

// NOTE: Subscribe as product expert AJAX action

add_action('wp_ajax_subscribe_product_expert', 'subscribe_user_as_product_expert');

function subscribe_user_as_product_expert($user = null)
{
    if (empty($user)) {
        $user_id = get_current_user_id();
    } else {
        $user_id = $user->ID;
    }

    $expert_product_slug = sanitize_text_field($_POST['expert_product_slug']);

    $user_products_expert = get_user_meta($user_id, 'products_expert', true);

    if (empty($user_products_expert)) {
        $user_products_expert = [];
    }

    if (false === ($key = array_search($expert_product_slug, $user_products_expert))) {
        $user_products_expert[] = $expert_product_slug;

        update_user_meta($user_id, 'products_expert', $user_products_expert);
    }

    if (wp_doing_ajax()) {
        $response = json_encode($response);

        echo $response;

        die();
    }
}

// NOTE: Unsubscribe as product expert AJAX action

add_action('wp_ajax_unsubscribe_product_expert', 'unsubscribe_user_as_product_expert');

function unsubscribe_user_as_product_expert($user = null)
{
    $user_id = get_current_user_id();

    $expert_product_slug = sanitize_text_field($_POST['expert_product_slug']);

    $user_products_expert = get_user_meta($user_id, 'products_expert', true);

    if (is_array($user_products_expert) && false !== ($key = array_search($expert_product_slug, $user_products_expert))) {
        unset($user_products_expert[$key]);

        update_user_meta($user_id, 'products_expert', $user_products_expert);
    }

    if (wp_doing_ajax()) {
        $response = json_encode($response);

        echo $response;

        die();
    }
}

// NOTE: Product expert link after single product summary

//add_action('woocommerce_after_single_product_summary', 'add_product_expert_link_after_single_product_summary', 0);
// TODO: If re-enabled, find a way to scroll to login form

function add_product_expert_link_after_single_product_summary()
{
    echo_product_expert_container();
}

add_action('yith_question_answer_before_question_list_section', 'add_product_expert_link_before_question_list_section');

function add_product_expert_link_before_question_list_section()
{
    echo_product_expert_container();
}

function echo_product_expert_container()
{
    ?>

        <div id="product-expert-container" class="product-expert-container">

            <?php
    if (is_user_logged_in()) {
        global $post;

        $expert_product_slug = $post->post_name;

        $user_id = get_current_user_id();

        $user_products_expert = get_user_meta($user_id, 'products_expert', true);

        // TODO: Duplicated in another function. Maybe make a global function
        if (!empty($user_products_expert) && false !== ($key = array_search($expert_product_slug, $user_products_expert))) {
            wc_print_notice(__('<span id="product-expert" class="unsubscribe-product-expert"><a href>Unsubscribe from new questions</a></span>', 'theme-customisations'), 'notice');
        } else {
            wc_print_notice(__('<span id="product-expert" class="subscribe-product-expert">Experienced? <a href>Subscribe to new questions</a> and help future visitors</span>', 'theme-customisations'), 'notice');
        }
    } else {
        wc_print_notice(__('<label class="toggle login-register-toggle-label" for="login-register-toggle">Experienced? <span class="clickable">Log in or
        register</span> to subscribe to new questions and help future visitors</label>', 'theme-customisations'), 'notice');

        static $form_exists;

        if (!isset($form_exists)) {
            ?>

            <input type="checkbox" class="toggle" name="login-register-toggle" id="login-register-toggle">

            <?php echo do_shortcode('[woocommerce_my_account]'); ?>

            <?php

            $form_exists = true;
        }
    } ?>
        </div>

        <?php
}

// NOTE: Subscribe product expert register/login fields

add_action('woocommerce_register_form_end', 'add_subscribe_product_expert_register_login_fields');
add_action('woocommerce_login_form_end', 'add_subscribe_product_expert_register_login_fields');

function add_subscribe_product_expert_register_login_fields()
{
    global $post;

    $expert_product_slug = $post->post_name; ?>

        <input type="hidden" name="redirect" value="<?php echo get_permalink() . '#product-expert-container'; ?>" />
        <input type="hidden" name="expert_product_slug" value="<?php echo $expert_product_slug; ?>" />

        <?php
}

// NOTE: Subscribe to questions after register

add_filter('woocommerce_registration_redirect', 'subscribe_to_questions_after_register');

function subscribe_to_questions_after_register($redirect)
{
    if (isset($_POST['expert_product_slug'])) {
        subscribe_user_as_product_expert();
    }

    return $redirect;
}

// NOTE: Subscribe to questions after login

add_filter('woocommerce_login_redirect', 'subscribe_to_questions_after_login', 10, 2);

function subscribe_to_questions_after_login($redirect, $user)
{
    if (isset($_POST['expert_product_slug'])) {
        subscribe_user_as_product_expert($user);
    }

    return $redirect;
}

// NOTE: Notify product experts on new question

add_action('yith_questions_answers_after_new_question', 'notify_product_experts_on_new_question');

function notify_product_experts_on_new_question($question)
{
    $product             = get_post($question->product_id);
    $expert_product_slug = $product->post_name;

    $users = get_users(['meta_key' => 'products_expert', 'meta_value' => $expert_product_slug, 'meta_compare' => 'LIKE']);

    foreach ($users as $user) {
        $customer_detail = [];

        $user_id    = $user->ID;
        $user_email = $user->user_email;

        $customer_detail['customer_user_id'] = $user_id;
        $customer_detail['billing_email']    = $user_email;

        $woocommerce_question_answer = YITH_WooCommerce_Question_Answer_Premium::get_instance();
        $woocommerce_question_answer->send_email_to_customer_of_same_product($question, $customer_detail);
    }
}

//
// HACK: [-2-] Keep post navigation in same term (overrides Storefront function)
// TODO: Check if working

function storefront_post_nav()
{
    $args = [
        'next_text'    => '<span class="screen-reader-text">' . esc_html__('Next post:', 'storefront') . ' </span>%title',
        'prev_text'    => '<span class="screen-reader-text">' . esc_html__('Previous post:', 'storefront') . ' </span>%title',
        'in_same_term' => true,
    ];

    the_post_navigation($args);
}

//
// HACK: [-2-] Relocate archive description

add_action('woocommerce_before_main_content', 'relocate_archive_description');

function relocate_archive_description()
{
    if (is_archive()) {
        ?>
        <h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?>
        </h1>

        <?php
        woocommerce_taxonomy_archive_description();
        woocommerce_product_archive_description();
    }
}

//
// HACK: [-2-] Remove update wishlist button

add_filter('tinvwl_manage_buttons_create', 'remove_update_wishlist_button');

function remove_update_wishlist_button($buttons)
{
    foreach ($buttons as $key => $button) {
        if ($button['name'] = 'product_update') {
            unset($buttons[$key]);
        }

        break;
    }

    return $buttons;
}

//
// HACK: [-3-] Change product review comment form args

add_filter('woocommerce_product_review_comment_form_args', 'change_product_review_comment_form_args');

function change_product_review_comment_form_args($comment_form)
{
    // NOTE: Change form title
    if (has_term('polls', 'product_tag')) {
        $comment_form['comment_field'] = str_replace('Your Review', 'Your Comment', $comment_form['comment_field']);

        return $comment_form;
    }

    if (has_term('hobbies', 'product_tag')) {
        $comment_form['comment_field'] = str_replace('Your Review', 'Your Tip', $comment_form['comment_field']);

        return $comment_form;
    }

    if (has_term('essentials', 'product_tag')) {
        $comment_form['comment_field'] = str_replace('Your Review', 'Your Review or Tip', $comment_form['comment_field']);

        return $comment_form;
    }

    return $comment_form;
}

//
// HACK: [-3-] Add CTA to last infinite scroll

add_filter('infinite_scroll_results', 'add_cta_to_last_infinite_scroll', 3, 10);

function add_cta_to_last_infinite_scroll($results, $query_args, $wp_query)
{
    if (true === $results['lastbatch'] && (false !== strpos($query_args['taxonomy'], 'pa_'))) {
        $results['html'] .= '<div class="infinite-scroll-end-cta"><p>Want to explore even more? Check out our <a href="/">full list of hobbies »</a></p></div>';
    } elseif (true === $results['lastbatch'] && (false === strpos($query_args['taxonomy'], 'pa_'))) {
        $results['html'] .= '<div class="infinite-scroll-end-cta"><p>That\'s it, for now. We are regularly adding more, so come back soon.</p><p>Found the list helpful?</p>' . do_shortcode('[addtoany]') . '</div>';
    }

    return $results;
}

//
// HACK: [-3-] Add CTA to short archive pages

add_action('woocommerce_after_main_content', 'add_cta_to_short_archive_pages');

function add_cta_to_short_archive_pages()
{
    if (is_archive()) {
        $wp_query = $GLOBALS['wp_the_query'];

        $pages = $wp_query->max_num_pages;

        if ($pages <= 1) {
            ?>

        <div class="infinite-scroll-end-cta">
            <p>Want to explore even more? <a href="/">Check out our full list of hobbies »</a></p>
        </div>

        <?php
        }
    }
}

//
// HACK: [-3-] Change account menu items

add_filter('woocommerce_account_menu_items', 'change_account_menu_items', 100);

function change_account_menu_items($menu_links)
{
    unset($menu_links['edit-address'], $menu_links['orders']);

    $menu_links['tinv_wishlist'] = 'Saved List';

    return $menu_links;
}

//
// HACK: [-3-] Add post classes

add_filter('woocommerce_post_class', 'add_post_classes', 10, 2);

function add_post_classes($classes, $product)
{
    if (has_term('essentials', 'product_tag')) {
        $essentials_tags = get_the_terms($product->get_id(), 'pa_essentials-tags');

        if (!empty($essentials_tags)) {
            $essentials_tags_classes = array_column($essentials_tags, 'slug');

            $classes = array_merge($classes, $essentials_tags_classes);
        }
    }

    if (get_field('recommended')) {
        $classes[] = 'recommended';
    }

    return $classes;
}

//
// HACK: [-3-] Add apps-essential term if store badges exist

add_action('acf/save_post', 'add_apps_essential_term');
// TODO: Replace with acf/update_value
// https://www.advancedcustomfields.com/resources/acf-update_value/

function add_apps_essential_term($post_id)
{
    $app_store_html_badge  = get_field('app_store_html_badge');
    $play_store_html_badge = get_field('play_store_html_badge');

    if ($app_store_html_badge || $play_store_html_badge) {
        $product = wc_get_product($post_id);

        wp_set_object_terms($post_id, 'apps-essential', 'pa_essentials-tags', true);

        $essential_tags = $product->get_attribute('pa_essentials-tags');

        if (empty($essential_tags)) {
            $meta_value = ['pa_essentials-tags' => [
                'name'        => 'pa_essentials-tags',
                'value'       => 'apps-essential',
                'is_visible'  => '1',
                'is_taxonomy' => '1',
            ]];

            update_post_meta($post_id, '_product_attributes', $meta_value);
        }
    } else {
        wp_remove_object_terms($post_id, 'apps-essential', 'pa_essentials-tags');
    }
}

//
// HACK: [-2-] Remove WooCommerce page title

add_filter('woocommerce_show_page_title', '__return_false');

//
// HACK: [-3-] Change WooCommerce product thumbnails columns

add_filter('woocommerce_product_thumbnails_columns', 'change_woocommerce_product_thumbnails_columns', 100);

function change_woocommerce_product_thumbnails_columns()
{
    return 3;
}

//
// HACK: [-3-] Add PWA short_name

add_filter(
    'web_app_manifest',
    function ($manifest) {
        $manifest['short_name'] = 'S-Hobbies';

        return $manifest;
    }
);

//
// HACK: [-3-] Add essential hover links

add_action('woocommerce_before_shop_loop_item', 'add_essential_hover_links', 5);

function add_essential_hover_links()
{
    global $product;

    $product_id = $product->get_id();

    if (has_term(['essentials'], 'product_tag')) {
        static $index ;

        if (!isset($index)) {
            $index = 1;
        }

        $external_url = $product->get_product_url();
        $internal_url = $product->get_permalink();

        $domain = get_url_domain($external_url);

        $essential_tags = $product->get_attribute('pa_essentials-tags'); ?>

        <div class="hover-links">
            <?php
        echo do_shortcode('[ti_wishlists_addtowishlist]');

        $essential_tags = !empty($essential_tags) ? explode(', ', $essential_tags) : [];

        if (in_array('Apps', $essential_tags)) {
            $app_store_html_badge = get_field('app_store_html_badge', $product_id);
            $app_store_html_badge = add_app_store_image($app_store_html_badge);

            $play_store_html_badge = get_field('play_store_html_badge', $product_id);
            $play_store_html_badge = add_play_store_image($play_store_html_badge);

            echo $app_store_html_badge;
            echo $play_store_html_badge;
        }

        $site_description = get_field('site_description', $product_id);

        if (!empty($site_description)) {
            ?>

            <div class="site_description-wrapper">
                <input type='checkbox' class='toggle site_description_toggle-input' id='essentials-grid-<?php echo $index; ?>' name='site_description_toggle'><label class='site_description_toggle-label' for='essentials-grid-<?php echo $index; ?>'></label>
                <a href="<?php echo $external_url; ?>" class="site_description product-url"><span class="text"><?php echo $site_description; ?></span><span class='site_description-hide material-icons'>cancel</span></a>
            </div>

            <?php
        } ?>
        </div>

        <?php

        $index++;
    }
}

add_action('woocommerce_after_shop_loop_item', 'add_amazon_favicon', 4);

function add_amazon_favicon()
{
    global $product;

    if ($product->is_type('external')) {
        $external_url = $product->get_product_url();

        if (strpos($external_url, 'amzn.to') || strpos($external_url, 'amazon.com')) {
            ?>

        <span class="amazon-favicon"><img loading="lazy" src="/wp-content/uploads/favicons/amzn.to.ico" alt=""></span>

        <?php
        }
    }
}

//
// HACK: [-2-] Get URL domain

function get_url_domain($url)
{
    $start = strpos($url, '//') + 2;
    $end   = strpos($url, '/', 8);

    if ($end !== false) {
        $domain = substr($url, $start, $end - $start);
    } else {
        $domain = substr($url, $start);
    }

    return $domain;
}

//
// HACK: [-2-] Get URL protocol and domain

function get_url_protocol_and_domain($url)
{
    $start = 0;
    $end   = strpos($url, '/', 8);

    if ($end !== false) {
        $protocol_and_domain = substr($url, $start, $end - $start);
    } else {
        $protocol_and_domain = substr($url, $start);
    }

    return $protocol_and_domain;
}

//
// HACK: [-3-] Add Tonesque support

add_action('after_setup_theme', 'add_tonesque_support');

function add_tonesque_support()
{
    add_theme_support('tonesque');
}

//
// HACK: [-2-] Get favicon URL

function get_favicon_url($url)
{
    static $page_favicon_urls;

    $domain = get_url_domain($url);

    if (isset($page_favicon_urls[$domain])) {
        return $page_favicon_urls[$domain];
    }

    $absolute_filepath = ABSPATH . "wp-content/uploads/favicons/{$domain}.ico";
    $relative_filepath = "/wp-content/uploads/favicons/{$domain}.ico";

    if (file_exists($absolute_filepath)) {
        $favicon_url = $relative_filepath;

        $page_favicon_urls[$domain] = $favicon_url;

        return $favicon_url;
    }

    $protocol_and_domain = get_url_protocol_and_domain($url);

    $duckduckgo_ip3_url = "https://icons.duckduckgo.com/ip3/{$domain}.ico";
    $html_favicon_url   = get_html_favicon_url($url);
    $google_s2_url      = "https://www.google.com/s2/favicons?domain={$domain}";
    $naive_url          = $protocol_and_domain . '/favicon.ico';

    if (false) { //NOTE: Manually switch to Google S2 for some domains
        $favicon_url = $google_s2_url;
    } else {
        // NOTE: Check if URL is valid
        // Check if favicon exists and is visible.
        // Can't be checked with JS onerror because DuckDuckGo provides a placeholder which prevents error triggering

        $is_valid_favicon_url = false;

        $favicon_url = $duckduckgo_ip3_url;

        $is_valid_favicon_url = is_valid_favicon_url($favicon_url);

        if ($is_valid_favicon_url === false) {
            $favicon_url = $html_favicon_url;

            if (!empty($favicon_url) && wp_http_validate_url($favicon_url)) {
                $is_valid_favicon_url = is_valid_favicon_url($favicon_url);
            }

            if ($is_valid_favicon_url === false) { // TODO: Fallback to local icon if Google S2 fails
                $favicon_url = $google_s2_url;

                $is_valid_favicon_url = is_valid_favicon_url($favicon_url);
            }
        }

        if ($is_valid_favicon_url === true) {
            // NOTE: Last custom fallback if is default Google icon
            if (class_exists('Tonesque')) {
                $tonesque = new Tonesque($favicon_url);

                $color = $tonesque->color();

                if ($color === 'dee2ee') {
                    if (strpos($favicon_url, 'fei.org') !== false) {
                        $favicon_url = 'https://www.fei.org/sites/all/themes/feifan/favicon.ico';
                    }
                }
            }
        } else {
            $favicon_url = $naive_url;

            $is_valid_favicon_url = is_valid_favicon_url($favicon_url);

            if ($is_valid_favicon_url === false) {
                $favicon_url = '/wp-content/plugins/theme-customisations/custom/assets/link-material-icon.png';

                $page_favicon_urls[$domain] = $favicon_url;

                return $favicon_url;
            }
        }
    }

    if (!function_exists('download_url')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    // TODO: Use file_get_contents and file_put_contents or copy instead?
    // https://stackoverflow.com/questions/1371966/save-remote-img-file-to-server-with-php

    $file_url  = $favicon_url;
    $temp_file = download_url($file_url, 300, true);

    if (!is_wp_error($temp_file)) {
        $mime_content_type = mime_content_type($temp_file);

        if (strpos($mime_content_type, 'image') !== false) {
            $is_moved = rename($temp_file, $absolute_filepath);

            if ($is_moved) {
                $image_editor = wp_get_image_editor($absolute_filepath);

                if (!is_wp_error($image_editor)) {
                    $size = $image_editor->get_size();

                    if ($size['width'] > 48) {
                        $image_editor->resize(48, null);
                        $image_editor->save($absolute_filepath);
                    }
                }

                $favicon_url = $relative_filepath;

                $page_favicon_urls[$domain] = $favicon_url;

                return $favicon_url;
            }
        }

        unlink($temp_file);
    } // NOTE: If not saved, external favicon_url kept

    $page_favicon_urls[$domain] = $favicon_url;

    return $favicon_url;
}

// HACK: [-3-] Check if is valid favicon URL

function is_valid_favicon_url($favicon_url)
{
    $is_valid_favicon_url = false;

    $response = wp_safe_remote_head($favicon_url);
    $headers  = wp_remote_retrieve_headers($response);

    if (empty($headers)) {
        global $bugsnagWordpress;

        $bugsnagWordpress->notifyError('Error', 'Failed to retrieve headers', [
            'favicon_url' => $favicon_url,
            'response'    => $response
        ], 'error');

        return false;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $content_type  = $headers->offsetGet('content-type');

    if ($response_code === 200 && (strpos($content_type, 'image') !== false)) {
        $is_valid_favicon_url = true;

        if (class_exists('Tonesque')) {
            $tonesque = new Tonesque($favicon_url); // NOTE: Only works sometimes

            $color = $tonesque->color();

            if ($color === 'ffffff') {
                $is_valid_favicon_url = false;
            }
        }
    }

    return $is_valid_favicon_url;
}

// HACK: [-2-] Grab favicon URL from HTML

function get_html_favicon_url($url)
{
    $response_body = get_response_body($url);

    $preg_match = preg_match('/<link rel=\"[^"<>]*icon[^"<>]*\"[^<>]*href=\"(.*?)\"/si', $response_body, $match);

    if ($preg_match) {
        $favicon_url = $match[1];

        if (strpos($favicon_url, '/') === 0) {
            $protocol_and_domain = get_url_protocol_and_domain($url);

            $favicon_url = $protocol_and_domain . $favicon_url;
        }

        return $favicon_url;
    }

    return;
}

//
// HACK: [-9-] Remove thumbnails sizes

add_action('admin_init', 'remove_thumbnails_sizes', 999);

function remove_thumbnails_sizes()
{
    global $_wp_additional_image_sizes;

    $sizes = [];

    foreach (get_intermediate_image_sizes() as $_size) {
        if (in_array($_size, ['thumbnail', 'medium', 'medium_large', 'large'])) {
            update_option("{$_size}_size_w", 0);
            update_option("{$_size}_size_h", 0);
            update_option("{$_size}_crop", 0);
        } elseif (isset($_wp_additional_image_sizes[$_size])) {
            remove_image_size($_size);
        }
    }
}

//
// HACK: [-2-] Limit image size

add_filter('single_product_archive_thumbnail_size', function ($size) {
    if (has_term('essentials', 'product_tag')) {
        $size = [324, 648];
    } else {
        $size = [324, 648];
    }

    return $size;
});

add_filter('woocommerce_gallery_image_size', function ($size) {
    if (has_term('essentials', 'product_tag')) {
        $size = [324, 648];
    }

    return $size;
});

add_filter('woocommerce_gallery_thumbnail_size', function ($size) {
    $size = [96, 96];

    return $size;
});

//
// HACK: [-2-] Enable product revisions

add_filter('woocommerce_register_post_type_product', 'enable_product_revisions');

function enable_product_revisions($args)
{
    $args['supports'][] = 'revisions';

    return $args;
}

//
// HACK: [-3-] Add lazy loading attribute to images
// TODO: Remove Jetpack lazy load when browser support is good

add_filter('wp_get_attachment_image_attributes', 'add_lazy_loading_attribute_to_images', 10, 2);

function add_lazy_loading_attribute_to_images($attr, $attachment)
{
    $attr['loading'] = 'lazy';

    return $attr;
}

//
// HACK: [-3-] Change external loop product link

add_filter('woocommerce_loop_product_link', 'change_external_loop_product_link', 10, 2);

function change_external_loop_product_link($link, $product)
{
    if ($product->is_type('external')) {
        $link = $product->get_product_url();
    }

    return $link;
}

//
// HACK: [-3-] Amazon associates program disclaimer

add_action('storefront_footer', 'amazon_associates_program_disclaimer', 20);

function amazon_associates_program_disclaimer()
{
    if (is_singular()) {
        ?>

        <div>
            Simply Hobbies is a participant in the Amazon Services LLC Associates Program, an affiliate advertising program designed to provide a means for sites to earn advertising fees by advertising and linking to amazon.com
        </div>

        <?php
    }
}

//
// HACK: [-2-] Get podcast player
// https://gist.github.com/jeherve/ec1293761c71f56d4362e2260fbe810f

function get_podcast_player($url, $items_to_show = 2, $show_cover_art = false, $show_episode_description = true)
{
    if (!class_exists('Jetpack')) {
        return;
    }

    if (!wp_http_validate_url($url)) {
        return;
    }

    if (!class_exists('Jetpack_Podcast_Helper')) {
        jetpack_require_lib('class-jetpack-podcast-helper');
    }

    $attributes = [
        'url'                    => $url,
        'itemsToShow'            => $items_to_show,
        'showCoverArt'           => $show_cover_art,
        'showEpisodeTitle'       => true,
        'showEpisodeDescription' => $show_episode_description,
    ];

    $player_data = Jetpack_Podcast_Helper::get_player_data($attributes['url']);

    if (is_wp_error($player_data)) {
        return $player_data;
    }

    $player = \Automattic\Jetpack\Extensions\Podcast_Player\render_player($player_data, $attributes);

    return $player;
}

//
// HACK: [-3-] Change Jetpack Site Accelerator args

add_filter('jetpack_photon_pre_args', 'sa_custom_params');

function sa_custom_params($args)
{
    $args['quality'] = 100;
    $args['strip']   = 'all';

    return $args;
}

//
// HACK: [-3-] Add lazy loading attribute oEmbed

//add_filter('oembed_result', 'add_lazy_loading_attribute_to_oembed', 10, 3);

function add_lazy_loading_attribute_to_oembed($html, $url, $args)
{
    $html = str_replace(' src="https://www.youtube.com/embed/', ' loading="lazy" src="" data-src="https://www.youtube.com/embed/', $html);

    return $html;
}

//
// HACK: [-2-] Maybe purge custom assets

if (!wp_next_scheduled('maybe_purge_custom_assets')) {
    wp_schedule_event(time(), 'hourly', 'maybe_purge_custom_assets');
}

add_action('maybe_purge_custom_assets', 'maybe_purge_custom_assets');
add_action('admin_head', 'maybe_purge_custom_assets');

function maybe_purge_custom_assets()
{
    $assets = ['style.css', 'custom.js'];

    foreach ($assets as $asset) {
        $extention = pathinfo($asset, PATHINFO_EXTENSION);
        $asset_ver = filemtime_ver($asset);

        if (((int) get_transient("custom_{$extention}_ver_transient")) !== $asset_ver) {
            do_action('litespeed_purge_url', plugins_url($asset, __FILE__));
            cloudflare_purge_files_by_url([plugins_url($asset, __FILE__)]);

            set_transient("custom_{$extention}_ver_transient", $asset_ver, 2592000);
        }
    }
}

//
// HACK: [-2-] Schedule delete old favicons cron

if (!wp_next_scheduled('sh_delete_old_favicons')) {
    wp_schedule_event(time(), 'monthly', 'sh_delete_old_favicons');
}

add_action('sh_delete_old_favicons', 'sh_delete_old_favicons');

function sh_delete_old_favicons()
{
    if (wp_get_environment_type() === 'production') {
        $files = [];

        $absolute_path = ABSPATH . 'wp-content/uploads/favicons/';

        $files = scandir($absolute_path);

        if (is_array($files)) {
            foreach ($files as $file) {
                $rand = mt_rand(1, 3); // NOTE: Randomize to reduce bulk re-rendering on same page

                if ($rand == 2) {
                    $absolute_filepath = $absolute_path . $file;

                    $date_time_instance = new DateTime();

                    $filemtime      = filemtime($absolute_filepath);
                    $file_date_time = $date_time_instance->setTimestamp($filemtime);

                    $now = new DateTime('now');

                    $diff = intval($file_date_time->diff($now)->format('%R%a'));

                    if ($diff > 10) {
                        unlink($absolute_filepath);
                    }
                }
            }
        }
    }
}

//
// HACK: [-2-] Schedule shuffle menu_order cron

if (!wp_next_scheduled('sh_shuffle_menu_order')) {
    wp_schedule_event(time(), 'weekly', 'sh_shuffle_menu_order');
}

add_action('sh_shuffle_menu_order', 'sh_shuffle_menu_order');

function sh_shuffle_menu_order()
{
    $site_url = get_site_url();

    do_action('litespeed_purge_url', $site_url);

    //$urls[] = $site_url ;

    //$cloudflare_purge_result = cloudflare_purge_files_by_url($urls);

    $frontpage_id = get_option('page_on_front');

    $cloudflareHooks = new \CF\WordPress\Hooks();

    $cloudflareHooks->purgeCacheByRelevantURLs($frontpage_id);

    if (wp_get_environment_type() === 'production') {
        $args = [
            'tag'     => ['hobbies'],
            'limit'   => 100,
            'status'  => 'publish',
            'orderby' => 'rand',
        ];

        $hobbies = wc_get_products($args);

        foreach ($hobbies as $key => $hobby) {
            $hobby->set_menu_order(mt_rand(1, 500));

            $hobby->save();
        }
    }
}

//
// HACK:

//add_filter('acf/update_value/name=app_store_html_badge', 'remove_img_src', 10, 3);
//add_filter('acf/update_value/name=play_store_html_badge', 'remove_img_src', 10, 3);

function remove_img_src($value, $post_id, $field)
{
    $value = str_replace(' src=', ' data-src=', $value);

    return $value;
}

//
// HACK: [-2-] Grab site name

add_filter('acf/update_value/name=site_name', 'update_value_site_name', 10, 3);

function update_value_site_name($value, $post_id, $field)
{
    if ($url = filter_var($value, FILTER_VALIDATE_URL)) {
        $meta_tags = _get_meta_tags($url);

        if (!empty($meta_tags['og:site_name'])) {
            $value = $meta_tags['og:site_name'];
        } elseif (!empty($meta_tags['twitter:app:name:googleplay'])) {
            $value = $meta_tags['twitter:app:name:googleplay'];
        } elseif (!empty($meta_tags['twitter:app:name:iphone'])) {
            $value = $meta_tags['twitter:app:name:iphone'];
        } else {
            $value = '';
        }

        $value = html_entity_decode(strip_tags($value), ENT_QUOTES);
    }

    $value = strip_invisible_characters($value);

    return $value;
}

//
// HACK: [-3-]  Grab site_title

add_filter('acf/update_value/name=site_title', 'update_value_site_title', 10, 3);

function update_value_site_title($value, $post_id, $field)
{
    if ($url = filter_var($value, FILTER_VALIDATE_URL)) {
        $meta_tags = _get_meta_tags($url);

        if (!empty($meta_tags['og:title'])) {
            $value = $meta_tags['og:title'];
        } elseif ($title_tag = get_title_tag($url)) {
            $value = $title_tag;
        } else {
            $value = '';
        }

        $value = html_entity_decode(strip_tags($value), ENT_QUOTES);
    }

    $value = strip_invisible_characters($value);

    return $value;
}

//
// HACK: [-3-]  Grab site_description

add_filter('acf/update_value/name=site_description', 'update_value_site_description', 10, 3);

function update_value_site_description($value, $post_id, $field)
{
    if ($url = filter_var($value, FILTER_VALIDATE_URL)) {
        $meta_tags = _get_meta_tags($url);

        if (!empty($meta_tags['og:description'])) {
            $value = $meta_tags['og:description'];
        } elseif (!empty($meta_tags['description'])) {
            $value = $meta_tags['description'];
        } elseif (!empty(get_schema_description($url))) {
            $value = get_schema_description($url);
        } else {
            $value = '';
        }

        $value = html_entity_decode(strip_tags($value), ENT_QUOTES);
    }

    $value = strip_invisible_characters($value);

    return $value;
}

function strip_invisible_characters($value)
{
    $search  = ["\xc2\xa0", "\xE2\x80\x8B"]; // NO-BREAK SPACE; ZERO WIDTH SPACE
    $replace = ['', ''];

    $value = str_replace($search, $replace, $value);

    $value = trim($value);

    return $value;
}

//
// HACK: [-2-] Grab title tag

function get_title_tag($url)
{
    $response_body = get_response_body($url);

    $preg_match = preg_match('/<title(.*?)<\/title>/si', $response_body, $match);

    if ($preg_match) {
        $title_tag = trim(html_entity_decode(strip_tags($match[0]), ENT_QUOTES));

        return $title_tag;
    }

    return;
}

//
// HACK: [-2-] Get Schema Description

function get_schema_description($url)
{
    $response_body = get_response_body($url);

    $preg_match = preg_match('/\"description\":\"(.*?)\"/si', $response_body, $match);

    if ($preg_match) {
        $schema_description = trim(html_entity_decode(strip_tags(trim($match[1], '"')), ENT_QUOTES));

        return $schema_description;
    }

    return;
}

function get_squarespace_description($url)
{
    $response_body = get_response_body($url);

    $preg_match = preg_match('/\"siteDescription\":\"(.*?)\"/si', $response_body, $match);

    if ($preg_match) {
        $squarespace_description = trim(html_entity_decode(strip_tags(trim($match[1], '"')), ENT_QUOTES));

        return $squarespace_description;
    }

    return;
}

//
// HACK: [-2-] Get Meta Tags (prefixed with '_' because of native PHP function)

function _get_meta_tags($url)
{
    static $meta_tags;

    if (isset($meta_tags[$url])) {
        return $meta_tags[$url];
    }

    $response_body = get_response_body($url);

    $pattern = '
  ~<\s*meta\s

  # using lookahead to capture type to $1
    (?=[^>]*?
    \b(?:name|property|http-equiv)\s*=\s*
    (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
    ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
  )

  # capture content to $2
  [^>]*?\bcontent\s*=\s*
    (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
    ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
  [^>]*>

  ~ix';

    if (preg_match_all($pattern, $response_body, $out)) {
        $meta_tags[$url] = array_combine($out[1], $out[2]);

        return $meta_tags[$url];
    }

    return;
}

//
// HACK: [-2-] Override core woocommerce_template_loop_product_title

function woocommerce_template_loop_product_title()
{
    if (!empty(get_field('site_name'))) {
        global $post;

        $site_name        = (!empty($site_name = get_field('site_name')) ? "<span class='site_name'>{$site_name}</span>" : '');
        $site_title       = (!empty($site_title = get_field('site_title')) ? "<span class='site_title'>{$site_title}</span>" : '');
        $site_description = (!empty($site_description = get_field('site_description')) ? "<span class='site_description'>{$site_description}</span>" : '');

        $author = (!empty($author = get_field('author')) ? "<span class='author'>{$author}</span>" : '');
        $brand  = (!empty($brand = get_field('brand')) ? "<span class='brand'>{$brand}</span>" : '');

        //$helpful_count = (!empty($helpful_count = get_field('helpful')) ? $helpful_count['yes'] : '');
        // TODO: Switch back after all posts updated with this field
        if ($helpful_count = get_post_meta($post->ID, 'helpful_yes', true)) {
            $args['helpful_count'] = (int) $helpful_count[0];
        } else {
            $args['helpful_count'] = 0;
        }

        if ($helpful_count) {
            $helpful_count_badge = "<span class='helpful_count_badge' title='{$helpful_count} found the link helpful. Visit it and come back to vote.'><span class='material-icons-outlined yes'>thumb_up</span><span class='count'>+{$helpful_count}</span></span>";
        } else {
            $helpful_count_badge = '';
        }

        $recommended = (!empty($recommended = get_field('recommended')) ? '<span class="material-icons-outlined recommended" title="Recommended">grade</span>' : '');

        if ($author || $brand) {
            $site_name = '';
        }

        $post_title = "<span class='link_text'>{$helpful_count_badge}{$recommended}{$author}{$brand}{$site_name}{$site_title}</span>";

        echo '<h2 class="' . esc_attr(apply_filters('woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title')) . '">' . $post_title . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    } else {
        echo '<h2 class="' . esc_attr(apply_filters('woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title')) . '">' . get_the_title() . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

//
// HACK: [-2-] Get Response Body

function get_response_body($url)
{
    static $response_bodies;

    if (isset($response_bodies[$url])) {
        return $response_bodies[$url];
    }

    $response = wp_safe_remote_get($url, ['limit_response_size' => 76800]);
    $body     = wp_remote_retrieve_body($response);

    $response_bodies[$url] = $body;

    return $response_bodies[$url];
}

//
// HACK: [-3-] Change WP link query results

add_filter('wp_link_query', 'change_wp_link_query_results', 10, 2);

function change_wp_link_query_results($results, $query)
{
    // NOTE: Change external products links to external
    foreach ($results as $key => $result) {
        if ($result['info'] === 'Product') {
            $product_id = $result['ID'];

            $product = wc_get_product($product_id);

            if ($product->is_type('external')) {
                $external_url = $product->get_product_url();

                $results[$key]['permalink'] = $external_url;
            }
        }
    }

    return $results;
}

//
// HACK: [-3-] Change WooCommerce min password strength

add_filter('woocommerce_min_password_strength', 'change_woocommerce_min_password_strength');

function change_woocommerce_min_password_strength()
{
    return 1;
}

//
// HACK: [-Z-]

//add_filter('woocommerce_single_product_image_thumbnail_html', 'defer_featured_video', 100, 2);

function defer_featured_video($html, $post_thumbnail_id)
{
    $html = str_replace('onload="onYouTubeIframeAPIReady()" src="https://www.youtube.com/embed/', 'onload="onYouTubeIframeAPIReady()" src="" data-src="https://www.youtube.com/embed/', $html);

    return $html;
}

//
// HACK: [-2-] Get broken links URLs
// TODO: Save to transient? Make global?

function get_broken_links_urls($container_type, $parser_type, $post_id)
{
    global $post;

    static $broken_links_urls;

    if ($post_id === $post->ID && isset($broken_links_urls)) {
        return $broken_links_urls;
    }

    if (!function_exists('blc_get_links')) {
        return;
    }

    $broken_links = blc_get_links([
        //'s_parser_type' => $parser_type,
        //'s_container_type' => $container_type,
        's_container_id' => $post_id,
        's_http_code'    => '0,404,503',
        's_filter'       => 'broken',
    ]);

    $broken_links_urls = [];

    foreach ($broken_links as $broken_link) {
        $broken_links_urls[] = $broken_link->url;
    }

    return $broken_links_urls;
}

//
// HACK: [-2-] Cloudflare purge on save

add_action('save_post', 'cloudflare_purge_on_save', 100, 3);

function cloudflare_purge_on_save($post_ID, $post, $update)
{
    //$post_permalink = get_the_permalink($post);

    //do_action('litespeed_purge_url', $post_permalink);

    $essential_related_hobbies_urls = get_essential_related_hobbies_urls($product_id);

    foreach ($essential_related_hobbies_urls as $url) {
        do_action('litespeed_purge_url', $url);
    }

    //$urls[] = $post_permalink;

    //$result = cloudflare_purge_files_by_url($urls);
}

//
// HACK: [-3-] Cloudflare purge files by URL

function cloudflare_purge_files_by_url($urls)
{
    $files['files'] = $urls;
    $files_json     = json_encode($files, JSON_UNESCAPED_SLASHES);

    $headers   = [];
    $headers[] = 'X-Auth-Email: asafm7@gmail.com';
    $headers[] = 'X-Auth-Key: ' . CLOUDFALRE_X_AUTH_KEY;
    $headers[] = 'Content-Type: application/json';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/zones/' . CLOUDFALRE_ZONE_ID . '/purge_cache');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $files_json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);

    return $result;
}

//
// HACK: [-2-] Modify related links to purge

add_filter('cloudflare_purge_by_url', 'modify_related_links_to_purge', 10, 2);

function modify_related_links_to_purge($urls, $postId)
{
    $related_hobbies_urls = get_essential_related_hobbies_urls($postId);

    $urls = array_merge($urls, $related_hobbies_urls);

    return $urls;
}

function get_essential_related_hobbies_urls($postId)
{
    static $urls;

    if (isset($urls)) {
        return $urls;
    }

    if (get_post_type($postId) === 'product') {
        add_filter('woocommerce_product_related_posts_relate_by_tag', '__return_false');

        $related_products = wc_get_related_products($postId);

        foreach ($related_products as $related_product) {
            if (has_term('hobbies', 'product_tag', $related_product)) {
                $product = wc_get_product($related_product);

                $urls[] = $product->get_permalink();
            }
        }
    }

    return $urls;
}

//
// HACK: [-2-] Purge Cloudflare on upgrades

//add_action('upgrader_process_complete', 'cloudflare_purge_all', 100);
//add_action('admin_action_do-plugin-upgrade', 'cloudflare_purge_all', 100);

//
// HACK: [-2-] Purge Cloudflare

function cloudflare_purge_all()
{
    $ch = curl_init();

    $headers   = [];
    $headers[] = 'X-Auth-Email: asafm7@gmail.com';
    $headers[] = 'X-Auth-Key: ' . CLOUDFALRE_X_AUTH_KEY;
    $headers[] = 'Content-Type: application/json';

    curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/zones/' . CLOUDFALRE_ZONE_ID . '/purge_cache');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{"purge_everything":true}');

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);

    return $result;
}

/*
if ( is_array( $log ) || is_object( $log ) ) {
   error_log( print_r( $log, true ) );
} else {
   error_log( $log );
}
*/

// HACK: External
// wp-content\themes\galleria\searchwp-live-ajax-search\search-results.php

/*
// HACK: [-Z-] Change Jetpack sharing display markup
// NOTE: Add CSS ID to enable bookmark link

add_filter('jetpack_sharing_display_markup', 'change_jetpack_sharing_display_markup', 10, 2);

function change_jetpack_sharing_display_markup($sharing_content, $enabled)
{
    static $id_exists;

    if (!isset($id_exists) && !wp_doing_ajax()) {
        $sharing_content = str_replace('<div class="sharedaddy', '<div id="sharedaddy" class="sharedaddy', $sharing_content);

        $id_exists = true;
    }

    return $sharing_content;
}

//
// HACK: [-Z-] Add sharing display

add_action('storefront_post_header_after', 'add_sharing_display', 50);
add_action('woocommerce_before_shop_loop', 'add_sharing_display', 50);

function add_sharing_display()
{
    if (function_exists('sharing_display')) {
        sharing_display('', true);
    }

    // TODO: Check if likes are showing
    if (class_exists('Jetpack_Likes')) {
        $custom_likes = new Jetpack_Likes();

        echo $custom_likes->post_likes('');
    }
}
*/

//Added, Enhanced, Fixed, Changed
