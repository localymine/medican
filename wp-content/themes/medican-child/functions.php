<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

add_action('after_setup_theme', 'azexo_load_childtheme_languages', 5);

function azexo_load_childtheme_languages() {
    /* this theme supports localization */
    load_child_theme_textdomain('medican', get_stylesheet_directory() . '/languages');
}

/* Please add your custom functions code below this line. */

