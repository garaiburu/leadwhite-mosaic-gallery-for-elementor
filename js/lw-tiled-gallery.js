( function () {

    'use strict';

    /**
     * Round an array of floats to integers while preserving their sum.
     * Port of Jetpack_Constrained_Array_Rounding (GPL-2.0).
     *
     * @param {number[]} values  Array of floats to round
     * @param {number}   target  The integer sum the result must equal
     * @returns {number[]}
     */
    function constrainedRound( values, target ) {
        var floored   = values.map( Math.floor );
        var fractions = values.map( function ( v ) { return v - Math.floor( v ); } );
        var lowerSum  = floored.reduce( function ( a, b ) { return a + b; }, 0 );
        var diff      = Math.round( target ) - lowerSum;

        var indices = values.map( function ( _, i ) { return i; } );
        indices.sort( function ( a, b ) { return fractions[ b ] - fractions[ a ]; } );

        for ( var i = 0; i < diff; i++ ) {
            floored[ indices[ i % indices.length ] ]++;
        }

        return floored;
    }

    /**
     * Resize a gallery instance.
     * Uses window.innerWidth for breakpoint (viewport concept).
     * Uses gallery.clientWidth for scaling (actual rendered width).
     *
     * @param {HTMLElement} gallery
     */
    function resize( gallery ) {
        var gap           = parseInt( gallery.dataset.gap, 10 ) || 4;
        var breakpoint    = parseInt( gallery.dataset.breakpoint, 10 ) || 480;
        var originalWidth = parseInt( gallery.dataset.originalWidth, 10 ) || 1000;
        var currentWidth  = gallery.clientWidth;

        // Use viewport width for breakpoint decision to avoid ResizeObserver loops
        if ( window.innerWidth <= breakpoint ) {
            if ( ! gallery.classList.contains( 'is-stacked' ) ) {
                gallery.classList.add( 'is-stacked' );
                gallery.querySelectorAll( '.lw-tiled-row, .lw-tiled-group, .lw-tiled-item, .lw-tiled-item img' ).forEach( function ( el ) {
                    el.style.cssText = '';
                } );
            }
            return;
        }

        gallery.classList.remove( 'is-stacked' );

        var resizeRatio = currentWidth / originalWidth;
        var scaledGap   = Math.floor( resizeRatio * gap );

        // Pre-calculate constrained pixel widths per row
        var rows = Array.from( gallery.querySelectorAll( '.lw-tiled-row' ) );

        rows.forEach( function ( row ) {
            var groups = Array.from( row.querySelectorAll( '.lw-tiled-group' ) );
            if ( ! groups.length ) { return; }

            var rawWidths = groups.map( function ( g ) {
                var w = parseInt( g.dataset.originalWidth, 10 );
                return resizeRatio * ( isNaN( w ) ? 100 : w );
            } );

            var targetWidth = currentWidth - scaledGap * ( groups.length - 1 );
            var rounded     = constrainedRound( rawWidths, targetWidth );

            groups.forEach( function ( g, i ) {
                g._scaledWidth = rounded[ i ];
            } );
        } );

        // Apply row styles
        rows.forEach( function ( row, rowIndex ) {
            row.style.display      = 'flex';
            row.style.flexWrap     = 'nowrap';
            row.style.alignItems   = 'stretch';
            row.style.overflow     = 'hidden';
            row.style.marginBottom = rowIndex < rows.length - 1 ? scaledGap + 'px' : '0';
            row.style.height       = Math.floor( resizeRatio * parseInt( row.dataset.originalHeight, 10 ) ) + 'px';
        } );

        // Apply group styles
        gallery.querySelectorAll( '.lw-tiled-group' ).forEach( function ( group ) {
            var siblings = Array.from( group.parentElement.querySelectorAll( '.lw-tiled-group' ) );
            var idx      = siblings.indexOf( group );
            group.style.display       = 'flex';
            group.style.flexDirection = 'column';
            group.style.overflow      = 'hidden';
            group.style.height        = group.parentElement.style.height;
            group.style.marginLeft    = idx > 0 ? scaledGap + 'px' : '0';
            group.style.flex          = '';
            group.style.width         = ( group._scaledWidth || 100 ) + 'px';
        } );

        // Apply item and image styles
        gallery.querySelectorAll( '.lw-tiled-item' ).forEach( function ( item ) {
            var siblings = Array.from( item.parentElement.querySelectorAll( '.lw-tiled-item' ) );
            var idx      = siblings.indexOf( item );
            item.style.overflow  = 'hidden';
            item.style.marginTop = idx > 0 ? scaledGap + 'px' : '0';
            item.style.flex      = '1';
            item.style.minHeight = '0';

            var img = item.querySelector( 'img' );
            if ( img ) {
                img.style.width     = '100%';
                img.style.height    = '100%';
                img.style.objectFit = 'cover';
                img.style.display   = 'block';
            }
        } );
    }

    function debounce( fn, delay ) {
        var timer;
        return function () {
            clearTimeout( timer );
            timer = setTimeout( fn, delay );
        };
    }

    function init() {
        var galleries = document.querySelectorAll( '.lw-tiled-gallery' );
        if ( ! galleries.length ) { return; }

        galleries.forEach( function ( gallery ) {
            resize( gallery );
        } );

        window.addEventListener( 'resize', debounce( function () {
            galleries.forEach( function ( gallery ) {
                resize( gallery );
            } );
        }, 100 ) );
    }

    if ( document.readyState === 'loading' ) {
        document.addEventListener( 'DOMContentLoaded', init );
    } else {
        init();
    }

    window.addEventListener( 'elementor/frontend/init', function () {
        init();
        elementorFrontend.hooks.addAction(
            'frontend/element_ready/lw_tiled_gallery.default',
            function ( $scope ) {
                var gallery = $scope[ 0 ].querySelector( '.lw-tiled-gallery' );
                if ( gallery ) {
                    resize( gallery );
                }
            }
        );
    } );

}() );