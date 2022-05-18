<?php
/**
 * Plugin Name: SWG Downloads
 * Description: Simple downloader plugin
 * Version: 1.0.8
 * Author: Eric Griffiths
 * Author URI: https://stonewallgroup.com
 * License: GPLv2 or later
 * Text Domain: swg-downloads
 */


//Load Composer
include( trailingslashit(plugin_dir_path( __FILE__ )) . 'includes/init.php');

//Load Composer
require_once(plugin_dir_path(__FILE__) . '/lib/autoload.php');
/**
 * Set thumb image size.
 */
//add_action ('init', function(){
//    if(function_exists('get_field')) {
//        $w = get_field('thumbnail_image_width','options');
//        $h = get_field('thumbnail_image_height','options');
//    }
//    if (!$w) {
//        $w = 150;
//    }
//    if (!$h) {
//        $h = 100;
//    }
//    add_image_size( 'swg_download_thumb', $w, $h, true );
//});
//
//if(function_exists('get_field')) {
//    $w = get_field('thumbnail_image_width','options');
//    $h = get_field('thumbnail_image_height','options');
//}
//if (!$w) {
//    $w = 150;
//}
//if (!$h) {
//    $h = 100;
//}
//add_image_size( 'swg_download_thumb', $w, $h, true );


/**
 * Add filter for admin posts
 */
add_action('restrict_manage_posts', 'tsm_filter_post_type_by_taxonomy');
function tsm_filter_post_type_by_taxonomy() {
    global $typenow;
    $post_type = 'swg_downloads'; // change to your post type
    $taxonomy = 'swg_download_category'; // change to your taxonomy
    if ($typenow == $post_type) {
        $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
        $info_taxonomy = get_taxonomy($taxonomy);
        wp_dropdown_categories(array(
            'show_option_all' => sprintf(__('Show all %s', 'swg-downloads'), $info_taxonomy->label),
            'taxonomy' => $taxonomy,
            'name' => $taxonomy,
            'orderby' => 'name',
            'selected' => $selected,
            'show_count' => true,
            'hide_empty' => true,
        ));
    };
}
add_filter('parse_query', 'tsm_convert_id_to_term_in_query');
function tsm_convert_id_to_term_in_query($query) {
    global $pagenow;
    $post_type = 'swg_downloads'; // change to your post type
    $taxonomy = 'swg_download_category'; // change to your taxonomy
    $q_vars = &$query->query_vars;
    if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
        $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
        $q_vars[$taxonomy] = $term->slug;
    }
}





/**
 * locate template file
 */
function swg_locate_template($template) {
    if(file_exists(trailingslashit(get_stylesheet_directory()).'swg_plugins/swg_downloads/'.$template)) {
        $located = trailingslashit(get_stylesheet_directory()).'swg_plugins/swg_downloads/'.$template;
    } elseif (file_exists(trailingslashit(get_template_directory()).'swg_plugins/swg_downloads/'.$template)) {
        $located = trailingslashit(get_template_directory()).'swg_plugins/swg_downloads/'.$template;
    } else {
        $located = trailingslashit(plugin_dir_path( __FILE__ )).'views/'.$template;
    }
    return $located;
}




/**
 * Build the template
 */
function swg_get_temp($id,$temp='single.php',$w,$h) {

//    if (get_field('download_type',$id) == 'file') {
//        $file = get_field('download_file',$id);
//        $url = $file['url'];
//    } else {
//        $url  = get_field('download_url',$id);
//    }
    $url = trailingslashit(get_site_url()).'swg_downloads/swg_download_id/'.$id;

    $img = get_field('thumbnail',$id);


    $dl['url']         =  $url;
    $dl['thumbnail']   =  $img;
    $dl['width']       =  $w;
    $dl['height']      =  $h;
    $dl['button_text'] =  get_field('button_text',$id);
    $dl['title']       =  get_the_title($id);
    $dl['downloads']   =  get_field('downloads',$id); ///Not needed?
    $template = swg_locate_template($temp);
    if (!$template) {
        swg_locate_template('single.php');
    }
    return load_template($template,false, $dl);
}






/**
 * Add shortcode for 1 download or whole cat
 */
