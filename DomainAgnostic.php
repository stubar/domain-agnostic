<?php
/*
Plugin Name: Domain Agnostic
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Allows quick copying of a DB to different domains.
Version: 1.0
Author: Stuart Barker
Author URI: http://www.caracaldigital.com
License:  GPL2
*/

class DomainAgnostic {
    static function getDomain($siteUrl) {
        //takes the domain that comes from the DB ($siteUrl) and replaces the hostname with the real host.
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && $host = $_SERVER['HTTP_X_FORWARDED_HOST'])
        {
            $elements = explode(',', $host);

            $host = trim(end($elements));
        }
        else
        {
            if (!$host = $_SERVER['HTTP_HOST'])
            {
                if (!$host = $_SERVER['SERVER_NAME'])
                {
                    $host = !empty($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
                }
            }
        }

        // Remove port number from host
        $host = preg_replace('/:\d+$/', '', $host);

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";

        return trim($protocol . '://' . $host . parse_url($siteUrl, PHP_URL_PATH));

    }

}


add_filter ( 'option_siteurl', 'DomainAgnostic::getDomain' );
add_filter ( 'option_home', 'DomainAgnostic::getDomain' );
add_filter ( 'option_ossdl_off_cdn_url', 'DomainAgnostic::getDomain' );
