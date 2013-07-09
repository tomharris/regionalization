<?php
/*
Plugin Name: Regionalization
Plugin URI: https://github.com/tomharris/regionalization
Description: Adds support for a region taxonomy to posts with additional helpers.
Version: 1.0.0
Author: Tom Harris
Author URI: http://harrissoftworks.com
License: MIT
*/

/*
The MIT License (MIT)

Copyright (c) 2013 Tom Harris

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

/*-----------------------------------------------------------------------------------*/
/* Taxonamy Support
/*-----------------------------------------------------------------------------------*/

// Register Custom Taxonomy
function region_taxonomy() {
  $labels = array(
    'name'                       => 'Regions',
    'singular_name'              => 'Region',
    'menu_name'                  => 'Regions',
    'all_items'                  => 'All Regions',
    'parent_item'                => 'Parent Region',
    'parent_item_colon'          => 'Parent Region:',
    'new_item_name'              => 'New Region Name',
    'add_new_item'               => 'Add New Region',
    'edit_item'                  => 'Edit Region',
    'update_item'                => 'Update Region',
    'separate_items_with_commas' => 'Separate regions with commas',
    'search_items'               => 'Search region',
    'add_or_remove_items'        => 'Add or remove regions',
    'choose_from_most_used'      => 'Choose from the most used regions',
  );

  $rewrite = array(
    'with_front'                 => true,
    'slug'                       => 'region',
  );

  $args = array(
    'labels'                     => $labels,
    'rewrite'                    => $rewrite,
    'hierarchical'               => true,
    'public'                     => true,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => true,
  );

  register_taxonomy( 'region', 'post', $args );
}

// Hook into the 'init' action
add_action( 'init', 'region_taxonomy', 0 );


// Register Custom Taxonomy for users
function user_region_taxonomy() {
  $labels = array(
    'name'                       => 'Regions',
    'singular_name'              => 'Region',
    'menu_name'                  => 'Regions',
    'all_items'                  => 'All Regions',
    'parent_item'                => 'Parent Region',
    'parent_item_colon'          => 'Parent Region:',
    'new_item_name'              => 'New Region Name',
    'add_new_item'               => 'Add New Region',
    'edit_item'                  => 'Edit Region',
    'update_item'                => 'Update Region',
    'separate_items_with_commas' => 'Separate regions with commas',
    'search_items'               => 'Search region',
    'add_or_remove_items'        => 'Add or remove regions',
    'choose_from_most_used'      => 'Choose from the most used regions',
  );

  $rewrite = array(
    'with_front'                 => true,
    'slug'                       => 'user_region',
  );

  $capabilities = array(
    'manage_terms'                => 'edit_users',
    'edit_terms'                  => 'edit_users',
    'delete_terms'                => 'edit_users',
    'assign_terms'                => 'read',
  );

  $args = array(
    'labels'                     => $labels,
    'rewrite'                    => $rewrite,
    'capabilities'               => $capabilities,
    'hierarchical'               => true,
    'public'                     => true,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => true,
  );

  register_taxonomy( 'user_region', 'user', $args );
}

// Hook into the 'init' action
add_action( 'init', 'user_region_taxonomy', 0 );


/*-----------------------------------------------------------------------------------*/
/* Custom Menu Walker
/*-----------------------------------------------------------------------------------*/

class description_walker extends Walker_Nav_Menu {
  function start_el(&$output, $item, $depth, $args) {
    if($item->description === $args->user_region || $depth != 0) {
      $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

      $class_names = $value = '';

      $classes = empty( $item->classes ) ? array() : (array) $item->classes;
      $classes[] = 'menu-item-' . $item->ID;

      $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
      $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

      $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
      $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

      $output .= $indent . '<li' . $id . $value . $class_names .'>';

      $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
      $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
      $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
      $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

      $item_output = $args->before;
      $item_output .= '<a'. $attributes .'>';
      $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
      $item_output .= '</a>';
      $item_output .= $args->after;

      $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
  }
}


/*-----------------------------------------------------------------------------------*/
/* Helpers
/*-----------------------------------------------------------------------------------*/

function get_current_user_region() {
  $user_terms = wp_get_object_terms(get_current_user_id(), 'user_region');
  return $user_terms[0]->slug;
}

function query_posts_with_current_user_region($query_string) {
  $carousel_query = wp_parse_args($query_string);
  $carousel_query['tax_query'] = array(
    array(
      'taxonomy' => 'region',
      'terms' => get_current_user_region(),
      'field' => 'slug',
    ),
  );
  query_posts($carousel_query);
}

function nav_menu_region_filtered($args) {
  $args['user_region'] = get_current_user_region();
  $args['walker'] = new description_walker();

  wp_nav_menu($args);
}
?>
