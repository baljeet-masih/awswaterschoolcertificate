<?php
if(isset($_POST['save_setting'])){
    
    if(isset($_POST['sendgrid_mail_from'])){
        update_option('sendgrid_mail_from',$_POST['sendgrid_mail_from']);
    }
    if(isset($_POST['sendgrid_mail_from_name'])){
         update_option('sendgrid_mail_from_name',$_POST['sendgrid_mail_from_name']);
    }
    if(isset($_POST['sendgrid_hostname'])){
         update_option('sendgrid_hostname',$_POST['sendgrid_hostname']);
    }
    if(isset($_POST['sendgrid_username'])){
         update_option('sendgrid_username',$_POST['sendgrid_username']);
    }
    if(isset($_POST['sendgrid_password'])){
         update_option('sendgrid_password',$_POST['sendgrid_password']);
    }
    if(isset($_POST['sendgrid_mail_subject'])){
         update_option('sendgrid_mail_subject',$_POST['sendgrid_mail_subject']);
    }     
    if(isset($_POST['aws_certificate_page'])){
         update_option('aws_certificate_page',$_POST['aws_certificate_page']);
    }   
    if(isset($_POST['aws_editor'])){
         update_option('aws_editor',$_POST['aws_editor']);
    }   
    
    
    
} 
 
?>
<STYLE>
    .field_area {
        margin: 11px 0;
    }
    .field_area label {
        margin-bottom: -12px;
        display: block;
    }    
</STYLE>
<div class="smtp-settings">
     <h2><?php  echo __( 'Sand Grid Smtp settings', 'textdomain' ); ?></h2>
     <hr />
     <form action="<?php echo admin_url('/edit.php?post_type=course&page=wa-school-certificate'); ?>" method="post">
     <div class="field_area">
         <label><?php  echo __( 'Sand Grid mail From', 'textdomain' ); ?></label><br />
         <input type="text" id="sendgrid_mail_from" name="sendgrid_mail_from" value="<?php echo  get_option('sendgrid_mail_from') ?  get_option('sendgrid_mail_from') : get_option('admin_email'); ?>">
     </div>
     
     <div class="field_area">
         <label><?php  echo __( 'Sand Grid from name', 'textdomain' ); ?></label><br />
         <input type="text" id="sendgrid_mail_from_name" name="sendgrid_mail_from_name" value="<?php echo get_option('sendgrid_mail_from_name'); ?>">
     </div>
     
     <div class="field_area">
         <label><?php  echo __( 'Sand Grid Hostname', 'textdomain' ); ?></label><br />
         <input type="text" id="sendgrid_hostname" name="sendgrid_hostname" value="<?php echo get_option('sendgrid_hostname'); ?>">
     </div>
     
     <div class="field_area">
         <label><?php  echo __( 'Sand Grid Username', 'textdomain' ); ?></label><br />
         <input type="text" id="sendgrid_username" name="sendgrid_username" value="<?php echo get_option('sendgrid_username'); ?>">
     </div>
     
     <div class="field_area">
         <label><?php  echo __( 'Sand Grid Password', 'textdomain' ); ?></label><br />
         <input type="password" id="sendgrid_password" name="sendgrid_password" value="<?php echo get_option('sendgrid_password'); ?>">
     </div>
     
     <div class="field_area">
         <label><?php  echo __( 'Sand Grid Email Subject', 'textdomain' ); ?></label><br />
         <input type="text" id="sendgrid_mail_subject" name="sendgrid_mail_subject" value="<?php echo get_option('sendgrid_mail_subject'); ?>">
     </div>
     
    
     <h2><?php  echo __( 'Certificate Page download Setting', 'textdomain' ); ?></h2>
     <hr />     
     
     <div class="field_area">
     
         <select  name="aws_certificate_page" class="page-title-action">
             <option value="">Please Select Page</option>
             <?php $args = array('post_type'=>'page','posts_per_page'=>-1); 
             $aws_certificate_page = get_option('aws_certificate_page');
               $query  = new wp_query($args);
               if(!empty($query->have_posts())){
                   
                   while($query->have_posts()) { $query->the_post();
                  $selected = '';
                  if($aws_certificate_page == get_the_ID()){
                      $selected = 'selected';
                  }
             ?>
               <option value="<?php echo get_the_ID(); ?>" <?php echo $selected;  ?>><?php echo get_the_title(); ?></option>
             
             <?php } wp_reset_query(); } ?>
         </select>
     </div>
     
     
     <label>Add custom content  message</label>
     <?php
        $content   = get_option('aws_editor');
        $editor_id = 'aws_editor'; 
        $settings = array(
                            'editor_class'  => 'c_msg_c',
                            'media_buttons' => false,
                            'editor_height' => 300, // In pixels, takes precedence and has no default value
                            'textarea_rows' => 10,  // Has no visible effect if editor_height is set, default is 20
                        );
        wp_editor( $content, $editor_id,$settings ); 
     ?>
      <div class="field_area">
         <input type="submit" name="save_setting" value="Save Setting" class="page-title-action" style="margin-top: 19px;padding: 9px 17px;">
     </div>
     </form>
</div>



