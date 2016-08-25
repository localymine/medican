<?php

if (class_exists('WPLessPlugin')) {
    $less = WPLessPlugin::getInstance();
    $options = get_option(AZEXO_THEME_NAME);
    if (isset($options['brand-color']))
        $less->addVariable('brand-color', $options['brand-color']);
}