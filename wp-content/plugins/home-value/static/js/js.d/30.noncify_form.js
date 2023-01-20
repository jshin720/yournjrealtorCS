/**
	@brief		Use JS to add the nonce to the form.
	@since		2016-12-23 19:29:36
**/
jQuery(function($)
{
	$.fn.extend(
	{
		noncify_form : function()
		{
			return this.each(function ()
			{
				var $$ = $(this);

				// Put the nonce into the form.
				$( '<input>' )
					.prop( 'type', 'hidden' )
					.prop( 'name', '8b_home_value[nonce]' )
					.val( eightb_home_value_data.nonce )
					.appendTo( $$ );
			});
		}
	});
}(jQuery));
