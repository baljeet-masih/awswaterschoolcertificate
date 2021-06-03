<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 * @author     Your Name <email@example.com>
 */
 
 
 
class Plugin_Name_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
        add_action( 'init',[ $this, 'download_pdf_certificate' ]  );
		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	
	function download_pdf_certificate(){ 
	       $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L']);       
	      
	           
	           if(isset($_GET['course']) && isset($_GET['email']) &&  isset($_GET['inrolid'])){
	               
	               $get_post_id = $_GET['inrolid'];
	               $course_id = $_GET['course'];
	               
	               if ( FALSE === get_post_status( $get_post_id ) ) {	
	                   // The post does not exist	
	                   ?>
	                   <script>
	                       alert('Opps! attendees record deleted form system.');
	                   </script>
	                   <?php 
	                    } else {	
	                   // The post exists
	                   
	                   $firstname   =  get_post_meta($get_post_id,'first_name',true);
	                   $lastname    =  get_post_meta($get_post_id,'last_name',true);
	                   $relation_id =  $course_id;
	                   $webinar     =  get_the_title($relation_id) ? get_the_title($relation_id) : 'Webinar : Realising the benifits of cloud computing';
	                   
	                   $date = date('l, d F Y',strtotime(get_post_meta($relation_id,'course_date',true))); 
	                   $presenters = '';
	                   if( have_rows('presenters',$relation_id) ){
	                       while(have_rows('presenters',$relation_id)) { the_row();
	                       $presenters .=  '<td><span class="smit_by_heading"><b style="color:#38506e">'.get_sub_field('presenters_name').'</b> <br />'.get_sub_field('company_name').'</span></td>';
	                           
	                       }
	                   }
	                   
	                   
                        				//	<td><span class="smit_by_heading">John Smith <br /> <b>Company B</b></span></td>
	                   
    	               $html = '';
                       $html .= '<!DOCTYPE html>
                        <html>
                        <head>
                        <title>Page Title</title>
                        <link href="//db.onlinewebfonts.com/c/8d223b3ad8d4819e9dcf22757e4cc2c4?family=Arial" rel="stylesheet" type="text/css"/>
                        <style>
                                    body {
                                              background: url("'.home_url('/wp-content/plugins/aws-water/includes/mpdf/').'images/pastedimage0.png") no-repeat 0 0;
                                             background-position: top left;
                                        	 background-repeat: no-repeat;
                                        	 background-image-resize: 4;
                                        	 background-image-resolution: from-image;
                                        	 hright:400px;
                                        }
                                        .surename{
                                        	color:#87be9c;
                                        	font-size:30px;	   	
                                        	font-family: \'Arial\';
                                        }
                                        pre{
                                            font-family: \'Arial\';
                                        }
                                        
                                        .for_atteding{
                                        	color:#87be9c;
                                        	font-size:18px;	  
                                        	padding-bottom:15px;
                                        	font-family: \'Arial\';
                                        	border-bottom:#c3dbcb 2px solid;
                                        }
                                        .for_atteding1{
                                        	color:#87be9c;
                                        	font-size:21px;	 
                                        	font-family: \'Arial\';
                                        	font-weight:bold;
                                        	line-height:0;
                                        }
                                        .for_pass_date{
                                        	color:#87be9c;
                                        	font-size:17px;	 
                                        	font-family: \'Arial\';
                                        	font-weight:400;
                                        	margin-top:-50px;
                                        	line-height:0;
                                        }
                                        .info_box{
                                        	padding-top:350px;
                                        	padding-left:70px;
                                        	width:55%;
                                        	float:left
                                        }
                                        .info_box_signature{
                                        	padding-top:390px;
                                        	width:30%;
                                        	float:left
                                        }
                                        .presented_by{
                                        	display:flex;
                                        }
                                        td, span{
                                            font-family: \'Arial\';
                                        }
                                        .date{
                                           color:#38506e;
                                            font-family: \'Arial\';
                                        }

                                                    </style>
                        </head>
                        <body> 
                           <div class="info_box">
                        		<h2 class="surename">'.$firstname.' '.$lastname.'</h2>
                        		<p class="for_atteding">for attending</p>
                        		<p class="for_atteding1" style="margin:0; margin-bottom:7px;">'.$webinar.'</p>
                        		<p class="for_pass_date" style="margin-bottom:50px;">'.$date.'</p>
                        		
                        		<table style="width:100%;">
                        		  <tbody>
                        				<tr>
                        					<td style="vertical-align: top;"><span class="smit_by_heading"><b style="color:#38506e">Presented By</b></span></td>
                        					'.$presenters.'
                        				</tr>
                        		  </tbody>
                        		</table>
                        		
                        	</div>
                        	<div class="info_box_signature">
                        	       <img src="'.home_url('/wp-content/plugins/aws-water/includes/mpdf/').'images/pdf-stemp.png" style="width:200px; margin-left:100px" />
                        	       <p class="date" style="margin-left:160px; position:relative; margin-top:-20px;">'.date('d-m-Y').'<p>
                        	</div>
                        		
                        		
                        </body>
                        </html>'; 
                        
                        $mpdf->WriteHTML($html);
                        
                        $mpdf->Output('filename.pdf','D');
                        $mpdf->Output();
	                   
	                    wp_safe_redirect( home_url('/'));
                        exit;
	                   
	               }
	               
	           }
	           
                

	    
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin-name-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-public.js', array( 'jquery' ), $this->version, false );

	}

}
