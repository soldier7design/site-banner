(function () {
	function getCookie( name ) {
		var match = document.cookie.match( new RegExp( '(^| )' + name + '=([^;]+)' ) );
		return match ? decodeURIComponent( match[2] ) : null;
	}
	function setCookie( name, value, days ) {
		var expires = '';
		if ( days ) {
			var d = new Date();
			d.setTime( d.getTime() + days * 24 * 60 * 60 * 1000 );
			expires = '; expires=' + d.toUTCString();
		}
		document.cookie = name + '=' + encodeURIComponent( value ) + expires + '; path=/; SameSite=Lax';
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		var banner = document.getElementById( 'sb-banner' );
		var reopen = document.getElementById( 'sb-reopen' );
		if ( ! banner ) return;

		var cookieName   = ( window.SiteBannerData && window.SiteBannerData.cookieName ) || 'sb_dismissed';
		var closeBtn     = banner.querySelector( '.sb-banner-close' );
		var REOPEN_HEIGHT = 34; // matches .sb-reopen height in frontend.css

		// If logged into WP with the admin bar showing, sit below it instead of under it.
		function adminBarOffset() {
			var bar = document.getElementById( 'wpadminbar' );
			if ( bar && window.getComputedStyle( bar ).position === 'fixed' ) {
				return bar.offsetHeight;
			}
			return 0;
		}

		// Nudge any other fixed/sticky top elements (e.g. a fixed site header)
		// down by the banner's height, so nothing sits hidden underneath it.
		function adjustFixedElements( offset ) {
			var all = document.body.querySelectorAll( '*' );
			for ( var i = 0; i < all.length; i++ ) {
				var el = all[ i ];
				if ( el === banner || banner.contains( el ) || el === reopen || el.id === 'wpadminbar' ) continue;
				var cs = window.getComputedStyle( el );
				if ( cs.position === 'fixed' || cs.position === 'sticky' ) {
					var top = parseFloat( cs.top );
					if ( ! isNaN( top ) && top >= 0 && top < 5 ) {
						if ( el.dataset.sbBaseTop === undefined ) {
							el.dataset.sbBaseTop = top;
						}
						el.style.top = ( parseFloat( el.dataset.sbBaseTop ) + offset ) + 'px';
					}
				}
			}
		}

		// Position the reopen badge under the admin bar (same rule the banner
		// follows) and reserve its height so it never overlaps whatever the
		// theme's own header has sitting in the top-right corner.
		// Divs, not buttons, on purpose (avoids inheriting a theme's global
		// `button { ... }` reset styles). That means click still works out of
		// the box, but keyboard activation (Enter/Space) needs to be added
		// back in manually since a div isn't a real button.
		function onActivate( el, handler ) {
			if ( ! el ) return;
			el.addEventListener( 'click', handler );
			el.addEventListener( 'keydown', function ( e ) {
				if ( e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar' ) {
					e.preventDefault();
					handler( e );
				}
			} );
		}

		function positionReopen() {
			if ( ! reopen ) return;
			reopen.style.top = adminBarOffset() + 'px';
		}

		function showBanner() {
			var barOffset = adminBarOffset();
			banner.style.top = barOffset + 'px';
			banner.style.display = 'flex';

			requestAnimationFrame( function () {
				banner.classList.add( 'sb-visible' );
			} );

			setTimeout( function () {
				var height = banner.offsetHeight;
				document.documentElement.style.setProperty( '--sb-banner-height', height + 'px' );
				document.documentElement.classList.add( 'sb-banner-active' );
				adjustFixedElements( height );
			}, 60 );

			if ( reopen ) reopen.classList.remove( 'sb-visible' );
		}

		function hideBanner( animate ) {
			function finish() {
				banner.style.display = 'none';
				document.documentElement.style.setProperty( '--sb-banner-height', '0px' );
				document.documentElement.classList.remove( 'sb-banner-active' );
				// Don't drop all the way to 0: keep the header nudged down by the
				// reopen badge's own height so the badge has clear space to sit in.
				adjustFixedElements( REOPEN_HEIGHT );
				positionReopen();
				if ( reopen ) reopen.classList.add( 'sb-visible' );
			}
			if ( animate ) {
				banner.classList.remove( 'sb-visible' );
				banner.addEventListener( 'transitionend', function handler() {
					banner.removeEventListener( 'transitionend', handler );
					finish();
				}, { once: true } );
			} else {
				finish();
			}
		}

		if ( getCookie( cookieName ) ) {
			banner.style.display = 'none';
			adjustFixedElements( REOPEN_HEIGHT );
			positionReopen();
			if ( reopen ) reopen.classList.add( 'sb-visible' );
		} else {
			showBanner();
		}

		if ( closeBtn ) {
			onActivate( closeBtn, function ( e ) {
				e.preventDefault();
				e.stopPropagation();
				setCookie( cookieName, '1', 180 );
				hideBanner( true );
			} );
		}

		if ( reopen ) {
			onActivate( reopen, function () {
				setCookie( cookieName, '', -1 );
				showBanner();
			} );
		}

		window.addEventListener( 'resize', function () {
			if ( banner.style.display !== 'none' ) {
				var height = banner.offsetHeight;
				document.documentElement.style.setProperty( '--sb-banner-height', height + 'px' );
				adjustFixedElements( height );
			} else {
				adjustFixedElements( REOPEN_HEIGHT );
			}
			positionReopen();
		} );
	} );
})();
