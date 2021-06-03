<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

 
  
class Plugin_Name_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
         add_action( 'init',  [ $this, 'custom_post_type' ] );
		 add_filter( 'views_edit-attendees',[ $this, 'add_csv_html' ]  );
		 add_filter( 'admin_footer',[ $this, 'loader_setin_footer' ]  );
		 add_filter( 'wp_ajax_upload_attendees',[ $this, 'upload_attendees' ]  );
	   	 add_filter( 'wp_ajax_nopriv_upload_attendees',[ $this, 'upload_attendees' ]  );
	   	 
	   	 add_filter( 'manage_attendees_posts_columns', [ $this,'attendeescolumns'] );
	   	 
	   	 add_filter( 'manage_course_posts_columns', [ $this,'coursecolumns'] );
	   	 
	   	 
   	 	 add_action( 'manage_course_posts_custom_column', [ $this,'smashing_course_column'], 10, 2);
   	 	 add_action( 'manage_attendees_posts_custom_column', [ $this,'smashing_attendees_column'], 10, 2);
   	 	
		 add_filter( 'manage_attendees_posts_columns', [ $this, 'set_custom_edit_attendees_columns' ] );
         add_action( 'manage_attendees_posts_custom_column' ,[ $this, 'custom_attendees_column' ], 10, 2 );
         add_filter( 'wp_ajax_send_mail_to',[ $this, 'send_mail_to' ]  );
    
         add_filter( 'wp_ajax_send_mail_to1',[ $this, 'send_mail_to1' ]  );
         
        
         add_filter( 'handle_bulk_actions-edit-attendees',[ $this, 'misha_bulk_action_handler' ], 10, 3 );
         
         add_action( 'admin_menu', [ $this, 'aws_page_menu' ] );
         
    	$this->plugin_name = $plugin_name;
    	$this->version = $version;

	} 
	
	//// Wordpress custom certificate page menu
   function aws_page_menu(){
        remove_menu_page( 'edit.php?post_type=course' );
        remove_menu_page( 'edit.php?post_type=attendees' );
        remove_menu_page( 'post-new.php?post_type=course' );
        remove_menu_page( 'post-new.php?post_type=attendees' );
        
       
        add_menu_page('My Custom Page', 'Certificates', 'manage_options','/edit.php?post_type=course','','dashicons-awards'); 
        add_submenu_page('/edit.php?post_type=course', 'Attendees', 'Attendees', 'manage_options', '/edit.php?post_type=attendees');
        add_submenu_page('/edit.php?post_type=course',__( 'Books Shortcode Reference', 'textdomain' ),__( 'Settings', 'textdomain' ), 'manage_options','wa-school-certificate',array($this,'aws_ref_page_callback'));
  
        
   }	
    public function aws_ref_page_callback() { 
        ?>
        <div class="wrap">
            <h1><?php _e( 'Aw plugin Settings', 'textdomain' ); ?></h1>
            <?php include_once(__DIR__.'/wa-settings.php'); ?>
        </div>
        <?php
    }

	
	function attendeescolumns($columns){
        	
            $columns['course_title'] = __( 'Courses', 'smashing' );
            return $columns;
	}
	
	function smashing_attendees_column($column, $post_id){
          if ( 'course_title' === $column ) {
              	     global $wpdb;
              	    
                     
                    $results = get_post_meta($post_id,'courses',true);
                     $links = [];
                     if(!empty($results)){
                        
                          foreach($results as $result){
                              $links[] = '<a target="_blank" href="'.admin_url('/post.php?post='.$result.'&action=edit').'">'.get_the_title($result).'</a>';
                          }
                     }
                     
                     echo implode(", ",$links);

          }
	}
	
	function smashing_course_column($column, $post_id){
          if ( 'course_code' === $column ) {
             echo get_post_meta($post_id,'course_code',true);
          }
          if ( 'course_date' === $column ) {
             echo date('d-F-Y',strtotime(get_post_meta($post_id,'course_date',true)));
          }
	}
	
	function coursecolumns($columns){
            $columns['course_code'] = __( 'Course Code', 'smashing' );
            $columns['course_date'] = __( 'Course Date', 'smashing' );
            return $columns;
	}


    function smashing_realestate_column( $column, $post_id ) {
      // Image column
      if ( 'image' === $column ) {
        echo get_the_post_thumbnail( $post_id, array(80, 80) );
      }
    }



