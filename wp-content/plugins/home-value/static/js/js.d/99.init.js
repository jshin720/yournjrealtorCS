/**
	@brief		Initilialize all 8b home value js on the page.
	@since		2016-12-11 20:19:08
**/
eightb_home_value = function()
{
	$( '.8b_home_value .ask_for_address' ).ask_for_address();
	$( '.8b_home_value .address_found' ).address_found();
	$( '.8b_home_value form' ).ajaxify_form();
	$( '.8b_home_value form' ).noncify_form();
	$( 'form.plainview_form_auto_tabs' ).plainview_form_auto_tabs();
}

jQuery( document ).ready( function( jQuery )
{
	$ = jQuery;
	eightb_home_value();
} );
