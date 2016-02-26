
(function ( $, gmb ) {

    /**
     *  Add Markers
     *
     * This is the marker that first displays on load for the main location or place
     *
     * @param map
     */
    gmb.add_markers = function ( map ) {

        gmb.clear_main_markers();

        //Loop through repeatable field of markers
        $( "#gmb_markers_group_repeat" ).find( '.cmb-repeatable-grouping' ).each( function ( index ) {

            var marker_icon = gmb_data.default_marker;
            var marker_label = '';

            //check for custom marker and label data
            var custom_marker_icon = $( '#gmb_markers_group_' + index + '_marker' ).val();
            var custom_marker_img = $( '#gmb_markers_group_' + index + '_marker_img' ).val();

            //check for custom marker and label data
            if ( custom_marker_img ) {
                marker_icon = custom_marker_img;
            } else if ( custom_marker_icon.length > 0 && custom_marker_icon.length > 0 ) {
                var custom_label = $( '#gmb_markers_group_' + index + '_label' ).val();
                marker_icon = eval( "(" + custom_marker_icon + ")" );
                marker_label = custom_label;
            }


            //Marker for map
            var location_marker = new Marker( {
                map: map,
                zIndex: 9,
                icon: marker_icon,
                custom_label: marker_label
            } );

            var marker_lat = $( '#gmb_markers_group_' + index + '_lat' ).val();
            var marker_lng = $( '#gmb_markers_group_' + index + '_lng' ).val();

            location_marker.setPosition( new google.maps.LatLng( marker_lat, marker_lng ) );
            location_marker.setVisible( true );

            google.maps.event.addListener( location_marker, 'click', function () {
                gmb.get_info_window_content( index, location_marker );
            } );

            //Remove row button/icon also removes icon (CMB2 buttons)
            $( '#gmb_markers_group_' + index + '_title' ).parents( '.cmb-repeatable-grouping' ).find( '.cmb-remove-group-row' ).each( function () {
                google.maps.event.addDomListener( $( this )[ 0 ], 'click', function () {
                    var index = $( this ).parents( '.cmb-repeatable-grouping' ).data( 'index' );
                    //close info window and remove marker
                    info_bubble.close();
                    location_marker.setVisible( false );
                } );
            } );

        } ); //end $.each()

    };

    gmb.get_marker_index = function(){
        //Create a new marker repeatable meta group
        var index = parseInt( $( '#gmb_markers_group_repeat div.cmb-repeatable-grouping' ).last().attr( 'data-iterator' ) );
        var existing_vals = $( 'div[data-iterator="0"] ' ).find( 'input,textarea' ).val();

        //Ensure appropriate index is used for marker
        if ( existing_vals && index === 0 ) {
            $( '.cmb-add-group-row.button' ).trigger( 'click' );
            index = 1;
        } else if ( index !== 0 ) {
            $( '.cmb-add-group-row.button' ).trigger( 'click' );
            //recount rows
            index = parseInt( $( '#gmb_markers_group_repeat div.cmb-repeatable-grouping' ).last().attr( 'data-iterator' ) );
        }
        return index;
    }


}( jQuery, window.MapsBuilderAdmin || ( window.MapsBuilderAdmin = {} ) ) );