/*    function misha_bulk_action_handler( $redirect, $doaction, $object_ids ) {
     
    	// let's remove query args first
    	$redirect = remove_query_arg( array( 'misha_make_draft_done', 'misha_bulk_price_changed' ), $redirect );
     
    	// do something for "Make Draft" bulk action
    	if ( $doaction == 'send_email' ) {
     
    		foreach ( $object_ids as $post_id ) {
          
    			 extract($_POST);
               
                //$email = 'baljeet.masih.755@gmail.com';
                 $email = get_post_meta($post_id,'email',true);
                $name = get_post_meta($post_id,'first_name',true).' '.get_post_meta($post_id,'last_name',true); 
              	   
              	   
               $link =  get_the_permalink(get_option('wa_certificate_page')).'?course='.get_post_meta($post_id,'course_id',true).'&email='.get_post_meta($post_id,'email',true).'&inrolid='.$post_id; 
            
                $subject = "send email cetificate";	           

               $html = $this->html_mail_content($name,$link);
               $this->sendgridmail($email,$name,$subject,$html); 
                
               /// wp_mail($email,'send email cetificate',$html,$headers);
                
    		}
     
    		// do not forget to add query args to URL because we will show notices later
    		$redirect = add_query_arg('misha_make_draft_done', // just a parameter for URL (we will use $_GET['misha_make_draft_done'] )
    			count( $object_ids ), // parameter value - how much posts have been affected
    		$redirect );
     
    	}
    	return $redirect;
    }
*/

    
	function html_mail_content($name = 'Just test',$link = '',$custom_content = ''){
	    $link = explode('=',$link);
	    
	   if($custom_content == ''){
	      $post_id =  end($link);   
	  	    
	   
	  
	    $relation_id =  get_post_meta($post_id,'relation_id',true);
	    $date = date('l, d F Y',strtotime(get_post_meta($relation_id,'course_date',true)));
	     
	    $presenters = '';
           if( have_rows('presenters',$relation_id) ){
               while(have_rows('presenters',$relation_id)) { the_row();
               $presenters .= get_sub_field('presenters_name').' ,';// 'Robert Czachorski, Chris Goodell and Krey Price <td><span class="smit_by_heading"><b style="color:#38506e">'.get_sub_field('presenters_name').'</b> <br />'.get_sub_field('company_name').'</span></td>';
                   
               }
           } 
	     
	       
	   }
	  
	    
	     $html = '<!doctype html>
            <html>
              <head>
                <meta name="viewport" content="width=device-width" />
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                <link href="//db.onlinewebfonts.com/c/8d223b3ad8d4819e9dcf22757e4cc2c4?family=Arial" rel="stylesheet" type="text/css"/>
                <title>Simple Transactional Email</title>
                <style>
                  /* -------------------------------------
                      GLOBAL RESETS
                  ------------------------------------- */
                  
                  /*All the styling goes here*/
                  
                  img {
                    border: none;
                    -ms-interpolation-mode: bicubic;
                    max-width: 100%; 
                  }
            
                  body {
                    background-color: #f6f6f6;
                    
                    font-family: \'Arial\';
                    -webkit-font-smoothing: antialiased;
                    font-size: 14px;
                    line-height: 1.4;
                    margin: 0;
                    padding: 0;
                    -ms-text-size-adjust: 100%;
                    -webkit-text-size-adjust: 100%; 
                  }
            
                  table {
                    border-collapse: separate;
                    mso-table-lspace: 0pt;
                    mso-table-rspace: 0pt;
                    width: 100%; }
                    table td {
                       font-family: \'Arial\';
                      font-size: 14px;
                      vertical-align: top; 
                  }
            
                  /* -------------------------------------
                      BODY & CONTAINER
                  ------------------------------------- */
            
                  .body {
                    background-color: #f6f6f6;
                    width: 100%; 
                  }
            
                  /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
                  .container {
                    display: block;
                    margin: 0 auto !important;
                    /* makes it centered */
                    max-width: 580px;
                    padding: 10px;
                    width: 580px; 
                  }
            
                  /* This should also be a block element, so that it will fill 100% of the .container */
                  .content {
                    box-sizing: border-box;
                    display: block;
                    margin: 0 auto;
                    max-width: 580px;
                    padding: 10px; 
                  }
            
                  /* -------------------------------------
                      HEADER, FOOTER, MAIN
                  ------------------------------------- */
                  .main {
                    background: #ffffff;
                    border-radius: 3px;
                    width: 100%; 
                  }
            
                  .wrapper {
                    box-sizing: border-box;
                    padding: 20px; 
                  }
            
                  .content-block {
                    padding-bottom: 10px;
                    padding-top: 10px;
                  }
            
                  .footer {
                    clear: both;
                    margin-top: 10px;
                    text-align: center;
                    width: 100%; 
                  }
                    .footer td,
                    .footer p,
                    .footer span,
                    .footer a {
                      color: #999999;
                      font-size: 12px;
                      text-align: center; 
                  }
            
                  /* -------------------------------------
                      TYPOGRAPHY
                  ------------------------------------- */
                  h1,
                  h2,
                  h3,
                  h4 {
                    color: #000000;
                     font-family: \'Arial\';
                    font-weight: 400;
                    line-height: 1.4;
                    margin: 0;
                    margin-bottom: 30px; 
                  }
            
                  h1 {
                    font-size: 35px;
                    font-weight: 300;
                    text-align: center;
                    text-transform: capitalize; 
                  }
            
                  p,
                  ul,
                  ol {
                    font-family: \'Arial\';
                    font-size: 14px;
                    font-weight: normal;
                    margin: 0;
                    margin-bottom: 15px; 
                  }
                    p li,
                    ul li,
                    ol li {
                      list-style-position: inside;
                      margin-left: 5px; 
                  }
            
                  a {
                    color: #3498db;
                    text-decoration: underline; 
                  }
            
                  /* -------------------------------------
                      BUTTONS
                  ------------------------------------- */
                  .btn {
                    box-sizing: border-box;
                    width: 100%; }
                    .btn > tbody > tr > td {
                      padding-bottom: 15px; }
                    .btn table {
                      width: auto; 
                  }
                    .btn table td {
                      background-color: #ffffff;
                      border-radius: 5px;
                      text-align: center; 
                  }
                    .btn a {
                      background-color: #ffffff;
                      border: solid 1px #3498db;
                      border-radius: 5px;
                      box-sizing: border-box;
                      color: #3498db;
                      cursor: pointer;
                      display: inline-block;
                      font-size: 14px;
                      font-weight: bold;
                      margin: 0;
                      padding: 12px 25px;
                      text-decoration: none;
                      text-transform: capitalize; 
                  }
            
                  .btn-primary table td {
                    background-color: #3498db; 
                  }
            
                  .btn-primary a {
                    background-color: #3498db;
                    border-color: #3498db;
                    color: #ffffff; 
                  }
            
                  /* -------------------------------------
                      OTHER STYLES THAT MIGHT BE USEFUL
                  ------------------------------------- */
                  .last {
                    margin-bottom: 0; 
                  }
            
                  .first {
                    margin-top: 0; 
                  }
            
                  .align-center {
                    text-align: center; 
                  }
            
                  .align-right {
                    text-align: right; 
                  }
            
                  .align-left {
                    text-align: left; 
                  }
            
                  .clear {
                    clear: both; 
                  }
            
                  .mt0 {
                    margin-top: 0; 
                  }
            
                  .mb0 {
                    margin-bottom: 0; 
                  }
            
                  .preheader {
                    color: transparent;
                    display: none;
                    height: 0;
                    max-height: 0;
                    max-width: 0;
                    opacity: 0;
                    overflow: hidden;
                    mso-hide: all;
                    visibility: hidden;
                    width: 0; 
                  }
            
                  .powered-by a {
                    text-decoration: none; 
                  }
            
                  hr {
                    border: 0;
                    border-bottom: 1px solid #f6f6f6;
                    margin: 20px 0; 
                  }
            
                  /* -------------------------------------
                      RESPONSIVE AND MOBILE FRIENDLY STYLES
                  ------------------------------------- */
                  @media only screen and (max-width: 620px) {
                    table[class=body] h1 {
                      font-size: 28px !important;
                      margin-bottom: 10px !important; 
                    }
                    table[class=body] p,
                    table[class=body] ul,
                    table[class=body] ol,
                    table[class=body] td,
                    table[class=body] span,
                    table[class=body] a {
                      font-size: 16px !important; 
                    }
                    table[class=body] .wrapper,
                    table[class=body] .article {
                      padding: 10px !important; 
                    }
                    table[class=body] .content {
                      padding: 0 !important; 
                    }
                    table[class=body] .container {
                      padding: 0 !important;
                      width: 100% !important; 
                    }
                    table[class=body] .main {
                      border-left-width: 0 !important;
                      border-radius: 0 !important;
                      border-right-width: 0 !important; 
                    }
                    table[class=body] .btn table {
                      width: 100% !important; 
                    }
                    table[class=body] .btn a {
                      width: 100% !important; 
                    }
                    table[class=body] .img-responsive {
                      height: auto !important;
                      max-width: 100% !important;
                      width: auto !important; 
                    }
                  }
            
                  /* -------------------------------------
                      PRESERVE THESE STYLES IN THE HEAD
                  ------------------------------------- */
                  @media all {
                    .ExternalClass {
                      width: 100%; 
                    }
                    .ExternalClass,
                    .ExternalClass p,
                    .ExternalClass span,
                    .ExternalClass font,
                    .ExternalClass td,
                    .ExternalClass div {
                      line-height: 100%; 
                    }
                    .apple-link a {
                      color: inherit !important;
                      font-family: \'Arial\' !important;
                      font-size: inherit !important;
                      font-weight: inherit !important;
                      line-height: inherit !important;
                      text-decoration: none !important; 
                    }
                    #MessageViewBody a {
                      color: inherit;
                      text-decoration: none;
                      font-family: \'Arial\';
                      font-weight: inherit;
                      line-height: inherit;
                    }
                    .btn-primary table td:hover {
                      background-color: #34495e !important; 
                    }
                    .btn-primary a:hover {
                      background-color: #34495e !important;
                      border-color: #34495e !important; 
                    } 
                  }
            
                </style>
              </head>
              <body class="">
                <span class="preheader">This is preheader text. Some clients will show this text as a preview.</span>
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
                  <tr>
                    <td>&nbsp;</td>
                    <td class="container">
                      <div class="content">
            
                        <!-- START CENTERED WHITE CONTAINER -->
                        <table role="presentation" class="main">
            
                          <!-- START MAIN CONTENT AREA -->
                          <tr>
                            <td class="wrapper">
                              <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td>';
                                  if($custom_content !=""){
                                      $html .= $custom_content;
                                  }else{
                                  
                                  $html .='  <p>Dear '.$name.',</p>
                                    <p>Thank you for registering for the recent Webinar: <b>3D Computation Fluid Dynamic and Environmental Modelling</b>. The recording of this webinar will soon be available here.  </p>
                                    <p>Please <a href="'.$link.'">download your Certificate of Participation here.</a></p>
                                    
                                    <p>Feel free to register for the next Australian Water School Webinar:<br />
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.get_the_title($relation_id).'<br />
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Date: '.$date.'<br />
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Presenter/s: '.$presenters.'<br /> 
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;More info & register here</p>
                                        
                                    <p>View more info on upcoming Australian Water School here.</p>
                                    <br />
                                   
                                    <p>Kind Regards,</p>';
                                  }
                                    $html .='<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                      <tbody>
                                        <tr>
                                          <td align="left">
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                              <tbody>
                                                <tr>
                                                  <td>  </td>
                                                </tr>
                                              </tbody>
                                            </table>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                    <img src="'.get_site_url().'/wp-content/plugins/aws-water/admin/pasted-image-0.png" />
                                  </td>
                                </tr>
                              </table>
                            </td>
                          </tr>
            
                        <!-- END MAIN CONTENT AREA -->
                        </table>
                        <!-- END CENTERED WHITE CONTAINER -->
            
                        <!-- START FOOTER -->
                       
                        <!-- END FOOTER -->
            
                      </div>
                    </td>
                    <td>&nbsp;</td>
                  </tr>
                </table>
              </body>
            </html>';
            return $html;
            
	}
	 
	
	public function sendgridmail($to,$name,$subject,$html){   
	    include_once plugin_dir_path( __DIR__) . 'includes/phpmailer/vendor/autoload.php';
	    $from_email = get_option('sendgrid_mail_from') ? get_option('sendgrid_mail_from') : get_option('admin_email');
        $from_name = get_option('sendgrid_mail_from_name') ? get_option('sendgrid_mail_from_name') :  get_option('name');
        
        /////////////////// cerdieantal
        $Username =  get_option('sendgrid_username') ? get_option('sendgrid_username') :  'apikey';
        $password =  get_option('sendgrid_password') ? get_option('sendgrid_password') :  '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';
        $hostname =  get_option('sendgrid_hostname') ? get_option('sendgrid_hostname') :  'smtp.sendgrid.net'; 
        
         $subject =  get_option('sendgrid_mail_subject') ? get_option('sendgrid_mail_subject') :  'WA School certificate';
            
	    $mail = new PHPMailer(false);
        try {
            //Server settings
            $mail->SMTPDebug = 0;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = $hostname;                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = $Username;                     // SMTP username
            $mail->Password   = $password;                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above 
        
            //Recipients
            
            
            $mail->setFrom($from_email, $from_name);
            $mail->addAddress($to, $name);     // Add a recipient
            // $mail->addAddress('ellen@example.com');               // Name is optional
            $mail->addReplyTo('info@example.com', 'Information');
          /* $mail->addCC('cc@example.com');
            $mail->addBCC('bcc@example.com');*/
        
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $html;
          
            $mail->send();
          //  echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
	
	
	  public function send_mail_to1(){
	     
	    //echo   'i am here'; 
	    extract($_POST); 
	  
	    $att_id = $_POST['attendees_id'];
	    $email = $_POST['email'];
	    $link = '<a href="'.get_the_permalink(get_option('wa_certificate_page')).'?course='.$post_id.'&email='.$email.'&inrolid='.$att_id.'">download your Certificate of Participation here.</a>';
	  
	    $relation_id =  $post_id;
	    $date = date('l, d F Y',strtotime(get_post_meta($relation_id,'course_date',true)));
	     
	    $presenters = '';
           if( have_rows('presenters',$relation_id) ){
               while(have_rows('presenters',$relation_id)) { the_row();
               $presenters .= get_sub_field('presenters_name').' ,';// 'Robert Czachorski, Chris Goodell and Krey Price <td><span class="smit_by_heading"><b style="color:#38506e">'.get_sub_field('presenters_name').'</b> <br />'.get_sub_field('company_name').'</span></td>';
                   
               }
           } 
	     
	    $message = str_replace('{course}',get_the_title($relation_id),$message);
	    $message = str_replace('{date}',$date,$message);
	    $message = str_replace('{name}',$name,$message);
	    $message = str_replace('{link}',$link,$message);
	    $message = str_replace('{presenters}',$presenters,$message); 
	    
	     
	   
         $html =  $this->html_mail_content($name,$link,$message); 
          // $email = 'baljeet.masih.755@gmail.com'; //default setup my own email
         //$email = get_post_meta($post_id,'email',true); // now i have setup dynamic email 
          $subject = "";
    //    print_r($html);
        $this->sendgridmail($email,$name,$subject,$html); 
	    wp_send_json(array("status"=>"success"));
	    exit();
	}
	

   public function send_mail_to(){
	    //echo   'i am here'; 

	       extract($_POST);
	       global $wpdb;

                 $array = array('post_type'=>'attendees','posts_per_page'=>-1);
                  $loop = new wp_query($array);
                  
                  if($loop->have_posts()){
                      while($loop->have_posts()) { $loop->the_post();
                      $name = get_post_meta(get_the_ID(),'first_name',true).' '.get_post_meta(get_the_ID(),'last_name',true);
                      $attendess_course = get_post_meta(get_the_ID(),'courses',true);
                      if(in_array($course_id,$attendess_course)){
                                $message = '';
                                $message = $content;
                                $name  = get_post_meta(get_the_ID(),'first_name',true).' '.get_post_meta(get_the_ID(),'last_Name',true); 
                                $link =  get_the_permalink(get_option('wa_certificate_page')).'?course='.$course_id.'&email='.$result->email.'&inrolid='.get_the_ID();   
                                $link = '<p>Please <a href="'.$link.'">download your Certificate of Participation here.</a></p>';
                         	  
                        	    $date = date('l, d F Y',strtotime(get_post_meta($course_id,'course_date',true)));
                        	     
                        	   
                                   if( have_rows('presenters',$course_id) ){ 
                                       while(have_rows('presenters',$course_id)) { the_row();
                                       $presenters .= get_sub_field('presenters_name').' ,';// 'Robert Czachorski, Chris Goodell and Krey Price <td><span class="smit_by_heading"><b style="color:#38506e">'.get_sub_field('presenters_name').'</b> <br />'.get_sub_field('company_name').'</span></td>';
                                           
                                       }
                                   } 
                        	     
                        	    $message = str_replace('{course}',get_the_title($course_id),$message);
                        	    $message = str_replace('{date}',$date,$message);
                        	    $message = str_replace('{link}',$link,$message);
                        	    //echo $name;
                        	    $message = str_replace('{name}',$name,$message);  
                        	    $message = str_replace('{presenters}',$presenters,$message); 
                                $subject = "";
                               
                                
                                $html =  $this->html_mail_content($name,$link,$message);  
                                $email = get_post_meta(get_the_ID(),'email',true);
                                $this->sendgridmail($email,$name,$subject,$html);  
                               //$this->sendgridmail('baljeet.masih.755@gmail.com',$name,$subject,$html); 
                        }
                      }
                  } 
                      
     
	    wp_send_json(array("status"=>"success"));
	    exit();
	}
	
	
	
    // Add the custom columns to the book post type:
    
    function set_custom_edit_attendees_columns($columns) {
        unset( $columns['author'] );
        $columns['email'] = __( 'Email', 'textdomain' );
       // $columns['send_button'] = __( 'Action', 'textdomain' );
        //$columns['send_custom'] = __( 'Send Custom Message', 'textdomain' );
        $new = array();
          foreach($columns as $key => $title) {
            if ($key=='date') // Put the Thumbnail column before the Author column
              $new['email'] = 'Email';
            $new[$key] = $title;
          }
          return $new;
    }
     
    // Add the data to the custom columns for the book post type:
   
    function custom_attendees_column( $column, $post_id ) {
        switch ( $column ) {
            case 'email' :
               echo get_post_meta($post_id,'email',true);
                break;
            case 'send_button' :
                $name  = get_post_meta($post_id,'first_name',true).' '.get_post_meta($post_id,'last_name',true);
                echo '<button type="button" name="'.$name.'" class="send_email button" email="'.get_post_meta($post_id,'email',true).'" post_id="'.$post_id.'">Send Email</button>';
                break;
           
        }
    }

	function upload_attendees(){
		
	      $support_title = !empty($_POST['supporttitle']) ? 
			   $_POST['supporttitle'] : 'Support Title';

				if (!function_exists('wp_handle_upload')) {
				   require_once(ABSPATH . 'wp-admin/includes/file.php');
			   }
			  // echo $_FILES["upload"]["name"];
			  $uploadedfile = $_FILES['file'];
			  $upload_overrides = array('test_form' => false);
			  $movefile = wp_handle_upload($uploadedfile, $upload_overrides);
			  
			  $course_id = $_POST['course_id'];
			  $post_idsss   = $_POST['post_id'];
			  
             
			// echo $movefile['url'];
			  if ($movefile && !isset($movefile['error'])) {
				  
				  //print_r($movefile); 
				
				/******************************************/
				    $row = 0;
				    
			        /// i am getting email list here through this code start ----
			        global $wpdb;
				    $existing_email = [];
				    $args = array('post_type'=>'attendees','posts_per_page'=>-1);
				    $query = new wp_query($args);
				    if($query->have_posts()) {
				        while($query->have_posts()) { $query->the_post();
				            $existing_email[] = get_post_meta(get_the_ID(),'email',true);
				            
				            
				        }
				    }
			
				    
				    /// i am getting email list here through this code start ----
				    
					if (($handle = fopen($movefile['url'], "r")) !== FALSE) {
						
					  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
						$num = count($data);
						
				      if($row == 0){
						 
					  }else{
						  
						  if(in_array($data[4],$existing_email)){ ///////////////////////// if attendees already exist in database
						  
						      	 $attendees_id  = $wpdb->get_var( $wpdb->prepare( "SELECT post_id from $wpdb->postmeta where meta_key='email' and meta_value ='".$data[4]."'" ) );
                                 if($attendees_id) {
            						      	  $post_data = array(
                    								'post_title' =>$data[2].' '.$data[3],
                    								'post_type' => 'attendees',
                    								'post_status' => 'publish',
                    								'ID' => $attendees_id,
            						           );
   						                     wp_update_post( $post_data );


   						                      $joined_course_list = get_post_meta($attendees_id,'courses',true);
   						                     if(!empty($joined_course_list)){
   						                        
   						                         $counts_list = count($joined_course_list);
   						                         $joined_course_list[$counts_list] = $course_id; 
   						                         
   						                          serialize($joined_course_list);
   						                         //echo $joined_course_list;
   						                         //   update_post_meta($attendees_id,'courses','');
   						                          update_post_meta($attendees_id,'courses',$joined_course_list); 
   						                     } 
                                                 
                                                // die('here');
                        					 for ($c=0; $c < $num; $c++) {
                    							    if($data[0] == "No" || $data[0] =="NO" || $data[0] =="no" || $data[0] =="nO"){  
                    							        //////// skiped if attendees not attended the session 
                    							    }else{
                            							update_field('attended', $data[0], $attendees_id);
                            							update_field('user_name', $data[1], $attendees_id);
                            							update_field('first_name', $data[2], $attendees_id);
                            							update_field('last_name', $data[3], $attendees_id);
                            							update_field('email', $data[4], $attendees_id);
                            							update_field('address', $data[5], $attendees_id);
                            							update_field('city', $data[6], $attendees_id);
                            							update_field('countryregion', $data[7], $attendees_id);
                            							update_field('phone', $data[8], $attendees_id);
                            							update_field('organization', $data[9], $attendees_id);
                            							update_field('job_title', $data[10], $attendees_id);
                            							update_field('questions_comments', $data[11], $attendees_id);
                            							update_field('registration_time', $data[12], $attendees_id);
                            							update_field('approval_status', $data[13], $attendees_id);
                            							update_field('join_time', $data[14], $attendees_id);
                            							update_field('leave_time', $data[15], $attendees_id);
                            							update_field('time_in_session_minutes', $data[16], $attendees_id);
                            							update_field('countryregion_name', $data[17], $attendees_id);
                             						    update_field('course_id',$course_id, $attendees_id);
                            							//update_field('relation_id',$post_idsss, $attendees_id);
                                                               

                    							   }
                    						  }
        							
                                 }
						      
						      
						  }else{   ///////////////////////// if new attendees then insert in database
						      						  $post_data = array(
                        								'post_title' =>$data[2].' '.$data[3],
                        								'post_type' => 'attendees',
                        								'post_status' => 'publish'
                        					    	  );
                        					      	 $post_id = wp_insert_post( $post_data );
                        					      	  
                        					            $joined_course_list = array($course_id);
                        					            serialize($joined_course_list);
                        					            update_post_meta($post_id,'courses',$joined_course_list);  
                        					           
                        					            
                        					      	 for ($c=0; $c < $num; $c++) {
                            							    if($data[0] == "No" || $data[0] =="NO" || $data[0] =="no" || $data[0] =="nO"){  
                            							        //////// skiped if attendees not attended the session 
                            							    }else{
                                    							update_field('attended', $data[0], $post_id);
                                    							update_field('user_name', $data[1], $post_id);
                                    							update_field('first_name', $data[2], $post_id);
                                    							update_field('last_name', $data[3], $post_id);
                                    							update_field('email', $data[4], $post_id);
                                    							update_field('address', $data[5], $post_id);
                                    							update_field('city', $data[6], $post_id);
                                    							update_field('countryregion', $data[7], $post_id);
                                    							update_field('phone', $data[8], $post_id);
                                    							update_field('organization', $data[9], $post_id);
                                    							update_field('job_title', $data[10], $post_id);
                                    							update_field('questions_comments', $data[11], $post_id);
                                    							update_field('registration_time', $data[12], $post_id);
                                    							update_field('approval_status', $data[13], $post_id);
                                    							update_field('join_time', $data[14], $post_id);
                                    							update_field('leave_time', $data[15], $post_id);
                                    							update_field('time_in_session_minutes', $data[16], $post_id);
                                    							update_field('countryregion_name', $data[17], $post_id);
                                     						    update_field('course_id',$course_id, $post_id);
                                    							update_field('relation_id',$post_idsss, $post_id);
                                     							
                                    							
                            							   }
                            						  }
						      
						  }

					  }						
						$row++;						
					  }
					  fclose($handle);
					}
				 /******************************************/
				 
				 wp_send_json(array("status"=>"success","msg"=>"Record importaed Successfully!"));
			} else {
				/**
				 * Error generated by _wp_handle_upload()
				 * @see _wp_handle_upload() in wp-admin/includes/file.php
				 */
				 wp_send_json(array("status"=>"success","msg"=>"Opps! somthing wrong. ".$movefile['error']));
				
			}
			
			
			
			
			
			die();
		
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        wp_enqueue_style( $this->plugin_name.'data-tabale-css', plugin_dir_url( __FILE__ ) . 'css/jquery.dataTables.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin-aws-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		 wp_enqueue_script( $this->plugin_name.'data-tabale-js', plugin_dir_url( __FILE__ ) . 'js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, true );
		 
        wp_enqueue_script( $this->plugin_name.'sweet-alert','https://unpkg.com/sweetalert/dist/sweetalert.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-aws-admin.js', array( 'jquery' ), $this->version, true );
	    	// Localize the script with new data
			$translation_array = array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'a_value' => '10'
			);
			wp_localize_script( $this->plugin_name, 'object_name', $translation_array );
			 
			// Enqueued script with localized data.
			wp_enqueue_script( $this->plugin_name );
		

	}
	
	
		/*
		* Creating a function to create our CPT
		*/
		 
		public function custom_post_type() {
		// Set UI labels for Custom Post Type
			$labels = array(
				'name'                => _x( 'Attendees', 'Post Type General Name', 'twentytwenty' ),
				'singular_name'       => _x( 'Attendees', 'Post Type Singular Name', 'twentytwenty' ),
				'menu_name'           => __( 'Attendees', 'twentytwenty' ),
				'parent_item_colon'   => __( 'Parent Attendees', 'twentytwenty' ),
				'all_items'           => __( 'All Attendees', 'twentytwenty' ),
				'view_item'           => __( 'View Attendees', 'twentytwenty' ),
				'add_new_item'        => __( 'Add New Attendees', 'twentytwenty' ),
				'add_new'             => __( 'Add New', 'twentytwenty' ),
				'edit_item'           => __( 'Edit Attendees', 'twentytwenty' ),
				'update_item'         => __( 'Update Attendees', 'twentytwenty' ),
				'search_items'        => __( 'Search Attendees', 'twentytwenty' ),
				'not_found'           => __( 'Not Found', 'twentytwenty' ),
				'not_found_in_trash'  => __( 'Not found in Trash', 'twentytwenty' ),
			);
			
		// Set other options for Custom Post Type  
			$args = array(
				'label'               => __( 'Attendees', 'twentytwenty' ),
				'description'         => __( 'Movie news and reviews', 'twentytwenty' ),
				'labels'              => $labels,
				// Features this CPT supports in Post Editor
				'supports'            => array( 'title'),
				// You can associate this CPT with a taxonomy or custom taxonomy. 
				'taxonomies'          => array( 'genres' ),
				/* A hierarchical CPT is like Pages and can have
				* Parent and child items. A non-hierarchical CPT
				* is like Posts.
				*/ 
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => false,
				'menu_position'       => 5,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'post',
				'show_in_rest' => true,
		 
			);
			 
			// Registering your Custom Post Type
			register_post_type( 'attendees', $args );
			
			
			$labels1 = array(
				'name'                => _x( 'Courses', 'Post Type General Name', 'twentytwenty' ),
				'singular_name'       => _x( 'course', 'Post Type Singular Name', 'twentytwenty' ),
				'menu_name'           => __( 'Courses', 'twentytwenty' ),
				'parent_item_colon'   => __( 'Parent course', 'twentytwenty' ),
				'all_items'           => __( 'Courses', 'twentytwenty' ),
				'view_item'           => __( 'View course', 'twentytwenty' ),
				'add_new_item'        => __( 'Add New course', 'twentytwenty' ),
				'add_new'             => __( 'Add new course', 'twentytwenty' ),
				'edit_item'           => __( 'Edit course', 'twentytwenty' ),
				'update_item'         => __( 'Update course', 'twentytwenty' ),
				'search_items'        => __( 'Search course', 'twentytwenty' ),
				'not_found'           => __( 'Not Found', 'twentytwenty' ),
				'not_found_in_trash'  => __( 'Not found in Trash', 'twentytwenty' ),
			);
			
		// Set other options for Custom Post Type  
			$args1 = array(
				'label'               => __( 'Courses', 'twentytwenty' ), 
				'description'         => __( 'course news and reviews', 'twentytwenty' ),
				'labels'              => $labels1,
				// Features this CPT supports in Post Editor
				'supports'            => array( 'title' ),
				// You can associate this CPT with a taxonomy or custom taxonomy. 
				'taxonomies'          => array( 'genres' ),
				/* A hierarchical CPT is like Pages and can have
				* Parent and child items. A non-hierarchical CPT
				* is like Posts.
				*/ 
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 5,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'post',
				'show_in_rest' => true,
		 
			);
			 
			// Registering your Custom Post Type
			register_post_type( 'course', $args1 );			
			
			
			
			
		     //     add_submenu_page('edit.php?post_type=attendees',  __( 'Test Settings', 'menu-test' ),  __( 'Test Settings', 'menu-test' ),'manage_options',  'testsettings',  'mt_settings_page');

		}
		 
		/* Hook into the 'init' action so that the function
		* Containing our post type registration is not 
		* unnecessarily executed. 
		*/
 
	
	
	
	function add_csv_html( $views )
	{
	    
	    $args = array('post_type'=>'course','posts_per_page'=>-1);
	    $query = new wp_query($args);
	    $option = '';
	    if($query->have_posts()){
	        while($query->have_posts()) { $query->the_post();
	           $option .='<option post_id="'.get_the_ID().'" value="'.get_post_meta(get_the_ID(),'course_code',true).'">'.get_the_title().'</option>';
	        }
	    }
	    $views['attendees-course'] ='<select class="select-course"><option value="">Select Course</option>'.$option.'</select>'; 
		$views['attendees-csv'] ='<input type="file" id="csv-upload-controller" />';
		
		$views['attendees-upload'] = '<a id="update-from-provider" type="button"  title="Update from Provider" style="margin:5px">Upload Csv</a>';
		return $views;
	}
	
	
	
	function loader_setin_footer(){
		$html ='<style>
		.mywebsite-loader svg {
			height: 102px;
			margin: 0 auto;
			width: 102px;
			float: none;
		}
		.mywebsite-loader {
			position: fixed;
			width: 100%;
			height: 100%;
			background: rgba(0,0,0,0.5);
			z-index: 99999999999;
			top: 0;
			left: 0;
			text-align: center;
			padding: 20% 0;
		}
		</style>
		<div class="mywebsite-loader" style="display: none;">
			  <svg version="1.1" id="L2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve">
			  <circle fill="none" stroke="#1fa7ff" stroke-width="4" stroke-miterlimit="10" cx="50" cy="50" r="48"></circle>
			  <line fill="none" stroke-linecap="round" stroke="#1fa7ff" stroke-width="4" stroke-miterlimit="10" x1="50" y1="50" x2="85" y2="50.5">
				<animateTransform attributeName="transform" dur="2s" type="rotate" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform>
			  </line>
			  <line fill="none" stroke-linecap="round" stroke="#1fa7ff" stroke-width="4" stroke-miterlimit="10" x1="50" y1="50" x2="49.5" y2="74">
				<animateTransform attributeName="transform" dur="15s" type="rotate" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform>
			  </line>
		  </svg>
		</div>';
		echo $html;
		
	}
	
	
	

	

}
