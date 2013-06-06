var supg_pass1 = document.getElementById('pass1');
var supg_send_password = document.getElementById('send_password');
jQuery(supg_pass1).closest('td').append('<p style="clear:both;margin:0;"><input type="button" name="simple-user-generate-password" id="simple-user-generate-password" value="' + simple_user_password_generator_l10n.Generate + '" onclick="simple_user_generate_password();" class="button" style="width: auto;" /></p>');
jQuery(supg_send_password).closest('td').append('<br /><label for="reset_password_notice"><input type="checkbox" id="reset_password_notice" name="reset_password_notice" /> ' + simple_user_password_generator_l10n.PassChange + '</label>')

jQuery(supg_pass1).on('keyup',function(){
	var passval = this.value;
	if ( '' == passval ) jQuery(supg_send_password).attr('disabled','disabled');
	else jQuery(supg_send_password).removeAttr('disabled');
});

function simple_user_generate_password() {
	jQuery.post( ajaxurl, { action: 'simple_user_generate_password' }, function(response){
		document.getElementById('pass2').value = response;
		supg_pass1.value = response;
		jQuery(supg_pass1).trigger('keyup');
		jQuery(supg_send_password).attr('checked',true);
		jQuery(document.getElementById('reset_password_notice')).attr('checked',true);
	});
}