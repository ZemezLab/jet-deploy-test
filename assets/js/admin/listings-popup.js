(function( $ ) {

	'use strict';

	var JetListings = {

		init: function() {

			var self = this;

			$( document )
				.on( 'click.JetListings', '.page-title-action', self.openPopup )
				.on( 'click.JetListings', '.jet-listings-popup__overlay', self.closePopup );

			$( 'body' ).on( 'change', '#listing_source', self.switchListingSources );

		},

		switchListingSources: function( event ) {

			var $this = $( this ),
				val   = $this.find( 'option:selected' ).val(),
				$row  = $this.closest( '.jet-listings-popup__form-row' );

			$row.siblings( '.jet-template-listing' ).removeClass( 'jet-template-act' );
			$row.siblings( '.jet-template-' + val ).addClass( 'jet-template-act' );

		},

		openPopup: function( event ) {
			event.preventDefault();
			$( '.jet-listings-popup' ).addClass( 'jet-listings-popup-active' );
		},

		closePopup: function() {
			$( '.jet-listings-popup' ).removeClass( 'jet-listings-popup-active' );
		}

	};

	JetListings.init();

})( jQuery );