function swg_shortcodes_init(){
    add_shortcode('swg_downloads', 'swg_downloads');
}
add_action('init', 'swg_shortcodes_init');
function swg_downloads($atts) {
    if (function_exists('get_field')) {
        $width = get_field('swg_thumbnail_image_width','options');
        $height = get_field('swg_thumbnail_image_height','options');
        $atts = shortcode_atts(
            array(
                'id'        => '',
                'cat'       => '',
                'col'      => '',
                'template'  => '',
                'break'     => ''
            ), $atts, 'swg_downloads');
        if ($atts['id']) {
            if (!$atts['template']) {
                $atts['template'] = 'single.php';
            }
            ob_start();
            echo swg_get_temp($atts['id'],$atts['template'],$width,$height);
            return ob_get_clean();;
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
            echo '<div class="container"><div class="row justify-content-center">';
            if ($query->posts && count($query->posts) > 0) {
                foreach ($query->posts as $post) {
                    echo '<div class="'.$col.' mb-4">';
                    echo swg_get_temp($post->ID,$atts['template'],$width,$height);
                    echo '</div>';
                }
            }
            echo '</div></div>';
            return ob_get_clean();
        }
    } else {
        return 'Please enable ACF Pro';
    }

}







/**
 * Add column to admin posts view
 */
function add_swg_downloads_columns ( $columns ) {
    unset($columns['date']);
    $columns = array_merge ( $columns, array (
        'shortcode' => __('Shortcode'),
        'date'                => __('Date'),
    ) );
    return $columns;
}
add_filter ( 'manage_swg_downloads_posts_columns', 'add_swg_downloads_columns' );






/**
 * Add shortcode to admin posts view
 */
function swg_downloads_custom_column ( $column, $post_id ) {
    if ($column == 'shortcode') {
        echo '[swg_downloads id="'.$post_id.'"]';
    }
}
add_action ( 'manage_swg_downloads_posts_custom_column', 'swg_downloads_custom_column', 10, 2 );






/**
 * Set up swg_downloads routes and redirect after count
 */


// flush_rules() if our rules are not yet included
add_action( 'wp_loaded', 'swg_flush_rules' );
function swg_flush_rules() {
    $rules = get_option( 'rewrite_rules' );

    if ( ! isset( $rules['^swg_downloads/swg_download_id/([^/]*)/?'] ) ) {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }
}


add_action('init', 'swg_custom_rewrite_rule', 10, 0);
function swg_custom_rewrite_rule() {
    add_rewrite_tag( '%swg_download_id%', '([^&]+)' );
    add_rewrite_rule( '^swg_downloads/swg_download_id/([^/]*)/?', 'index.php?post_type=swg_downloads&swg_download_id=$matches[1]','top' );

}

// Adding the id var so that WP recognizes it
add_filter( 'query_vars', 'swg_insert_query_vars' );
function swg_insert_query_vars( $vars ) {
    array_push( $vars, 'swg_download_id');
    return $vars;
}

// detect and forward
add_action( 'pre_get_posts', 'swg_pre_get_posts', 1 );
function swg_pre_get_posts( $query ) {
    // check if the user is requesting an admin page
    // or current query is not the main query
    if ( is_admin() || ! $query->is_main_query() ){
        return;
    }

    $swg_download_id = get_query_var( 'swg_download_id' );

    // add meta_query elements
    if( !empty( $swg_download_id ) ){


        $args = array('p'=>$swg_download_id, 'post_type'=>'swg_downloads', 'limit'=> '1');

        $loop = new WP_Query($args);
        // Start loop for seminar posts
        $loop->the_post();

        $id = get_the_id();
        $count = get_field('downloads',$id);
        if (!$count || is_nan($count)) {
            $count = 0;
        }
        $type = get_field('download_type',get_the_id());
        if ($type == 'file') {
            $download_file = get_field('download_file',$id);
            $url = $download_file['url'];
        } else {
            $url = get_field('download_url',$id);
        }


        update_field('downloads', $count+1, $id);
        wp_reset_postdata();
        if($url) {
            wp_redirect($url);
            exit;
        }
    }

}






add_action('plugins_loaded', function(){
    //Setup update check
    $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
        'https://stonewalldigital.com/repo/wordpress/plugins/swg_downloads/plugin.json',
        __FILE__,
        'swg-downloads'
    );
});
