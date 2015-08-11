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

        if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) && $host = $_SERVER['HTTP_X_FORWARDED_HOST']) {
            $elements = explode(',', $host);

            $host = trim(end($elements));
        } else {
            if (!$host = $_SERVER['HTTP_HOST']) {
                if (!$host = $_SERVER['SERVER_NAME']) {
                    $host = !empty($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
                }
            }
        }

        // Remove port number from host
        $host = preg_replace('/:\d+$/', '', $host);

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
        return trim($protocol . '://' . $host . parse_url($siteUrl, PHP_URL_PATH));

    }
    static function abs($url){
        return self::getDomain($url);
    }
    static function rel($url){
        /*$output= preg_replace("/(\\/)[^\\/]*$/",'',SITECOOKIEPATH);
        if($output=="") $output="/";
        return $output;*/
        $newUrl=self::getDomain($url);
        $newUrl =parse_url($newUrl, PHP_URL_PATH);
        if($newUrl=="") $newUrl="/";
        return $newUrl;
    }
    static function test($url){
        $x=true;
        flush ();
        return $url;
    }
    static function style_loader_src($url){
        $urlPath = parse_url($url, PHP_URL_PATH);
        return $urlPath;

        //remove base url
        $contentPath = parse_url(WP_CONTENT_URL, PHP_URL_PATH);
        if(substr_count($url,$contentPath)>1) {
            $x = true;
            $pos = strpos($url, $contentPath);
            if ($pos !== false) {
                return substr_replace($url, '', $pos, strlen($contentPath));
            }
        }
        return $url;

        //return preg_replace('/' .parse_url(WP_CONTENT_URL, PHP_URL_PATH) .'/','',$url,1);

    }
    static function wp_defaults( $args ) {
        $args->content_url=parse_url($args->content_url,PHP_URL_PATH);
        return $args;
    }
}
add_action('wp_default_styles', 'DomainAgnostic::wp_defaults');
add_action('wp_default_scripts', 'DomainAgnostic::wp_defaults');
add_filter ( 'option_siteurl', 'DomainAgnostic::abs' );
add_filter ( 'option_home', 'DomainAgnostic::abs' );
add_filter ( 'option_ossdl_off_cdn_url', 'DomainAgnostic::abs' );
add_filter ( 'set_url_scheme', 'DomainAgnostic::rel' );
/*add_filter ( 'stylesheet_uri', 'DomainAgnostic::test' );
add_filter ( 'stylesheet_directory_uri', 'DomainAgnostic::test' );
add_filter ( 'template_directory_uri', 'DomainAgnostic::test' );*/
//add_filter ( 'theme_root_uri', 'DomainAgnostic::test' );
//add_filter ( 'style_loader_src', 'DomainAgnostic::style_loader_src' );


