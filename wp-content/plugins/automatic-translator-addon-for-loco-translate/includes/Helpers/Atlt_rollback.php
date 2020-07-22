<?php
namespace LocoAutoTranslateAddon\Helpers;
/**
 * This class is used to rollback a plugin. It extends wordpress core class Plugin_Upgrader
 * Due to the use of package & namaspace, class can not be extend requiring file
 * 
 */

 if( !defined( 'ABSPATH' )){
     die( 'WordPress enviornment not found!');
 }

 if( !class_exists( 'Plugin_Upgrader' ) ){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
 }

 class Atlt_rollback extends \Plugin_Upgrader{

    public function rollback(){
        
        $key = ( isset( $_POST['key'] ) && !empty( $_POST['key'] ) ) ? $_POST['key'] : null;

        if( $key == null || !wp_verify_nonce($key, 'atlt_nounce_rollback_loco')){
            return 'Nonce verification failed!';
        }

        $this->init();
        $this->upgrade_strings();

        $url = 'https://downloads.wordpress.org/plugin/loco-translate.2.3.3.zip';

        add_filter( 'upgrader_pre_install', array( $this, 'deactivate_plugin_before_upgrade' ), 10, 2 );
        add_filter( 'upgrader_clear_destination', array( $this, 'delete_old_plugin' ), 10, 4 );

        $this->run( array(
            'package'           => $url,
            'destination'       => WP_PLUGIN_DIR,
            'clear_destination' => true,
            'clear_working'     => true,
            'hook_extra'        => array(
                'plugin' => 'loco-translate',
                'type'   => 'plugin',
                'action' => 'update',
            ),
        ) );

        remove_filter( 'upgrader_pre_install', array( $this, 'deactivate_plugin_before_upgrade' ) );
        remove_filter( 'upgrader_clear_destination', array( $this, 'delete_old_plugin' ) );

        if ( ! $this->result || is_wp_error( $this->result ) ) {
            return $this->result;
        }
        return 'Loco Translate rollback successful!';
    }
 }