jQuery( function ( $ ) {

	// Wire up each color field: global swatches as quick-picks, an "Other"
	// swatch that reveals the full color picker for a custom value.
	$( '.sb-color-picker' ).each( function () {
		var $input    = $( this );
		var $wrap     = $input.closest( '.sb-color-field' );
		var $swatches = $wrap.find( '.sb-swatches' );
		var current   = $input.val();

		$input.wpColorPicker( {
			change: function ( event, ui ) {
				$input.val( ui.color.toString() );
			}
		} );

		var $container = $input.closest( '.wp-picker-container' );
		var matched     = false;

		$swatches.find( '.sb-swatch' ).not( '.sb-swatch-other' ).each( function () {
			if ( String( $( this ).data( 'color' ) ).toLowerCase() === String( current ).toLowerCase() ) {
				$( this ).addClass( 'selected' );
				matched = true;
			}
		} );

		if ( matched ) {
			$container.hide();
		} else {
			$swatches.find( '.sb-swatch-other' ).addClass( 'selected' );
		}

		$swatches.on( 'click', '.sb-swatch', function () {
			$swatches.find( '.sb-swatch' ).removeClass( 'selected' );
			$( this ).addClass( 'selected' );

			var color = $( this ).data( 'color' );
			if ( color === 'other' ) {
				$container.show();
			} else {
				$input.wpColorPicker( 'color', color );
				$container.hide();
			}
		} );
	} );

	// Live-update the ON/OFF label next to the master toggle.
	$( '#sb-enabled' ).on( 'change', function () {
		$( this ).closest( '.sb-toggle-row' ).find( '.sb-toggle-label strong' ).text( this.checked ? 'ON' : 'OFF' );
	} );

	// Show/hide the whole schedule block.
	$( '#sb-schedule-enabled' ).on( 'change', function () {
		$( '#sb-schedule-fields' ).toggle( this.checked );
	} );

	// Show/hide the specific publish datetime field.
	$( 'input[name="publish_mode"]' ).on( 'change', function () {
		$( '#sb-publish-datetime-row' ).toggle( $( 'input[name="publish_mode"]:checked' ).val() === 'scheduled' );
	} );

} );
