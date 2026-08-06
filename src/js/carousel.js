import '@splidejs/splide/dist/css/splide-core.min.css';
import '../scss/carousel.scss';

import { Splide } from '@splidejs/splide';
import { AutoScroll } from '@splidejs/splide-extension-auto-scroll';

function mountSplide( el ) {
	let settings = {};
	try {
		settings = JSON.parse( el.dataset.settings || '{}' );
	} catch ( e ) {
		settings = {};
	}

	// `autoScroll` stays inside `settings` and is handed to the Splide
	// constructor as-is (Splide/the AutoScroll extension read it straight off
	// the options object). Assigning `splide.options = {...}` *after*
	// construction but *before* `.mount()` — which an earlier version of this
	// code did to bolt autoScroll on — crashes inside Splide's internal
	// options setter, since it reaches into `Components.Media`, which only
	// exists once mount() has started.
	const hasAutoScroll = !! settings.autoScroll;

	try {
		const splide = new Splide( el, settings );
		splide.mount( hasAutoScroll ? { AutoScroll } : undefined );
		el.classList.add( 'is-initialized' );
	} catch ( err ) {
		// Some environments (e.g. third-party scripts patching the Elementor
		// editor iframe's globals) can make Splide's own element detection
		// fail even though the markup is correct; fail silently here instead
		// of throwing, since the CSS fallback keeps the static markup visible
		// and the real front-end mounts normally.
		console.error( '[upsites-carousel] mount failed', err ); // eslint-disable-line no-console
	}
}

/**
 * In the Elementor editor, the widget's wrapper is inserted into the DOM
 * before its rendered markup (the `.splide__track`/`.splide__list` this
 * widget outputs) is injected — so `element_ready` can fire on an element
 * that is momentarily still empty. Rather than race that, wait for the
 * track to actually exist before mounting.
 */
function initCarousel( el ) {
	if ( el.classList.contains( 'is-initialized' ) ) {
		return;
	}

	if ( el.querySelector( '.splide__track' ) ) {
		mountSplide( el );
		return;
	}

	let attempts = 0;
	const waitForTrack = () => {
		attempts += 1;
		if ( el.querySelector( '.splide__track' ) ) {
			mountSplide( el );
		} else if ( attempts < 40 ) {
			requestAnimationFrame( waitForTrack );
		}
	};
	requestAnimationFrame( waitForTrack );
}

function initAll( context ) {
	const scope = context || document;
	scope.querySelectorAll( '.upsites-carousel' ).forEach( initCarousel );
}

document.addEventListener( 'DOMContentLoaded', () => initAll() );
// Footer-enqueued scripts commonly parse after DOMContentLoaded has already
// fired, in which case the listener above never runs — cover that case too.
if ( document.readyState !== 'loading' ) {
	initAll();
}

function bindElementorHook() {
	window.elementorFrontend.hooks.addAction( 'frontend/element_ready/upsites-carousel.default', ( $scope ) => {
		initAll( $scope[ 0 ] );
	} );
}

if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
	bindElementorHook();
} else {
	// Script is declared as depending on 'elementor-frontend', but in case load
	// order still slips (e.g. editor preview iframe re-injection), retry a few
	// times instead of silently never binding — otherwise widgets added after
	// the initial DOMContentLoaded never get initialized.
	let attempts = 0;
	const timer = setInterval( () => {
		attempts += 1;
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			clearInterval( timer );
			bindElementorHook();
		} else if ( attempts > 20 ) {
			clearInterval( timer );
		}
	}, 250 );
}
