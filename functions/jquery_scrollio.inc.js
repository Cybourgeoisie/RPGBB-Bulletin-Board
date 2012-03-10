// JavaScript Document
$(document).ready(function(){
	
	
	// Expand and collapse the forums or subforums
	$(".forum_header").click(function(){
	  $(this).next(".forum_block").slideToggle("slow");
	  $(this).toggleClass("active");
	});
	$(".subforum_header").click(function(){
	  $(this).next(".subforum_block").slideToggle("slow");
	  $(this).toggleClass("active");
	});


	// Change the page when the posts page dropdown is used
	$(".list_pages_dropdown").change(function(){ 
	  $.idnum = $(".thread_id_num").val();
	  $.startnum = $(".list_pages_dropdown").val();
	  window.location.href = "./thread.php?id=" + $.idnum + "&start=" + $.startnum; 
	});


	// Display the edit box for a link clicked without reloading the page
//	$(".admin_jq_link").click(function(){
//	  $.get("", { id: $(this).attr("id") }, function(){
//		alert("jebeesus");
//	  }
//	});


	// Toggle Login
	$("a#login_toggle").click(function(){
	  $(".guest_footer").hide('fast');
	  $(".basic_login").show('fast');
	});
	$("a#basic_login_return").click(function(){
	  $(".basic_login").hide('fast');
	  $(".guest_footer").show('fast');
	});

});