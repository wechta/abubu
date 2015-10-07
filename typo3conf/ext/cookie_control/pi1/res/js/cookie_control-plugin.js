jQuery(document).ready(function(){
    	  jQuery('#cctoggle').live('click',function(){
    	  	if(document.getElementById('cchide-popup').checked){
    	  		CookieControl.setCookie('civicShowCookieIcon', 'no');$('#ccc-icon').hide()
    	  	}
    		 if(jQuery(this).attr('class') == 'cctoggle-on'){
    			 jQuery.ajax({
                     url: 'index.php',
                     data: {
                             eID: 'cookieDelete',
                             tx_cookie_control_pi1: ({
                                 'in':2
                         })
                     },
                     success: function(data) {
                     	CookieControl.reset();
                     	setTimeout(function(){window.location.reload()}, 500);
                     }
             });
    			
    		 }else{ 
    			 jQuery.ajax({
                     url: 'index.php',
                     data: {
                             eID: 'cookieDelete',
                             tx_cookie_control_pi1: ({
                                     'in': 1
                             })
                     },
                     success: function(data) {
 						
                     }
             });
    		 }
    		 return false;
    	  });
    	 /*jQuery('#cookie_enable').bind('click',function(){
    		  CookieControl.setCookie('civicShowCookieIcon', 'yes');
    		  $('#cccwr').show();
        	  $('#ccc-icon').show();
    	  });
    	  
    	  setTimeout(function(){
    		  $('#cccwr').hide()
        	  $('#ccc-icon').hide()
          },300000) ;*/
});
