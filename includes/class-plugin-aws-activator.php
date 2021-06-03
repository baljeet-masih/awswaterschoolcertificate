<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Your Name <email@example.com>
 */
class Plugin_Name_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	 
	 
	 
	public static function activate() {
	   
            ///////// save default plugin settings
         update_option('sendgrid_mail_from','dummy@gmail.com');                                                                          ///// 1
         update_option('sendgrid_mail_from_name','dummy');                                                                               ///// 2
         update_option('sendgrid_hostname','smtp.sendgrid.net');                                                                         ///// 3
         update_option('sendgrid_username','dummy');                                                                                     ///// 4
         update_option('sendgrid_password','SG.xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');                           ///// 5
         update_option('sendgrid_mail_subject','Dummy School Certificate');                                                   ///// 6
         $pageID = get_option('page_on_front');                                                                                          ///// 7
         update_option('aws_certificate_page',$pageID);                                                                                  ///// 8
      
         
        /////////////////// create database table when install user this pluugin
    
         	// create the ECPT metabox database table 
        
        	         global $wpdb;
                    $table = $wpdb->prefix . 'aws_things';
                    $charset = $wpdb->get_charset_collate();
                    $charset_collate = $wpdb->get_charset_collate();
                    $sql = "CREATE TABLE $table (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    course_id mediumint(20) NOT NULL,
                    attendee_id mediumint(20) NOT NULL,
                    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                    attended tinytext NOT NULL,
                    first_name tinytext NOT NULL,
                    last_Name text NOT NULL,
                    email varchar(255) DEFAULT '' NOT NULL,
                    address varchar(255) DEFAULT '' NOT NULL,
                    city varchar(255) DEFAULT '' NOT NULL,
                    country_region varchar(255) DEFAULT '' NOT NULL,
                    phone varchar(255) DEFAULT '' NOT NULL,
                    organization varchar(255) DEFAULT '' NOT NULL,
                    job_title varchar(255) DEFAULT '' NOT NULL,
                    questions_comments varchar(255) DEFAULT '' NOT NULL,
                    registration_time varchar(255) DEFAULT '' NOT NULL,
                    approval_status varchar(255) DEFAULT '' NOT NULL,
                    join_time varchar(255) DEFAULT '' NOT NULL,
                    leave_time varchar(255) DEFAULT '' NOT NULL,
                    time_in_session varchar(255) DEFAULT '' NOT NULL,
                    country varchar(255) DEFAULT '' NOT NULL,
                    course_id varchar(255) DEFAULT '' NOT NULL,
                    PRIMARY KEY  (id)
                    ) $charset_collate;";
            
                    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                    dbDelta( $sql );
        	
         
         
         
         
	}
   
}

