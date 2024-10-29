<?php
/*
Plugin Name: AC STOP Content Copier
Plugin URI: http://adaptcoder.com/wp-stop-content-Copier
Description: This plugin helps stopping others steal your content as it would be their right to do so!
Author: Adapt Coder
Version: 1.0
Author URI: http://adaptcoder.com
*/

class AC_Stop_Content_Copier {

	public $IP;
	public $DB;
	public $LogTable;
	public $time_now;
	public $plugin_permalink;

	public function __construct() {

		// set plugin permalink
		$this->plugin_permalink = 'acbds-settings';

		// set time now
		$this->time_now = time();

		// set $WPDB variable
		global $wpdb;
		$this->DB = $wpdb;

		// set DB table name
		$this->LogTable = $wpdb->prefix . 'acbdlog';

		// set user IP
		$this->IP = $this->getRealIP();

		// install plugin DB
		register_activation_hook( __FILE__, array( $this, 'installDB' ));

		// enable session support
		add_action( 'init', array( $this, 'start_session' ));

		// log this visit
		add_action( 'init', array( $this, 'logVisit' ));

		// show captcha or leave user continue
		add_action( 'template_redirect', array( $this, 'show_captcha' ));

		// shortcode
		add_shortcode( 'acbd_show_captcha', array( $this, 'shortcode' ));

		// load plugin admin CSS
		add_action( 'admin_print_scripts', array( $this, 'loadCSS' ));

		// add administration menu
 		add_action( 'admin_menu', array( $this, 'settings' ));

	}

	/* Enable session support */
	public function start_session() {

		if( !session_id() ) {
			ob_start();
			session_start();
		}

	}


	/* Get Real IP */
	public function getRealIP()
	{
		$ipaddress = '';

	    if (isset($_SERVER['HTTP_CLIENT_IP']))
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    else if(isset($_SERVER['REMOTE_ADDR']))
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';

	    return $ipaddress;
	}

