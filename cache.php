<?php

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'FACETWP_CACHE', true );

$action = isset( $_POST['action'] ) ? $_POST['action'] : '';
$data = isset( $_POST['data'] ) ? $_POST['data'] : array();
$no_cache_capabilities = apply_filters('facetwp_cache_nocache_capabilities',array());

//Check to see if current user should not be returned cached data
$cache_enabled = true;
if(count($no_cache_users)){
    foreach($no_cache_capabilities AS $capability){
        if(current_user_can($capability)){
            $cache_enabled = false;
            break;
        }
    }
}

if ( 'facetwp_refresh' == $action && $cache_enabled) {

    global $table_prefix;
    $wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
    $wpdb->prefix = $table_prefix;

    // Timestamp
    $now = date( 'Y-m-d H:i:s' );

    // MD5 hash
    $cache_name = md5( json_encode( $data ) );

    // Check for a cached version
    $sql = "
    SELECT value
    FROM {$wpdb->prefix}facetwp_cache
    WHERE name = '$cache_name' AND expire >= '$now'
    LIMIT 1";
    $value = $wpdb->get_var( $sql );

    // Return cached version and EXIT
    if ( null !== $value ) {
        echo $value;
        exit;
    }
}
