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

		// Nothing here is fixed anymore, so there's no viewport math to do.
		// Both the banner and the reopen indicator sit in normal document
		// flow at the very top of the page and scroll away like anything
		// else. WordPress's own admin bar spacing and the theme's own
		// header just fall in line naturally underneath.

		function showBanner() {
			banner.style.display = 'flex';
			requestAnimationFrame( function () {
				banner.classList.add( 'sb-visible' );
			} );
			if ( reopen ) reopen.style.display = 'none';
		}

		function hideBanner( animate ) {
			function finish() {
				banner.style.display = 'none';
				if ( reopen ) reopen.style.display = 'flex';
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
			if ( reopen ) reopen.style.display = 'flex';
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
	} );
})();
