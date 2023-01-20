/**
	@brief		Ajaxify a form in the content div.
	@details	When the form is sent, the 8b he div is given the css class "busy".
	@since		2016-12-23 18:44:35
**/
jQuery(function($)
{
	$.fn.extend(
	{
		ajaxify_form : function()
		{
			return this.each(function ()
			{
				var $$ = $(this);

				var $div = $( '.8b_home_value' );

				/**
					@brief		Send the form content via ajax, and then replace the page contents with the new contents.
					@since		2016-12-23 19:21:17
				**/
				$$.send_via_ajax = function()
				{
					$.post( {
						'data': $$.serialize(),
						'type': $$.attr('method'),
						'url': $$.attr('action')
					} )
					.done( function( data )
					{
						// Replace the content with the new content.
						var $data = $( data );
						var $new_div = $( '.8b_home_value', $data );
						$div.html( $new_div.html() );
						$div.removeClass( 'busy' );
						// And restart all javascript.
						eightb_home_value();
					} );
				}

				$$.submit( function( e )
				{
					e.preventDefault();
					$div.addClass( 'busy' );
					$$.send_via_ajax();
				} );
			});
		}
	});
}(jQuery));
;
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
;
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
;
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
;
/**
	@brief		Convert the form fieldsets in a form2 table to ajaxy tabs.
	@since		2015-07-11 19:47:46
**/
;(function( $ )
{
    $.fn.extend(
    {
        plainview_form_auto_tabs : function()
        {
            return this.each( function()
            {
                var $this = $(this);

                if ( $this.hasClass( 'auto_tabbed' ) )
                	return;

                $this.addClass( 'auto_tabbed' );

				var $fieldsets = $( 'div.fieldset', $this );
				if ( $fieldsets.length < 1 )
					return;

				$this.prepend( '<div style="clear: both"></div>' );
				// Create the "tabs", which are normal Wordpress tabs.
				var $subsubsub = $( '<ul class="subsubsub">' )
					.prependTo( $this );

				$.each( $fieldsets, function( index, item )
				{
					var $item = $(item);
					var $h3 = $( 'h3.title', $item );
					var $a = $( '<a href="#">' ).html( $h3.html() );
					$h3.remove();
					var $li = $( '<li>' );
					$a.appendTo( $li );
					$li.appendTo( $subsubsub );

					// We add a separator if we are not the last li.
					if ( index < $fieldsets.length - 1 )
						$li.append( '<span class="sep">&emsp;|&emsp;</span>' );

					// When clicking on a tab, show it
					$a.click( function()
					{
						$( 'li a', $subsubsub ).removeClass( 'current' );
						$(this).addClass( 'current' );
						$fieldsets.hide();
						$item.show();
					} );

				} );

				$( 'li a', $subsubsub ).first().click();
            } ); // return this.each( function()
        } // plugin: function()
    } ); // $.fn.extend({
} )( jQuery );
;
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
	
	if($(".input_itself input[type=range][name=home_extra_value]").length > 0 )
	{
	//add range output box after range element :ky 06/07/2019
	$(".input_itself input[type=range][name=home_extra_value]").each(function(){
			if(!$(this).next(".range-output").length)
			{
				$(this).after('<span class="range-output" id="range-output"></span>');
			}
   });
    //update range value data in output box : KY 06/08/2019
	var slider = document.getElementById("plainview_sdk_eightb_home_value_form2_inputs_range_home_extra_value");
	var output = document.getElementById("range-output");
	output.innerHTML = slider.value + '%';
	//update range value data in output box on onchange: KY 06/08/2019
	slider.oninput = function() {
	  output.innerHTML = this.value + '%';
	}
	}
} );
;

