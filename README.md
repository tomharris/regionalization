# Regionalization

A WordPress plugin to add region taxonomies to users and posts. Helpers are also provided to filter posts to those that are in the same region as the signed-in user.

## Installation

* Install the [user-taxonomies](http://gostomski.co.uk/code/wordpress-user-taxonomies) plugin
* Install Regionalization

## Use

`get_current_user_region()` returns the region for the current signed-in user.

`query_posts_with_current_user_region()` can be called like `query_posts()`.

```php
<?php
// Replace
query_posts("cat=$cat&showposts=$num");

// With
query_posts_with_current_user_region("cat=$cat&showposts=$num");
?>
```

`nav_menu_region_filtered` is also provided to support filtering menu items by the current user's region. Simply:
* Add the slug name of the region to the menu's description field
* Replace with `wp_nav_menu` with `nav_menu_region_filtered`

```php
<?php
// Replace
wp_nav_menu(array('theme_location' => 'main_menu', 'container_class' => 'menu-header'));

// With
nav_menu_region_filtered(array('theme_location' => 'main_menu', 'container_class' => 'menu-header'));
?>
```
