<?php
/**
 * Plugin Name: SWG Downloads
 * Description: Simple downloader based on ACF and Bootstrap intended for WordPress theme developers
 * Version: 1.0.11
 * Author: Eric Griffiths
 * Author URI: https://stonewallgroup.com
 * License: GPLv2 or later
 * Text Domain: swg-downloads
 */

/*
 *  TODO: get all ACF fields and make them available in the templates
 *  TODO: Show count number in list
*/


include( plugin_dir_path( __FILE__ ) . '/includes/init.php');


/**
 * Set thumb image size.
 */
add_action('init', function(){
    if(function_exists('get_field')) {
        $w = wp_kses(get_field('swg_thumbnail_image_width', 'options'),null);
        $h = wp_kses(get_field('swg_thumbnail_image_height', 'options'),null);
    }
    if (!$w) {
        $w = 150;
    }
    if (!$h) {
        $h = 100;
    }
    add_image_size( 'swg_download_thumb', $w, $h, true );
});




/**
 * Add filter for admin posts
 */
add_action('restrict_manage_posts', function() {
    global $typenow;
    $term = get_term_by('slug', get_query_var('swg_download_category'), 'swg_download_category');
    if ($typenow == 'swg_downloads') {
        $selected = isset($term->term_id) ? $term->term_id : '';
        $info_taxonomy = get_taxonomy('swg_download_category');
        wp_dropdown_categories(array(
            'show_option_all' => sprintf(__('Show all %s', 'swg-downloads'), $info_taxonomy->label),
            'taxonomy' => 'swg_download_category',
            'name' => 'swg_download_category',
            'orderby' => 'name',
            'selected' => $selected,
            'show_count' => true,
            'hide_empty' => true,
        ));
    };
});


add_filter('parse_query', function($query){
    global $pagenow;
    $post_type = 'swg_downloads';
    $taxonomy = 'swg_download_category';
    $q_vars = &$query->query_vars;
    if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
        $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
        $q_vars[$taxonomy] = $term->slug;
    }
});




/**
 * Add shortcode for 1 download or whole category
 */
add_action('init', function(){
    add_shortcode('swg_downloads', 'swg_downloads_build_template');
});






/**
 * locate template file
 */
function swg_downloads_locate_template($template) {
    if(file_exists(trailingslashit(get_stylesheet_directory()).'swg_plugins/swg_downloads/'.$template)) {
        $located = trailingslashit(get_stylesheet_directory()).'swg_plugins/swg_downloads/'.$template;
    } elseif (file_exists(trailingslashit(get_template_directory()).'swg_plugins/swg_downloads/'.$template)) {
        $located = trailingslashit(get_template_directory()).'swg_plugins/swg_downloads/'.$template;
    } elseif (file_exists(trailingslashit(plugin_dir_path( __FILE__ )).'views/'.$template)) {
        $located = trailingslashit(plugin_dir_path( __FILE__ )).'views/'.$template;
    } else {
        $located = trailingslashit(plugin_dir_path( __FILE__ )).'views/single.php';
    }
    return $located;
}

/**
 * Build the template
 */
function swg_downloads_get_temp($id,$temp='single.php') {

    $url = trailingslashit(get_site_url()).'swg_downloads/swg_download_id/'.$id;
    $img = get_field('thumbnail',$id);

    $dl['url']           =  $url;
    $dl['thumbnail']     =  $img['sizes']['swg_download_thumb'];
    $dl['thumbnail_alt'] =  $img['alt'];
    $dl['width']         =  $img['sizes']['swg_download_thumb-width'];
    $dl['height']        =  $img['sizes']['swg_download_thumb-height'];
    $dl['button_text']   =  sanitize_text_field(get_field('button_text',$id));
    $dl['title']         =  sanitize_text_field(get_the_title($id));

    $template = swg_downloads_locate_template($temp);

    return load_template($template,false, $dl);
}



