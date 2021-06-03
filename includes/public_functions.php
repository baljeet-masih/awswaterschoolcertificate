<?php //ob_start();

   add_filter( 'wp_default_editor',  function() {return 'tinymce';});
  

    function base64url_encode($data) {
      return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    function base64url_decode($data) {
      return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
    
    function filter_cars_by_taxonomies( $post_type, $which ) {

    	// Apply this only on a specific post type
    	if ( 'attendees' !== $post_type )
    		return;
    
    	// A list of taxonomy slugs to filter by
    	$filters = array('attendees');
        global $wpdb;
    	foreach ( $filters as $filter) { 
              //////////////   
              if($filter == 'attendees'){
                 $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}posts WHERE post_type='course' and post_status='publish'");  
                /* echo '<pre>';
                 print_r($results);
                 echo '</pre>';*/
                 
                  	echo "<select name='filter_course' id='filter_course' class='postform'>";
                     echo '<option value="">All courses</option>';
                         foreach($results as $result){
                          $selected = '';
                              if(isset($_GET['filter_course']))
                              {
                                  if($_GET['filter_course'] == $result->ID){
                                        $selected = 'selected';        
                                  }
                              }
                              echo '<option value="'.$result->ID.'" '.$selected.'>'.$result->post_title.'</option>';
                         }
                  	echo '</select>';
              }

    	
    	}
    
    }
    add_action( 'restrict_manage_posts', 'filter_cars_by_taxonomies' , 10, 2);


    
    /**
     * if submitted filter by post meta
     * 
     * make sure to change META_KEY to the actual meta key
     * and POST_TYPE to the name of your custom post type
     * @author Ohad Raz
     * @param  (wp_query object) $query
     * 
     * @return Void
     */
add_action( 'before_delete_post', 'myfunc' );
function myfunc( $postid ){
  global $wpdb;
   if ( get_post_type( $postid ) == 'course' ) {
        //if is true
          $results = $wpdb->get_results( "DELETE FROM {$wpdb->prefix}aws_things WHERE  course_id=$postid"); 
   }
   if ( get_post_type( $postid ) == 'attendees' ) {
        //if is true
        $results = $wpdb->get_results( "DELETE FROM {$wpdb->prefix}aws_things WHERE  attendee_id=$postid"); 
   }
 
}
 
     
 add_filter( 'parse_query', 'aws_post_filter' ); 
 function aws_post_filter( $query ){ 
        global $pagenow;
        $type = 'attendees';
        if (isset($_GET['post_type'])) {
            $type = $_GET['post_type'];
        }
       // print_r($pagenow);
        if ( 'attendees' == $type && is_admin() && $pagenow=='edit.php') {
            
            
          /// echo 'i am here';
          if(isset($_GET['filter_course']) && $_GET['filter_course'] !=''){
       
            $course_id = $_GET['filter_course'];
            global $wpdb;
            $da_table_name = $wpdb->prefix.'aws_things';
            $results = $wpdb->get_results( "SELECT attendee_id FROM {$wpdb->prefix}aws_things WHERE course_id=$course_id");
            $filterAtt = [];
               foreach($results as $atlist){
                   $filterAtt[] = $atlist->attendee_id;
               }
                     
              $query->query_vars['post__in'] = $filterAtt;
          }
          
        }
    }   
    
    
/***************************************************************************************/  
add_action( 'add_meta_boxes', 'add_course_metaboxes' );

function add_course_metaboxes() {
	add_meta_box(
		'aws_email_csv_manage',
		'AWS Email / CSV manage',
		'aws_email_csv_manage',
		'course',
		'normal',
		'default'
	);
	add_meta_box(
		'was_attendees_list',
		'Attendees List',
		'was_attendees_list',
		'course',
		'normal',
		'default'
	);
// 	add_meta_box(
// 		'was_attendees_course_list',
// 		'Joined Course List',
// 		'was_attendees_course_list',
// 		'attendees',
// 		'normal',
// 		'default'
// 	);
	

}


function aws_email_csv_manage() {
	global $post;
	$post_id = $post->ID;
	
	// Nonce field to validate form request came from current site
	wp_nonce_field( basename( __FILE__ ), 'event_fields' );

	// Get the location data if it's already been entered
	$location = get_post_meta( $post->ID, 'location', true );
	



     echo '<div class="aws_button_align"><div class="button csv-upload-controller">Import CSV<input course_id="'.$post->ID.'" type="file" id="csv-upload-controller" name="csv-upload-controller" /></div>';
	// Send Email to All Attendees
	echo '<button type="button" class="button aws_send_email" course_id="'.$post->ID.'">Send Email to All Attendees</button></div>';

} 

function was_attendees_list() {
	global $post;
	$post_id = $post->ID;
// Nonce field to validate form request came from current site
	
  ?>
       <table id="example">
        <thead>
          <tr>
              <th>Attendees Name</th>
              <th>Email</th>
              <th>Action</th>              
          </tr>
        </thead>
        <tbody>
            <?php 
                  global $wpdb;
                  
                  $array = array('post_type'=>'attendees','posts_per_page'=>-1);
                  $loop = new wp_query($array);
                  
                  if($loop->have_posts()){
                      while($loop->have_posts()) { $loop->the_post();
                      $name = get_post_meta(get_the_ID(),'first_name',true).' '.get_post_meta(get_the_ID(),'last_name',true);
                      $attendess_course = get_post_meta(get_the_ID(),'courses',true);
                     
                     if(in_array($post_id,$attendess_course)){
                  ?>
                  <tr> 
                      
                      <td><?php //print_r($attendess_course); ?><a target="_blank" href="<?php echo admin_url('/post.php?post='.get_the_ID().'&action=edit'); ?>"><?php echo $name; ?></a></td>
                      <td><?php echo get_post_meta(get_the_ID(),'email',true); ?></td> 
                      <td><button email="<?php echo get_post_meta(get_the_ID(),'email',true); ?>" class="button send_custom_email_msg" name="<?php echo $name; ?>" attendees_id="<?php echo get_the_ID(); ?>" post_id="<?php echo $post_id; ?>">Send Email</button></td>                      
                  </tr>
            <?php
                   }
                       }
            } else {  ?>       
                  <tr>
                      <td  style="text-align:center"> </td>
                      <td  style="text-align:center"> Attandess not found!</td>     
                      <td  style="text-align:center"></td>     
                  </tr>

            <?php } ?>
        </tbody>
      </table>  Remove the “Send Email” link in the bulk actions list.


  
  <?php 
}   


function my_acf_input_admin_footer() {
	
?>
<script type="text/javascript">
(function($) {
	
acf.add_action('wysiwyg_tinymce_init', function( ed, id, mceInit, $field ){
   
	$('#wp-'+id+'-editor-container .mce-statusbar').append('<div class="acfcounter" style="background-color: #f7f7f7; color: #444; padding: 2px 10px; font-size: 12px; border-top: 1px solid #e5e5e5;"><span class="words" style="font-size: 12px; padding-right: 30px;"></span><span class="chars" style="font-size: 12px;"></span></div>');

	counter = function() {
	    var value = $('#'+id).val();
	          
	    if (value.length == 0) {
	        $('#wp-'+id+'-editor-container .mce-statusbar .acfcounter .words').html('Word Count: 0');
	        $('#wp-'+id+'-editor-container .mce-statusbar .acfcounter .chars').html('Characters: 0');
	        return;
	    }
	
	    var regex = /\s+/gi;
	    var wordCount = value.trim().replace(regex, ' ').split(' ').length;
	    var totalChars = value.length;

	    $('#wp-'+id+'-editor-container .mce-statusbar .acfcounter .words').html('Word Count: '+wordCount);
	    $('#wp-'+id+'-editor-container .mce-statusbar .acfcounter .chars').html('Characters: '+totalChars);
	    
	    
	    $('button.button.aws_send_email').attr('disabled','disabled');
	    $('button.button.send_custom_email_msg').attr('disabled','disabled');
	    
	     $('.erro_message_email').remove();
	     $('<p class="erro_message_email">Please save the email content before sending</p>').insertAfter('button.button.aws_send_email');
	     $('<p class="erro_message_email">Please save the email content before sending</p>').insertAfter('button.button.send_custom_email_msg');
	    
	    
	    $('button.update_editor_content').remove();
	    $('<button class="button update_editor_content button button-primary button-large">Update</button>').insertAfter('div[data-name="email_content"]');

	};
	
	
    $('#wp-'+id+'-editor-container .mce-statusbar .acfcounter .words').html('Word Count: 0');
    $('#wp-'+id+'-editor-container .mce-statusbar .acfcounter .chars').html('Characters: 0');
	        
    $('#'+id).change(counter);
    $('#'+id).keydown(counter);
    $('#'+id).keypress(counter);
    $('#'+id).keyup(counter);
	$('#'+id).blur(counter);
    $('#'+id).focus(counter);

	
});

})(jQuery);	
</script>
<?php
		
}

add_action('acf/input/admin_footer', 'my_acf_input_admin_footer');
   
   