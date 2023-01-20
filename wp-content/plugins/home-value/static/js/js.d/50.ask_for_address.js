/**
	@brief		Handle the ask for address page.
	@since		2016-12-11 20:18:44
**/
jQuery(function($)
{
	$.fn.extend(
	{
		ask_for_address : function()
		{
			return this.each(function ()
			{
				var $$ = $(this);

				var $address = $( 'input.address', $$ );
				var $found_address = $( '.found_address', $$ );

				autocomplete = new google.maps.places.Autocomplete
				(
					$address[ 0 ],
					{
						types: ['geocode'],
						componentRestrictions: { country: 'us' }
					}
				);

				google.maps.event.addListener(autocomplete, 'place_changed', function()
				{
					var found_parts = {};
					var needed_parts = {
						0 : 'street_number',
						1 : 'route',
						2 : 'postal_code',
					};

					var place = this.getPlace();
					var components = place.address_components;

					for ( var part_number in needed_parts )
					{
						var part_key = needed_parts[ part_number ];

						for ( var counter = 0; counter < components.length ; counter++ )
							if ( components[ counter ].types[ 0 ] == part_key )
								found_parts[ part_key ] = components[ counter ].long_name;
					}

					var found_address = found_parts[ 'street_number' ] + ' ' + found_parts[ 'route' ] + ';' + found_parts[ 'postal_code' ];
					$found_address.val( found_address );
				} );

			});
		}
	});
}(jQuery));
