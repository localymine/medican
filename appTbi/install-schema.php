<?php

ini_set('display_errors', 1);
require 'db.php';

/* creating table for app data for different stores */
$logs = "CREATE TABLE IF NOT EXISTS `$log_table` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `shopify_shop` text,
        `shopify_token` text,
        `status` text,
        `code` text,
        `time` text,
        `confirm_url` text,
        `api_client_id` text,
        `created_at` text,
         PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
$con->query($logs);

/* creating table for app data for different stores */
$configurations = "CREATE TABLE IF NOT EXISTS `$config_table` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `fb_appid` text,
        `fb_appsecret` text,
        `fb_customerkey` text,
        `fb_customersecret` text,
        `twitter_consumerkey` text,
        `twitter_consumersecret` text,
        `twitter_customerkey` text,
        `twitter_customersecret` text,
        `pinterest_appid` text,
        `pinterest_appsecret` text,
        `pinterest_customerkey` text,
        `pinterest_customersecret` text,
        `tumblr_consumerkey` text,
        `tumblr_consumersecret` text,
        `tumblr_customerkey` text,
        `tumblr_customersecret` text,
        `log_id` int(11),
         PRIMARY KEY (`id`),
         UNIQUE KEY (`log_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
$con->query($configurations);

/* creating table for app data for different stores */
$settings = "CREATE TABLE IF NOT EXISTS `$settings_table` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `last_post` varchar(255),
        `hash_tag` text,
        `tag_option` text,
        `post_time` varchar(255),
        `log_id` int(11),
         PRIMARY KEY (`id`),
         UNIQUE KEY (`log_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
$con->query($settings);