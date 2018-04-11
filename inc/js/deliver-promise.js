var goodwill_is_email_confirmed = false;

( function( $ ) {

    function generate_attached_material_html() {
        var html = '<ul>';

        $.each( $( '#panel-additional-materials input[type="checkbox"]:checked' ), function () {
            html += '<li>';
            html += '<a href="' + $( this ).val() + '">'
            html += $( this ).parent().text();
            html += '</a>'
            html += '</li>';
        } );

        return html + '</ul>';
    }

    function goodwill_apply_email_template_filters ( content, no_html ) {
        var $content = $( '<div>' + content + '</div>' );

        // Default no_html to false
        if ( typeof no_html === 'undefined' ) {
            no_html = false;
        }

        $.each( email_template_data.filters, function ( class_name, replacement ) {
            var $entity = $content.find('.' + class_name);
            if ( no_html === false ) {
                $entity.html( replacement );
            } else {
                $entity.replaceWith( replacement );
            }
        } );

        // Apply the additional materials
        if ( no_html === false ) {
            $content.find('.template-attached-materials').html(
                generate_attached_material_html()
            );
        }

        return $content.html();
    }

    // @todo Clean up the parsing and manipulation of HTML
    function display_uploaded_file( display_name, url, is_selected, is_image ) {
    	var html = $( $( '#preview-file-template' ).clone()
    											 .wrap( '<p/>' )
    											 .parent()
    											 .html() );
    	var $html = $( html );

    	$html.removeAttr( 'id' ).addClass( 'goodwill-uploaded-file' );

    	// Fill in the template
    	$html.find( '.file-filename' ).text( display_name );

        // Replace the image with a span if the file is not an image
        if ( is_image !== true ) {
            $html.find( 'img.file-thumbnail' ).replaceWith( function () {
                return $( '<span />' ).addClass('file-thumbnail').attr( 'src', url );
            } );
        }
        else {
            $html.find( 'img.file-thumbnail' ).attr( 'src', url );
        }

    	$html.removeClass( 'hide' );

    	if (is_selected === false) {
	    	handle_select_file( $html.find( '.btn-select-file' ) );
    	}

    	$( '#preview-files' ).append( $html.wrap( '<p/>' ).parent().html() );
    }

    function store_uploaded_files() {
    	var value = [];

    	// Iterate over the files
    	$( '.goodwill-uploaded-file' ).each( function () {
    		var file = {};

    		file.display_name = $( this ).find( '.file-filename' ).text();
    		file.url = $( this ).find( '.file-thumbnail' ).attr( 'src' );
    		file.is_selected = $( this ).find( '.glyphicon.goodwill-select-icon' ).length >= 1;
            file.is_image = $( this ).find( '.file-thumbnail' ).prop('tagName') === 'img';

    		value.push( file );

    	} );

    	$.cookie( 'uploaded-files', value );
    }

    function restore_uploaded_files() {
    	var uploaded_files = $.cookie( 'uploaded-files' ) ;

    	if ( $.isArray( uploaded_files ) ) {
	    	$.each( uploaded_files, function () {
	    		display_uploaded_file( this.display_name, this.url, this.is_selected, this.is_image );
	    	} );
	    }
    }

    function handle_upload( $form, file ) {
    	var original_text = $( '#select-file' ).text();
    	$( '#select-file' ).toggleClass( 'btn-upload' )
    						.attr( 'disabled', 'disabled' )
    						.text($( '#select-file').data( 'loading' ) );

    	var progressHandlingFunction = function ( e ) {
    		console.debug( e );
    	};

    	$.ajax( {
    		'url': $form.attr( 'action' ),
    		'type': $form.attr( 'method' ),
    		'xhr': function () {
				var myXhr = $.ajaxSettings.xhr();
				if ( myXhr.upload ) {
					myXhr.upload.addEventListener( 'progress', progressHandlingFunction, false );
				}
				return myXhr;
    		},
    		'data': new FormData( $form[0] ),
    		'cache': false,
    		'contentType': false,
    		'processData': false,
    		'dataType': 'json',
            'complete': function ( jq_xhr, text_status ) {
                $( '#select-file' ).toggleClass( 'btn-upload' )
                        .text( original_text )
                        .removeAttr( 'disabled' );
            },
            'error': function ( jq_xhr, text_status, error_thrown ) {
                alert( 'A server error occured when uploading, let someone know there was a "' + text_status + '" and "' + error_thrown + '"' );
            },
    		'success': function ( data ) {
    			if ( data.isError == false ) {
                    // @todo Get a thumbnail of the file and display it instead of the whole picture

                    display_uploaded_file(
                        file.name.substring( 0, file.name.lastIndexOf( '.' ) ),
                        data.url,
                        true,
                        data.isImage
                    );

                    store_uploaded_files();
                }
    			else {
                    alert( data.message );
                }
            }
    	} );
    }

    function handle_select_file( $element ) {
    	var $text = $element.find( '.btn-select-file-text' ),
    		new_text = $text.data( 'loading' );

    	$element.toggleClass( 'btn-success' ).toggleClass( 'btn-default' );

    	$text.data( 'loading', $element.text() );
    	$text.text( new_text );

    	$element.find( '.goodwill-select-icon' ).toggleClass( 'glyphicon glyphicon-ok' );

    	$element.parents( '.thumbnail' ).toggleClass( 'thumbnail-success' );
    }

    function setup_upload() {
        // Handle picking files from the device
        $( '#select-file' ).click( function ( e ) {
        	e.preventDefault();
        	$( '#upload-file' ).click();
        } );

        // Handle uploading files
        $( '#upload-file' ).change( function ( e ) {
        	var file;

        	// handle the cancel button - or nothing is selected
            if ( 0 === this.files.length ) {
                return;
            }

            file = this.files[0];

	        handle_upload(
	        	$(this).parent( 'form' ),
	        	this.files[0]
	        );
        } );

        // Handle selecting files for attaching
        $( '.btn-select-file' ).live( 'click', function ( e ) {
        	e.preventDefault();
			handle_select_file( $( this ) );
			store_uploaded_files();
        } );

        restore_uploaded_files();
    }

    // Client side only - passes to the server and is handled when attaching emails
    function setup_renaming() {

    }

    function render_preview_email_attachment_html() {
    	var html = '';

    	$( '#preview-files .thumbnail' ).each( function () {
    		if ( $( this ).parent( '.hide' ).length === 0
    			&& $( this ).find( '.glyphicon.goodwill-select-icon' ).length >= 1
    		) {
    			$thumbnail = $( this ).parent().clone();
    			$thumbnail.removeAttr( 'class' ).addClass( 'col-xs-12 col-sm-4' );
	    		handle_select_file( $thumbnail.find( '.btn-select-file' ) );
    			$thumbnail.find( '.caption p').remove();
    			html += $thumbnail.wrap( '<p/>' ).parent().html();
    		}
    	} );

    	return html;
    }

    $( function() {
        // Move the email preview HTML outside of the content
        // so the overlay doesn't over the modal
        $( '#content' ).before( $( '#modal-email-preview' ).detach() );

        // Apply the selected email template
        $( '#email-template' ).change( function ( e ) {
            var $selected = $( this ).find( ':selected' );

            $( '#email-subject' ).val(
                goodwill_apply_email_template_filters(
                    $selected.data( 'subject' ),
                    true // Leave no HTML in replacements
                )
            );

            tinyMCE.get( 'email-body' ).setContent(
                goodwill_apply_email_template_filters(
                    $selected.data( 'body' )
                )
            );
        } );

        $( '#panel-additional-materials input[type="checkbox"]').change( function ( e ) {
            var $content = $( '<div>' + tinyMCE.get( 'email-body' ).getContent() + '</div>' );

            $content.find('.template-attached-materials').html(
                generate_attached_material_html()
            );

            tinyMCE.get( 'email-body' ).setContent( $content.html() );
        } );

        setup_upload();

        $( '#email-send' ).submit( function ( e ) {
            // Detect if TinyMCE is loaded
            if (typeof tinyMCE === 'undefined') {
                alert('An error occurred loading the page. Please refresh from the side menu and try again.');
                return false;
            }

	    // Make sure we have a message body
	    if (tinyMCE.get( 'email-body' ).getContent().length === 0) {
                alert('Please enter a message body for the email.');
                return false;
            }

            if (goodwill_is_email_confirmed === true) {
                return true;
            }

            // Interrupt the submit process to display a dialog confirming the email
            e.preventDefault();

            $( '#preview-email-subject' ).text( $( '#email-subject' ).val() );
            $( '#preview-email-from' ).text( $( '#email-from' ).val() );
            $( '#preview-email-to' ).text( $( '#email-to' ).val() );
            $( '#preview-email-cc' ).text( $( '#email-cc' ).val() );
            $( '#preview-email-bcc' ).text( $( '#email-bcc' ).val() );
            $( '#preview-email-sent' ).text( moment().format('LLLL') );
            $( '#preview-email-body' ).html( tinyMCE.get( 'email-body' ).getContent() );
            $( '#preview-email-attachments' ).html( render_preview_email_attachment_html() );

            $( '#preview-email-send' ).click( function( e ) {
                goodwill_is_email_confirmed = true;
                $( this ).bootstrapButton( 'loading' );
                $( '#email-send' ).submit();
            } );

            // @todo Do a little validation about showing/hiding the "Send email" button

            $( '#modal-email-preview' ).modal();
        } );

    } ); // End on document ready
} )( jQuery );
