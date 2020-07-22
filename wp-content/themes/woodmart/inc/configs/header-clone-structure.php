<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );}

/**
 * ------------------------------------------------------------------------------------------------
 * Header clone structure template
 * ------------------------------------------------------------------------------------------------
 */

$template = '
    <div class="whb-sticky-header whb-clone whb-main-header <%wrapperClasses%>">
        <div class="<%cloneClass%>">
            <div class="container">
                <div class="whb-flex-row whb-general-header-inner">
                    <div class="whb-column whb-col-left whb-visible-lg">
                        <%.site-logo%>
                    </div>
                    <div class="whb-column whb-col-center whb-visible-lg">
                        <%.main-nav%>
                    </div>
                    <div class="whb-column whb-col-right whb-visible-lg">
                        <%.woodmart-header-links%>
                        <%.search-button:not(.mobile-search-icon)%>
						<%.woodmart-wishlist-info-widget%>
                        <%.woodmart-compare-info-widget%>
                        <%.woodmart-shopping-cart%>
                        <%.full-screen-burger-icon%>
                    </div>
                    <%.whb-mobile-left%>
                    <%.whb-mobile-center%>
                    <%.whb-mobile-right%>
                </div>
            </div>
        </div>
    </div>
';

return apply_filters( 'woodmart_header_clone_template', $template );
