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
		var REOPEN_HEIGHT = 46; // matches .sb-reopen's box height in frontend.css

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
		// WordPress admin bar the way normal-flow content does. So this
		// always needs to measure the admin bar and offset for it, in both
		// fixed and non-fixed banner modes. Reads the real height rather
		// than assuming 32px/46px, so it adapts on its own at any screen
		// size, including WordPress's own mobile admin bar breakpoint.
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

		// Fixed mode only: nudge any other fixed/sticky page elements pinned
		// to the top down by the given offset, so the site's own header
		// never sits hidden underneath the banner or the reopen indicator.
		function adjustFixedElements( offset ) {
			if ( ! fixedMode ) return;
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
				setTimeout( function () {
					adjustFixedElements( banner.offsetHeight );
				}, 60 );
			}
			if ( reopen ) reopen.style.display = 'none';
		}

		function hideBanner( animate ) {
			function finish() {
				banner.style.display = 'none';
				adjustFixedElements( REOPEN_HEIGHT );
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
			adjustFixedElements( REOPEN_HEIGHT );
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
				adjustFixedElements( banner.offsetHeight );
			} else {
				adjustFixedElements( REOPEN_HEIGHT );
			}
		} );
	} );
})();
