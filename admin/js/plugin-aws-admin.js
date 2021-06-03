(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	  
	 
	 $(document).ready(function(){
	       /// $('iframe').click();
	       
	     
	  
	          
	           
    	  if($('body').hasClass('post-type-attendees')){ 
    	  //   alert('sdf');
    	     $('li#toplevel_page_edit-post_type-course').addClass('wp-has-submenu wp-has-current-submenu wp-menu-open menu-top toplevel_page_edit?post_type=course menu-top-first menu-top-last');
    	     $('li#toplevel_page_edit-post_type-course > a').addClass('wp-has-submenu wp-has-current-submenu wp-menu-open menu-top toplevel_page_edit?post_type=course menu-top-first menu-top-last');
    	      $('li#toplevel_page_edit-post_type-course ul').addClass('wp-submenu wp-submenu-wrap');
    	 }
    	 
	 });
	 
	 $('button.send_custom_message').click(function(){
	        $('.custom_messgae_modal').hide();
            $(this).next().show();
	 });
	  $('span.custom_messgae_modal_close').click(function(){
	    //  alert('hii');
	        $('.custom_messgae_modal').hide(); 
	  });
	
	
	
	  
	
	
	
	  $('.send_custom_email_msg').click(function(e){
	         e.preventDefault(); 
	         
	         var editor = $('div[data-name="email_content"] textarea').val();
	         editor = '<pre>'+editor+'</pre>';
             var email = $(this).attr('email');
    	     var post_id = $(this).attr('post_id'); 
    	     var name = $(this).attr('name');  
    	     var attendees_id  = $(this).attr('attendees_id');  
    	    
    	     
              if(post_id == "" || email == "" || name==""){
    	         alert('required parameters are missing');
    	         return false;
    	     }
 
         	var form_data = new FormData();
    		form_data.append('email',email);
    		form_data.append('post_id',post_id);
    		form_data.append('action', 'send_mail_to1'); 	  
    		form_data.append('name', name);	  
    		form_data.append('attendees_id', attendees_id);	  
    		form_data.append('message', editor);	  
    	
    		
    			swal({
				  title: "Are you sure?",
				  text: "This will send an email to this attendee. Continue?",
				  icon: "warning",
				  buttons: true,
				  dangerMode: true,
				  buttons: ['Cancel', 'Continue']
				})
				.then((willDelete) => {
				  if (willDelete) {
					 	$('.mywebsite-loader').show();
						
                jQuery.ajax({
        				 type : "post",
        				 dataType : "json",
        				 url : object_name.ajax_url,
        				 contentType: false,
        				 processData: false,
        				 data: form_data,
        				 success: function(response) {
        					 $('.mywebsite-loader').hide();
        					 $('.custom_messgae_modal').hide();
        					 
        					if(response.status == "success") {
        					    swal("Good job!", "E-mail sent successfully!", "success");
        					//	location.reload();
        					}
        					else {
        					   swal("Opps! somthing wrong. Please try again.", "warning");
        					}
        				 }
        			  }); 	 	     
                      	
					//swal("Poof! Your imaginary file has been deleted!", { icon: "success",});
				  } else {
					  
					swal("Request Cancelled!");
				  }
				});	
				
    		
          

	  }); 
	
	$('li#menu-posts-attendees, li#menu-posts-course').remove();
	
	
	$('#csv-upload-controller').change(function(){
		  var fileExtension = ['csv'];
			if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
				alert("Only formats are allowed : "+fileExtension.join(', '));
			}else{
				    
					var file_data = $(this).prop('files')[0];
					console.log(file_data);
					var form_data = new FormData();
					form_data.append('file', file_data);
					form_data.append('action', 'upload_attendees');
					
					var course_id = $(this).attr('course_id');
				    var course_code = $('div[data-name="course_code"] input').val(); 
				
					
					form_data.append('course_id', course_id);  
				//	form_data.append('post_id', post_id);  
					if(course_code == ""){
					     alert('Please enter course code .');
					     return false;
					}
					
					if(course_id == ''){
					     alert('Please first select course .');
					     return false;
					} else{ 
					
					
					
				swal({
				  title: "Are you sure?",
				  text: "You want to upload selected file?",
				  icon: "warning",
				  buttons: true,
				  dangerMode: true,
				})
				.then((willDelete) => {
				  if (willDelete) {
					  $('.mywebsite-loader').show();
					   jQuery.ajax({
						 type : "post",
						 dataType : "json",
						 url : object_name.ajax_url,
						 contentType: false,
						 processData: false,
						 data: form_data,
						 success: function(response) {
							 $('.mywebsite-loader').hide();
							if(response.status == "success") {
							    swal("Good job!", "Your attendees imported successfully!", "success");
								location.reload();
							}
							else {
							   swal("Opps! somthing wrong. Please try again.", "warning");
							}
						 }
					  }); 
					//swal("Poof! Your imaginary file has been deleted!", { icon: "success",});
				  } else {
					  
					swal("Request Cancelled!");
				  }
				});	
				
				
				
			 }
			}
	}); 
	
	////////////////////////////////////////////
	
	$('.post-type-attendees #titlewrap input').attr('readonly','readonly');
	
	$('div[data-name="first_name"] input').keyup(function(){
	   var fname =  $(this).val();
	   var lname = $('div[data-name="last_name"] input').val();
	   $('.post-type-attendees #titlewrap input').val('');
	   $('.post-type-attendees #titlewrap input').val(fname+' '+lname); 
	   
	});
	
	$('div[data-name="last_name"] input').keyup(function(){
	   var lname =  $(this).val();
	   var fname = $('div[data-name="first_name"] input').val();
	   $('.post-type-attendees #titlewrap input').val('');
	   $('.post-type-attendees #titlewrap input').val(fname+' '+lname); 
	   
	});
	
	//////////////////////////////////////////// send email to individual
	$('button.aws_send_email').on('click',function(){
	
	     var course_id = $(this).attr('course_id');
	     var email_content = $('div[data-name="email_content"] textarea').val();
	     email_content = '<pre>'+email_content+'</pre>';  
	     
		var form_data = new FormData();
		form_data.append('course_id',course_id);
		form_data.append('content',email_content);
		form_data.append('action', 'send_mail_to');	  
  
  
  
  		swal({
				  title: "Are you sure?",
				  text: "This will send an email to all attendees. Continue?",
				  icon: "warning",
				  buttons: true,
				  dangerMode: true,
				  buttons: ['Cancel', 'Continue']
				})
				.then((willDelete) => {
				  if (willDelete) {
					  $('.mywebsite-loader').show();
						
            	     
                          jQuery.ajax({
            				 type : "post",
            				 dataType : "json",
            				 url : object_name.ajax_url,
            				 contentType: false,
            				 processData: false,
            				 data: form_data,
            				 success: function(response) {
            					 $('.mywebsite-loader').hide();
            					if(response.status == "success") {
            					    swal("Good job!", "E-mail sent successfully!", "success");
            					//	location.reload();
            					}
            					else {
            					   swal("Opps! somthing wrong. Please try again.", "warning");
            					}
            				 }
            			  }); 	
					//swal("Poof! Your imaginary file has been deleted!", { icon: "success",});
				  } else {
					  
					swal("Request Cancelled!");
				  }
				});	
				
		
			  
			  
	     
	});

 //   $("#example").dataTable();
    
var table = $('#example').DataTable();


setTimeout(function(){ 
  //  alert("Hello"); 
  var tr =  $('#example tbody tr').length
   /// alert(tr);
    if(tr == 1){
         var ifempty = $('#example tbody tr td:first-child').html(); 
          console.log(ifempty);

         if(ifempty == '' || ifempty == 'No data available in table'){
              $('button.button.aws_send_email').attr('disabled','disabled');    
         }else{
              $('button.button.aws_send_email').removeAttr('disabled');
         }
         
    }else{
         $('button.button.aws_send_email').removeAttr('disabled');
    }

}, 5000);

/*
 setInterval(function(){ 
var count =  1;

    $('#example tbody tr').each(function(){ 
       
        if(count ==1){
            $('button.button.aws_send_email').attr('disabled','disabled');
             count++;
        }
        if(count > 3){
            return false;
        }
        
        if(count >= 2){
            $('button.button.aws_send_email').removeAttr('disabled');
        }
         console.log(count);
       count++;
    });
    
    
    
    
 }, 3000);*/


})( jQuery );