	/* Log visit to DB for behaviour detection */
	public function logVisit() {

		$is_session_recorded_already = $this->DB->get_var( $this->DB->prepare( 
										"SELECT visitID FROM " . $this->LogTable . " 
									     WHERE visit_IP = %s AND session_ID = %s", $this->IP, session_id() ));

		if( !$is_session_recorded_already ) :
			
			$visit = array( 'visit_time' => $this->time_now, 
							'visit_IP' => $this->IP, 
							'session_id' => session_id() );

			return $this->DB->insert( $this->LogTable, $visit );

		endif;

	}

	/* Install DB Table required for the Plugin to work */
	public function installDB() {

		$this->DB->query( "CREATE TABLE IF NOT EXISTS `" . $this->LogTable . "` (
						`visitID` int(10) unsigned NOT NULL,
						`visit_IP` varchar(15) NOT NULL,
						`visit_time` int(11) NOT NULL,
						`session_id` varchar(255) NOT NULL
						) ENGINE=InnoDB DEFAULT CHARSET=utf8;" );

		// Create post object
		$my_post = array(
		  'post_title'    => 'WP Stop Content Copier Captcha',
		  'post_content'  => '[acbd_show_captcha]',
		  'post_status'   => 'publish',
		  'post_author'   => 1,
		  'post_type' 	  => 'page'
		);

		// Insert the post into the database
		$pageID = wp_insert_post( $my_post );

		update_option( 'acbd_captcha_page', $pageID );

	}

	/* WP Admin plugin settings */
	public function settings() {
		add_menu_page( 'Stop Copier', 'Stop Copier', 'manage_options', $this->plugin_permalink, array( $this, 'plugin_options' ), plugin_dir_url(__FILE__) . 'static/plugin-icon.png' );
	}

	public function plugin_options() {

		if( isset( $_POST['sb'] )) {
			update_option( 'acbd-seconds', intval($_POST['acbd-seconds']) );
			$message = 'OK';
		}

		include_once 'inc/settings.php';

	}

	/* Load Bootstrap inside admin panel */
	public function loadCSS() {
		wp_register_style( 'acbdCSS', plugin_dir_url(__FILE__) . '/static/acbd.css' );
		wp_enqueue_style( 'acbdCSS' );
	}

	/* Log plugin activity for user presentation */
	public function log( $due_to ) {

		$log = 'IP ' . $this->IP . ' asked for captcha due to ' . $due_to . '';
		add_post_meta( get_option('acbd_captcha_page'), 'acbd-log', $log );

	}

	/* Check if IP was presented a CAPTCHA prior to this visit and successfully complted it */
	public function is_ip_unlocked() {
		$unlocked = get_post_meta( get_option( 'acbd_captcha_page' ), $this->IP, TRUE );

		if( empty( $unlocked ))
			return FALSE;
		else
			return TRUE;
	}	

	/* Detect too fast browsing: specific to bots */
	public function check_fast_browse() {

		if( array_key_exists( 'acbd_last_time', $_SESSION )) {

			if( (time()-$_SESSION['acbd_last_time']) < get_option( 'acbd-seconds', 3 ) ) {

				$page_id = get_queried_object_id();
				if( $page_id != get_option('acbd_captcha_page') ) :

					$_SESSION['acbd_captcha_required'] = 'yes';
					$this->log( 'Too fast browsing' );

					echo '<meta http-equiv="refresh" content="0; url=' . get_bloginfo('url') . '?page_id=' . get_option('acbd_captcha_page') . '"/>';
					exit;

				endif;

			}else{
				$_SESSION['acbd_last_time'] = time();
			}
			
		}else{
			$_SESSION['acbd_last_time'] = time();
		}

	}

	/* basic : scrapper user agent ? */
	public function scraper_ua() {

		$scrapper_agents = array('libww-perl', 'cURL', 'scrapy');

		if(isset($_SERVER['HTTP_USER_AGENT'])) {

			foreach( $scrapper_agents as $ua ) {
				if( stristr( $_SERVER['HTTP_USER_AGENT'], $ua )) {
					return true;
				}
			}
		}

		return false;

	}

	/* Is this visit legit */
	public function show_captcha() {

		// set page id
		$page_id = get_queried_object_id();

		// if IP unlocked skip
		if( $this->is_ip_unlocked() ) return false;

		// if too fast browser
		$this->check_fast_browse();

		// is this a scraper 
		if( $this->scraper_ua() ) {
			if( $page_id != get_option('acbd_captcha_page') ) :

				$_SESSION['acbd_captcha_required'] = 'yes';
				
				$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'no user agent passed';

				$this->log( 'Scraper User Agent detected: ' . $ua );

				echo '<meta http-equiv="refresh" content="0; url=' . get_bloginfo('url') . '?page_id=' . get_option('acbd_captcha_page') . '"/>';
				exit;

			endif;
		}

		// set the three days search vars
		$yesterday = strtotime( '-1 Day' , $this->time_now);
		$two_days_ago = strtotime( '-2 Days' , $this->time_now);
		$three_days_ago = strtotime( '-3 Days' , $this->time_now);

		// search visit in DB
		$yesterday = $this->DB->get_var("SELECT visitID FROM " . $this->LogTable . " WHERE visit_time = $yesterday");
		$two_days_ago = $this->DB->get_var("SELECT visitID FROM " . $this->LogTable . " WHERE visit_time = $two_days_ago");
		$three_days_ago = $this->DB->get_var("SELECT visitID FROM " . $this->LogTable . " WHERE visit_time = $three_days_ago");

		// compare and take action
		if( $page_id != get_option('acbd_captcha_page') ) :
		if( $yesterday AND $two_days_ago AND $three_days_ago ) {
			$_SESSION['acbd_captcha_required'] = 'yes';
			$this->log( 'Behaviour Detection: exact same hour, minute, second visit habit' );

			echo '<meta http-equiv="refresh" content="0; url=' . get_bloginfo('url') . '?page_id=' . get_option('acbd_captcha_page') . '"/>';
			exit;
		}
		endif;

		// if required to enter captcha
		if( isset( $_SESSION['acbd_captcha_required'] ) AND $page_id != get_option('acbd_captcha_page') ) {
			echo '<meta http-equiv="refresh" content="0; url=' . get_bloginfo('url') . '?page_id=' . get_option('acbd_captcha_page') . '"/>';
			exit;
		}

	}

	/* Captcha shortcode */
	public function shortcode() {

		$ret = '';

		if( !isset( $_SESSION['acbd_captcha_required'] )) {
			return 'You will only be able to unlock your IP if required';
		}

		if( isset( $_POST['enter_captcha'] )) :
			if( trim(strip_tags($_POST['enter_captcha'])) == $_SESSION['captcha']['code'] ) {
				// unblock this user forever
				add_post_meta( get_option( 'acbd_captcha_page' ), $this->IP, 'unblocked' );
				add_post_meta( get_option( 'acbd_captcha_page' ), 'acbd-log', 'IP ' . $this->IP . ' unblocked for successfully completing captcha code.' );

				// redirect to home
				return '<meta http-equiv="refresh" content="0; url=' . get_bloginfo('url') . '" />';
			}else{

				require_once "simple-php-captcha-master/simple-php-captcha.php";
				$_SESSION['captcha'] = simple_php_captcha();

				$ret .= '<p style="color:#ff0000;">Invalid code. Try again!</p>';
				$ret .= '<img src="' . $_SESSION['captcha']['image_src'] . '" alt="CAPTCHA code"><br />';
				$ret .= '<form method="POST">';
				$ret .= '<input type="text" name="enter_captcha" />';
				$ret .= '<input type="submit" name="sb_captcha" value="Submit">';
				$ret .= '</form>';
			}
		else: 

			require_once "simple-php-captcha-master/simple-php-captcha.php";
			$_SESSION['captcha'] = simple_php_captcha();
			$ret .= '<img src="' . $_SESSION['captcha']['image_src'] . '" alt="CAPTCHA code"><br />';
			$ret .= '<form method="POST">';
			$ret .= '<input type="text" name="enter_captcha" />';
			$ret .= '<input type="submit" name="sb_captcha" value="Submit">';
			$ret .= '</form>';

		endif;

		return $ret;
	}


}

new AC_Stop_Content_Copier();