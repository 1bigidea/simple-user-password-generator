function simple_user_generate_password() {
	jQuery.post( ajaxurl, { action: 'simple_user_generate_password' }, function(response){
		jQuery('#pass2').val(response);
		jQuery('#pass1').val(response).keyup();
		jQuery('#send_password').attr('checked',true);
		jQuery('#reset_password_notice').attr('checked',true);
	});
}

jQuery(document).ready(function(){
	var pass_wrap = jQuery('#pass1').closest('td');
	pass_wrap.append('<input type="button" name="simple-user-generate-password" id="simple-user-generate-password" value="' + simple_user_password_generator_l10n.Generate + '" onclick="simple_user_generate_password();" class="button" style="width: auto;" />');
	var send_wrap = jQuery('#send_password').closest('td');
	send_wrap.append('<br /><label for="reset_password_notice"><input type="checkbox" id="reset_password_notice" name="reset_password_notice" /> ' + simple_user_password_generator_l10n.PassChange + '</label>')
});