function swg_downloads_build_template($atts) {
    if (function_exists('get_field')) {

        $atts = shortcode_atts(
            array(
                'id'        => '',
                'cat'       => '',
                'col'      => '',
                'template'  => '',
                'break'     => '',
                'container' => '',
                'row_class'   => ''
            ), $atts, 'swg_downloads');
        if ($atts['id']) {
            $atts['id'] = sanitize_text_field($atts['id']);
            $atts['template'] = sanitize_text_field($atts['template']);
            if (!$atts['template']) {
                $atts['template'] = 'single.php';
            }
            ob_start();
            echo swg_downloads_get_temp($atts['id'],$atts['template']);

            return wp_kses_post(ob_get_clean());;

        } elseif ($atts['cat']) {
            if (!$atts['col']) {
                $atts['col'] = 3;
            }
            if (!$atts['break']) {
                $atts['break'] = 'md';
            }
            if (!$atts['template']) {
                $atts['template'] = 'multiple.php';
            }
            if (!$atts['container']) {
                $atts['container'] = 'container';
            }
            $atts['cat'] = sanitize_text_field($atts['cat']);
            $atts['col'] = sanitize_text_field($atts['col']);
            $atts['break'] = sanitize_text_field($atts['break']);
            $atts['template'] = sanitize_text_field($atts['template']);
            $atts['container'] = sanitize_text_field($atts['container']);
            $atts['row_class'] = sanitize_text_field($atts['row_class']);
            $col = 'col-'.$atts['break'].'-'.floor(12 / $atts['col']);
            $args = array(
                'post_type' => 'swg_downloads',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'swg_download_category',
                        'field' => 'slug',
                        'terms' => $atts['cat']
                    )
                )
            );
            $query = new WP_Query( $args );
            wp_reset_postdata();
            ob_start();
            echo '<div class="'.$atts['container'].'"><div class="row '.$atts['row_class'].'">';
            if ($query->posts && count($query->posts) > 0) {
                foreach ($query->posts as $post) {
                    echo '<div class="'.$col.' mb-4">';
                    echo swg_downloads_get_temp($post->ID,$atts['template']);
                    echo '</div>';
                }
            }
            echo '</div></div>';

            return wp_kses_post(ob_get_clean());

        }
    } else {
        return '<p>Please enable ACF Pro.</p>';
    }
}



/**
 * Add column to admin posts view
 */
add_filter ( 'manage_swg_downloads_posts_columns', function($columns) {
    if (function_exists('get_field')) {
        unset($columns['date']);
        $columns = array_merge($columns, array(
            'shortcode' => __('Shortcode'),
            'count' => __('Count'),
            'date' => __('Date'),
        ));
    }
    return $columns;
});



/**
 * Add shortcode to admin posts view
 */

add_action ( 'manage_swg_downloads_posts_custom_column', function($column, $post_id){
    if ($column == 'shortcode') {
        echo '[swg_downloads id="'.$post_id.'"]';
    } else if($column == 'count') {
        if (function_exists('get_field')) {
            echo get_field('downloads', $post_id);
        }
    }
}, 10, 2 );




/**
 * Set up swg_downloads routes and redirect after count
 */
// flush_rules() if our rules are not yet included
add_action( 'wp_loaded', function(){
    $rules = get_option( 'rewrite_rules' );
    if ( ! isset( $rules['^swg_downloads/swg_download_id/([^/]*)/?'] ) ) {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }
});


//Add the rule
add_action('init', function(){
    add_rewrite_tag( '%swg_download_id%', '([^&]+)' );
    add_rewrite_rule( '^swg_downloads/swg_download_id/([^/]*)/?', 'index.php?post_type=swg_downloads&swg_download_id=$matches[1]','top' );
}, 10, 0);


// Adding the id var so that WP recognizes it
add_filter( 'query_vars', function($vars){
    array_push( $vars, 'swg_download_id');
    return $vars;
});


// detect and forward
add_action( 'pre_get_posts', function($query){
    // check if the user is requesting an admin page
    // or current query is not the main query
    if ( is_admin() || ! $query->is_main_query() ){
        return;
    }
    if (!function_exists('get_field')) {
        return;
    }
    $swg_download_id = get_query_var( 'swg_download_id' );
    // add meta_query elements
    if( !empty( $swg_download_id ) ){

        $args = array('p'=>$swg_download_id, 'post_type'=>'swg_downloads', 'limit'=> '1');
        $loop = new WP_Query($args);
        // Start loop for posts
        $loop->the_post();
        $id = get_the_id();
        $count = sanitize_text_field(get_field('downloads',$id));
        if (!$count || is_nan($count)) {
            $count = 0;
        }
        $type = sanitize_text_field(get_field('download_type',get_the_id()));
        if ($type == 'file') {
            $download_file = get_field('download_file',$id);
            $url = $download_file['url'];
        } else {
            $url = get_field('download_url',$id);
        }
        update_field('downloads', $count+1, $id);
        wp_reset_postdata();
        if($url) {
            wp_redirect(sanitize_url($url));
            exit;
        }
    }
}, 1 );
