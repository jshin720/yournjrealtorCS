/**
	@brief		Address was found.
	@since		2016-12-16 23:08:04
**/
jQuery(function($)
{
	$.fn.extend(
	{
		address_found : function()
		{
			return this.each(function ()
			{
				var $$ = $(this);

				// Put the nonce into the form.
				var $input = $( '<input>' )
					.prop( 'type', 'hidden' )
					.prop( 'name', '8b_home_value[nonce]' )
					.val( eightb_home_value_data.nonce )
					.appendTo( $( 'form', $$ ) );

				var $image = $( '.street_view img', $$ );

				var url = $image.data( 'url' );

				// Get the dimensions.
				var height = $image.height();
				var width = $image.width();

				// Replace the resolution in the street view url.
				url = url.replace( 'RESOLUTION', width + 'x' + height );

				$image.prop( 'src', url );
			});
		}
	});
}(jQuery));
