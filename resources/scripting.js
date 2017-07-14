/**
 * The Code
 *
 * based on Matty Theme QuickSwitch
 *
 * @since 0.5.1
 */

(function ($) {
	CryoutThemeSwitch = {

		add_search_box: function () {
			var searchForm = $( '<li class="search-form"> <form name="cryout-themeswitch-search"> <input type="text" class="search" placeholder="Quick search..."/> </form> </li>' ).addClass( 'search-form' );

			$( '#wp-admin-bar-cryout-themeswitch' ).find( 'li#wp-admin-bar-heading-child-themes' ).after( searchForm );

		},

		perform_search: function ( searchText ) {
			CryoutThemeSwitch.reset_results();
			if ( searchText != '' && searchText.length >= 1 ) {

				$( '#wp-admin-bar-cryout-themeswitch li.the_list' ).each( function ( i ) {
					var hayStack = $( this ).text().toLowerCase();
					var needle = searchText.toLowerCase();

					if ( hayStack.indexOf( needle ) == -1 ) {
						$( this ).addClass( 'hide-theme' );
					}
				});
			}

		},

		reset_results: function () {
			$( '#wp-admin-bar-cryout-themeswitch' ).find( '.hide-theme' ).removeClass( 'hide-theme' );
		},

		hide_all: function () {
			$( '#wp-admin-bar-cryout-themeswitch' ).find( '.the_list' ).addClass( 'hide-theme' );
		}

	}; // End Object

	$(document).ready(function () {

		CryoutThemeSwitch.add_search_box();

		// Make sure the search field focuses when visible.
		$( '#wp-admin-bar-cryout-themeswitch' ).mouseover( function ( e ) {
			$( this ).find( 'input.search' ).focus();
		});

		$( '#wp-admin-bar-cryout-themeswitch' ).find( 'input.search' ).keyup( function ( e ) {
			if ( $( this ).val() != '' ) {
				CryoutThemeSwitch.perform_search( $( this ).val() );
			} else {
				CryoutThemeSwitch.hide_all();
				$( '#wp-admin-bar-cryout-themeswitch' ).find( 'input.search' ).focus();
			}
		});

		$('#wp-admin-bar-cryout-themeswitch ul ul li').mouseover(function() {
		    // .position() uses position relative to the offset parent,
		    var posy = $(this).position();
			posy = posy.top;

			var posx = $('#wp-admin-bar-cryout-themeswitch').position();
			posx = posx.left;

		    var width = $('#wp-admin-bar-cryout-themeswitch-default').outerWidth();
			var total_left = posx + 2*width;

			var window_width = $(window).width() - 400;

			if ( total_left > window_width ) {
				total_left = posx - 1.5*width;
			}

		    $(this).find('.themeswitch-screenshot').css({
		        top: posy + "px",
		        left: total_left + "px"
		    })
		});

	});
})(jQuery);
