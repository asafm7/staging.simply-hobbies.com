<?php
/** 
 * Search results are contained within a div.searchwp-live-search-results
 * which you can style accordingly as you would any other element on your site.
 *
 * Some base styles are output in wp_footer that do nothing but position the
 * results container and apply a default transition, you can disable that by
 * adding the following to your theme's functions.php:
 *
 * add_filter( 'searchwp_live_search_base_styles', '__return_false' );
 *
 * There is a separate stylesheet that is also enqueued that applies the default
 * results theme (the visual styles) but you can disable that too by adding
 * the following to your theme's functions.php:
 *
 * wp_dequeue_style( 'searchwp-live-search' );
 *
 * You can use ~/searchwp-live-search/assets/styles/style.css as a guide to customize
 */
?>

<?php do_action('searchwp_metrics_click_tracking_start'); ?>

<?php if (have_posts()) {
    global $wp_query; ?>
<?php while (have_posts()) {
        the_post(); ?>
<?php $post_type = get_post_type_object(get_post_type()); ?>
<div class="searchwp-live-search-result" role="option" id="" aria-selected="false">
	<p><a href="<?php echo esc_url(get_permalink()); ?>">
			<?php the_title(); ?> &raquo;
		</a></p>
</div>
<?php
    }

    if (20 == $wp_query->post_count) {
        ?>

<button class="button all-results" type="submit" name="button" form="searchform"><?php _e('All Results', 'theme-customisations'); ?></button>

<?php
    } else {
        wc_print_notice(__('Couldn\'t find what you were looking for? <a href="/contact/" title="Contact me">Let us know!</a>', 'theme-customisations'), 'notice');
    }
} else {
    ?>
<p class="searchwp-live-search-no-results" role="option">
	<em><?php esc_html_e('No results found.', 'swplas'); ?></em>
</p>
<?php
wc_print_notice(__('Couldn\'t find what you were looking for? <a href="/contact/" title="Contact">Let me know!</a>', 'theme-customisations'), 'notice');
} ?>

<?php do_action('searchwp_metrics_click_tracking_stop');
