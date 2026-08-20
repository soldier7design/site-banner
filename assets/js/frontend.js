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

		var cookieName = ( window.SiteBannerData && window.SiteBannerData.cookieName ) || 'sb_dismissed';
		var fixedMode  = !! ( window.SiteBannerData && window.SiteBannerData.fixed );
		var closeBtn   = banner.querySelector( '.sb-banner-close' );

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

		// The reopen indicator is always position:absolute or position:fixed
		// (never normal flow), and neither of those automatically clears the
		// WordPress admin bar the way normal-flow content does. This always
		// measures the admin bar's real height rather than assuming one, so
		// it self-corrects at any screen size, including the admin bar's own
		// mobile breakpoint where it grows taller.
		function adminBarOffset() {
			var bar = document.getElementById( 'wpadminbar' );
			if ( bar && window.getComputedStyle( bar ).position === 'fixed' ) {
				return bar.offsetHeight;
			}
			return 0;
		}

		function positionReopen() {
			if ( reopen ) reopen.style.top = adminBarOffset() + 'px';
		}

		// Fixed mode only, and only while the full banner is showing (never
		// for the small corner indicator, it's deliberately small enough not
		// to need this). Pushes both (a) any other fixed/sticky page elements
		// like a theme's own header, and (b) normal in-flow page content,
		// down by the banner's real current height, so nothing ends up
		// hidden underneath it. offset of 0 reverts everything back.
		function pushPageContent( offset ) {
			if ( ! fixedMode ) return;

			document.documentElement.style.setProperty( '--sb-fixed-banner-height', offset + 'px' );
			document.documentElement.classList.toggle( 'sb-fixed-active', offset > 0 );

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

		function showBanner() {
			if ( fixedMode ) {
				banner.style.top = adminBarOffset() + 'px';
			}
			banner.style.display = 'flex';
			requestAnimationFrame( function () {
				banner.classList.add( 'sb-visible' );
			} );
			if ( fixedMode ) {
				// Measure after the browser has actually laid the banner
				// out (text wrap makes the real height a moving target),
				// then reserve exactly that much space.
				setTimeout( function () {
					pushPageContent( banner.offsetHeight );
				}, 60 );
			}
			if ( reopen ) reopen.style.display = 'none';
		}

		function hideBanner( animate ) {
			function finish() {
				banner.style.display = 'none';
				pushPageContent( 0 );
				positionReopen();
				if ( reopen ) reopen.style.display = 'block';
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
			pushPageContent( 0 );
			positionReopen();
			if ( reopen ) reopen.style.display = 'block';
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
			positionReopen();
			if ( ! fixedMode ) return;
			if ( banner.style.display !== 'none' ) {
				banner.style.top = adminBarOffset() + 'px';
				pushPageContent( banner.offsetHeight );
			}
			// If the banner is hidden (indicator showing), there's nothing
			// to re-push, pushPageContent(0) already applies from hideBanner.
		} );
	} );
})();
