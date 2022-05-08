<?php
/**
 * Check for acf pro plugin.
 */
add_action('admin_notices', function() {
    $plugin_messages = array();
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    $aRequired_plugins = array(
        array('name'=>'Advanced Custom Fields Pro', 'download'=>'https://www.advancedcustomfields.com/pro/', 'path'=>'advanced-custom-fields-pro/acf.php'),
    );
    foreach($aRequired_plugins as $aPlugin) {
        if(!is_plugin_active( $aPlugin['path'] )) {
            $plugin_messages[] = '<div class="notice notice-error"> <p>The SWG Downloads plugin recommends you to install the <a href="/wp-admin/plugin-install.php?s='.urlencode($aPlugin['name']).'&amp;tab=search&amp;type=term">'.$aPlugin['name'].'</a> plugin.  <a target="_blank" href="'.$aPlugin['download'].'">View site.</a></p></div>';
        }
    }
    if(count($plugin_messages) > 0) {
        foreach($plugin_messages as $message) {
            echo '
                '.$message.'
            ';
        }
    }
});

/**
 * Init CPT and category.
 */
add_action( 'init', function() {

    $labels = [
        "name" => __( "Downloads", "swg-downloads" ),
        "singular_name" => __( "Download", "swg-downloads" ),
    ];
    $args = [
        "label" => __( "Downloads", "swg-downloads" ),
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "show_in_rest" => true,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "has_archive" => false,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "delete_with_user" => false,
        "exclude_from_search" => false,
        "capability_type" => "post",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "can_export" => false,
        "rewrite" => [ "slug" => "swg_downloads", "with_front" => true ],
        "query_var" => true,
        "menu_position" => 25,
        "menu_icon" => "dashicons-arrow-down-alt",
        "supports" => [ "title" ],
        "show_in_graphql" => false,
    ];
    register_post_type( "swg_downloads", $args );
    //Download Cats
    $labels = array(
        'name' => __( 'Categories' , 'swg-downloads' ),
        'singular_name' => __( 'Category', 'swg-downloads' ),
        'search_items' => __( 'Search Categories' , 'swg-downloads' ),
        'all_items' => __( 'All Categories' , 'swg-downloads' ),
        'edit_item' => __( 'Edit Category' , 'swg-downloads' ),
        'update_item' => __( 'Update Categories' , 'swg-downloads' ),
        'add_new_item' => __( 'Add New Category' , 'swg-downloads' ),
        'new_item_name' => __( 'New Category Name' , 'swg-downloads' ),
        'menu_name' => __( 'Categories' , 'swg-downloads' ),
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'sort' => true,
        'args' => array( 'orderby' => 'term_order' ),
        'rewrite' => array( 'slug' => 'swg-download-category' ),
        'show_admin_column' => true,
        'show_in_rest' => true

    );
    register_taxonomy( 'swg_download_category', array( 'swg_downloads' ), $args);
});

/**
 * Init ACF Fields
 */
add_action('acf/init', 'register_swg_downloads_post_groups');

function register_swg_downloads_post_groups() {
    //ACF Custom Fields
    if( function_exists('acf_add_local_field_group') ):
        acf_add_local_field_group(array(
            'key' => 'group_626c7e8ad3a92',
            'title' => 'Download Plugin',
            'fields' => array(
                array(
                    'key' => 'field_626c7eb999042',
                    'label' => 'Download Type',
                    'name' => 'download_type',
                    'type' => 'radio',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        'file' => 'File',
                        'link' => 'Link',
                    ),
                    'allow_null' => 0,
                    'other_choice' => 0,
                    'default_value' => 'file',
                    'layout' => 'horizontal',
                    'return_format' => 'value',
                    'save_other_choice' => 0,
                ),
                array(
                    'key' => 'field_626c7ff599043',
                    'label' => 'File',
                    'name' => 'download_file',
                    'type' => 'file',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_626c7eb999042',
                                'operator' => '==',
                                'value' => 'file',
                            ),
                        ),
                    ),
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'return_format' => 'array',
                    'library' => 'all',
                    'min_size' => '',
                    'max_size' => 64,
                    'mime_types' => '',
                ),
                array(
                    'key' => 'field_626c806799044',
                    'label' => 'Download URL',
                    'name' => 'download_url',
                    'type' => 'url',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_626c7eb999042',
                                'operator' => '==',
                                'value' => 'link',
                            ),
                        ),
                    ),
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                ),
                array(
                    'key' => 'field_626c810e97f2b',
                    'label' => 'Thumbnail Image',
                    'name' => 'thumbnail',
                    'type' => 'image',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                    'library' => 'all',
                    'min_width' => 50,
                    'min_height' => 50,
                    'min_size' => '',
                    'max_width' => '',
                    'max_height' => '',
                    'max_size' => 24,
                    'mime_types' => '',
                ),
                array(
                    'key' => 'field_626c81df97f2c',
                    'label' => 'Button Text',
                    'name' => 'button_text',
                    'type' => 'text',
                    'instructions' => 'Text for download button',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 'Download',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => 30,
                ),
                array(
                    'key' => 'field_6276d912a2877',
                    'label' => 'Download Count',
                    'name' => 'downloads',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'swg_downloads',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
        ));

        acf_add_local_field_group(array(
            'key' => 'group_626c8545e3a32',
            'title' => 'SWG Download Settings',
            'fields' => array(
                array(
                    'key' => 'field_626c85cff7c30',
                    'label' => 'SWG Download Image',
                    'name' => '',
                    'type' => 'tab',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'placement' => 'left',
                    'endpoint' => 0,
                ),
                array(
                    'key' => 'field_626c8594c51d8',
                    'label' => 'Thumbnail image width',
                    'name' => 'swg_thumbnail_image_width',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 150,
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),
                array(
                    'key' => 'field_626c8552c51d7',
                    'label' => 'Thumbnail image height',
                    'name' => 'swg_thumbnail_image_height',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => 100,
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),

            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'theme-general-settings',
                    ),
                ),
            ),
            'menu_order' => 10,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
        ));

    endif;
}