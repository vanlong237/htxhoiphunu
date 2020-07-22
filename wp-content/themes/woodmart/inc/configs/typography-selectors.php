<?php if ( ! defined('WOODMART_THEME_DIR')) exit('No direct script access allowed');

/**
 * ------------------------------------------------------------------------------------------------
 * Elements selectors for advanced typography options
 * ------------------------------------------------------------------------------------------------
 */

return apply_filters( 'woodmart_typography_selectors', array(
    'main_nav' => array(
        'title' => 'Main navigation',
    ),
    'main_navigation' => array(
        'title' => 'Main navigation links',
        'selector' => '.woodmart-navigation.main-nav .item-level-0 > a',
        'selector-hover' => 'body .woodmart-navigation.main-nav .item-level-0:hover > a, body .woodmart-navigation.main-nav .item-level-0.current-menu-item > a'
    ),
    'mega_menu_drop_first_level' => array(
        'title' => 'Menu dropdowns first level',
        'selector' => '.woodmart-navigation .menu-mega-dropdown .sub-menu-dropdown .sub-menu > li > a',
        'selector-hover' => 'body .woodmart-navigation .menu-mega-dropdown .sub-menu-dropdown .sub-menu > li > a:hover'
    ),
    'mega_menu_drop_second_level' => array(
        'title' => 'Menu dropdowns second level',
        'selector' => '.woodmart-navigation .menu-mega-dropdown .sub-menu-dropdown .sub-sub-menu li a',
        'selector-hover' => 'body .woodmart-navigation .menu-mega-dropdown .sub-menu-dropdown .sub-sub-menu li a:hover'
    ),
    'simple_dropdown' => array(
        'title' => 'Menu links on simple dropdowns',
        'selector' => '.woodmart-navigation .menu-simple-dropdown .sub-menu-dropdown li a',
        'selector-hover' => '.woodmart-navigation .menu-simple-dropdown .sub-menu-dropdown li a:hover'
    ),
    'secondary_nav' => array(
        'title' => 'Other navigations',
    ),
    'secondary_navigation' => array(
        'title' => 'Secondary navigation links',
        'selector' => '.woodmart-navigation.whb-secondary-menu .item-level-0 > a',
        'selector-hover' => '.woodmart-navigation.whb-secondary-menu .item-level-0:hover > a, .woodmart-navigation.whb-secondary-menu .item-level-0.current-menu-item > a'
    ),
    'browse_categories' => array(
        'title' => '"Browse categories" title',
        'selector' => '.header-categories-nav .menu-opener',
        'selector-hover' => '.header-categories-nav .menu-opener:hover'
    ),
    'category_navigation' => array(
        'title' => 'Categories navigation links',
        'selector' => '.woodmart-navigation.vertical-navigation .item-level-0 > a',
        'selector-hover' => '.woodmart-navigation.vertical-navigation .item-level-0:hover > a'
    ),
    'my_account' => array(
        'title' => 'My account links in the header',
        'selector' => '.woodmart-navigation.woodmart-header-links .item-level-0 > a',
        'selector-hover' => '.woodmart-navigation.woodmart-header-links .item-level-0:hover > a'
    ),
    'mobile_nav' => array(
        'title' => 'Mobile menu',
    ),
    'mobile_menu_first_level' => array(
        'title' => 'Mobile menu first level',
        'selector' => '.mobile-nav .site-mobile-menu > li > a',
        'selector-hover' => '.mobile-nav .site-mobile-menu > li > a:hover, .mobile-nav .site-mobile-menu > li.current-menu-item > a'
    ),
    'mobile_menu_second_level' => array(
        'title' => 'Mobile menu second level',
        'selector' => '.mobile-nav .site-mobile-menu .sub-menu li a',
        'selector-hover' => '.mobile-nav .site-mobile-menu .sub-menu li a:hover, .mobile-nav .site-mobile-menu .sub-menu li.current-menu-item > a'
    ),
    'page_header' => array(
        'title' => 'Page heading',
    ),
    'page_title' => array(
        'title' => 'Page title',
        'selector' => '.page-title > .container .entry-title'
    ),
    'page_title_bredcrumps' => array(
        'title' => 'Breadcrumbs links',
        'selector' => '.page-title .breadcrumbs a, .page-title .breadcrumbs span, .page-title .yoast-breadcrumb a, .page-title .yoast-breadcrumb span',
        'selector-hover' => '.page-title .breadcrumbs a:hover, .page-title .yoast-breadcrumb a:hover'
    ),
    'products_categories' => array(
        'title' => 'Products and categories',
    ),
    'product_title' => array(
        'title' => 'Product grid title',
        'selector' => '.product.product-grid-item .product-title',
        'selector-hover' => '.product.product-grid-item .product-title a:hover'
    ),
    'product_price' => array(
        'title' => 'Product grid price',
        'selector' => '.product-grid-item .price > .amount, .product-grid-item .price ins > .amount'
    ),
    'product_old_price' => array(
        'title' => 'Product old price',
        'selector' => '.product.product-grid-item del, .product.product-grid-item del .amount, .product-image-summary .summary-inner > .price del, .product-image-summary .summary-inner > .price del .amount'
    ),
    'product_category_title' => array(
        'title' => 'Category title',
        'selector' => '.product.category-grid-item .category-title, .product.category-grid-item.cat-design-replace-title .category-title, .categories-style-masonry-first .category-grid-item:first-child .category-title'
    ),
    'product_category_count' => array(
        'title' => 'Category products count',
        'selector' => '.product.category-grid-item .more-products, .product.category-grid-item.cat-design-replace-title .more-products',
        'selector-hover' => '.product.category-grid-item .more-products a:hover'
    ),
    'single_product' => array(
        'title' => 'Single product',
    ),
    'product_title_single_page' => array(
        'title' => 'Single product title',
        'selector' => '.product-image-summary-wrap .entry-title'
    ),
    'product_price_single_page' => array(
        'title' => 'Single product price',
        'selector' => '.product-image-summary-wrap .summary-inner > .price > .amount, .product-image-summary-wrap .woodmart-scroll-content > .price > .amount, .product-image-summary-wrap .summary-inner > .price > ins .amount, .product-image-summary-wrap .woodmart-scroll-content > .price > ins .amount'
    ),
    'product_variable_price_single_page' => array(
        'title' => 'Variable product price',
        'selector' => '.product-image-summary-wrap .variations_form .woocommerce-variation-price .price > .amount, .product-image-summary-wrap .variations_form .woocommerce-variation-price .price > ins .amount'
    ),
    'blog' => array(
        'title' => 'Blog',
    ),
    'blog_title' => array(
        'title' => 'Blog post title',
        'selector' => '.post.blog-post-loop .entry-title',
        'selector-hover' => '.post.blog-post-loop .entry-title a:hover'
    ),
    'blog_title_shortcode' => array(
        'title' => 'Blog title on WPBakery element',
        'selector' => '.blog-shortcode .post.blog-post-loop .entry-title',
        'selector-hover' => '.blog-shortcode .post.blog-post-loop .entry-title a:hover'
    ),
    'blog_title_carousel' => array(
        'title' => 'Blog title on carousel',
        'selector' => '.slider-type-post .post.blog-post-loop .entry-title',
        'selector-hover' => '.slider-type-post .post.blog-post-loop .entry-title a:hover'
    ),
    'blog_title_sinle_post' => array(
        'title' => 'Blog title on single post',
        'selector' => '.post-single-page .entry-title'
    ),
    'custom_selector' => array(
        'title' => 'Write your own selector',
    ),
    'custom' => array(
        'title' => 'Custom selector',
        'selector' => 'custom'
    ),
) );