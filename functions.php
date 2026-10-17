<?php
/**
 * LAZED theme functions.
 *
 * Child theme of Storefront.
 *
 * Scope rule for this file: presentation only. Nothing here should touch
 * payment, tax, shipping or order logic. Those belong to WooCommerce and
 * the Bankful gateway, and changing them from a theme is how stores break.
 *
 * @package lazed
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'LAZED_VERSION', '0.2.0' );

/* -------------------------------------------------------------------------
 * Styles and fonts
 * ---------------------------------------------------------------------- */

/**
 * Load the child stylesheet after Storefront's own.
 *
 * Storefront enqueues its stylesheet under the handle 'storefront-style'.
 * Declaring it as a dependency guarantees our rules land afterwards and
 * win without needing !important everywhere.
 */
function lazed_enqueue_styles() {

	/**
	 * Three typefaces, three jobs:
	 *   Shrikhand  heavy groovy display. Big headline moments only.
	 *   Righteous  retro but readable. Buttons, product titles, labels.
	 *   Nunito     neutral and warm. Body copy and long form reading.
	 *
	 * Loaded in a single request so this costs one round trip, not three.
	 */
	wp_enqueue_style(
		'lazed-fonts',
		'https://fonts.googleapis.com/css2?family=Shrikhand&family=Righteous&family=Nunito:wght@400;600;700;800&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'lazed-style',
		get_stylesheet_uri(),
		array( 'storefront-style' ),
		LAZED_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'lazed_enqueue_styles', 20 );

/**
 * Preconnect to Google Fonts so the display font is not render blocking.
 */
function lazed_resource_hints( $hints, $relation ) {
	if ( 'preconnect' === $relation ) {
		$hints[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $hints;
}
add_filter( 'wp_resource_hints', 'lazed_resource_hints', 10, 2 );

/* -------------------------------------------------------------------------
 * Wavy section divider
 * ---------------------------------------------------------------------- */

/**
 * Print a wavy divider between two sections.
 *
 * Decorative only, so it is hidden from assistive technology. Pass the colour
 * of whatever sits BELOW the wave, since the wave is effectively the top edge
 * of the next section bleeding upwards.
 *
 * Usage:  lazed_wave( 'var(--lzd-blush)' );
 *         lazed_wave( 'var(--lzd-cream)', true );   // flipped
 *
 * @param string $fill CSS colour for the wave body.
 * @param bool   $flip Mirror it vertically.
 */
function lazed_wave( $fill = 'var(--lzd-cream)', $flip = false ) {

	$transform = $flip ? ' style="transform:scaleY(-1)"' : '';
	?>
	<div class="lzd-wave" aria-hidden="true"<?php echo $transform; // phpcs:ignore ?>>
		<svg viewBox="0 0 1200 60" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M0,28 C150,2 270,56 420,34 C570,12 690,54 840,32 C990,10 1100,44 1200,24 L1200,60 L0,60 Z"
			      fill="<?php echo esc_attr( $fill ); ?>" />
		</svg>
	</div>
	<?php
}

/**
 * Resolve the URL of a decorative artwork file shipped with the theme.
 *
 * Artwork lives in the theme at assets/images/ rather than in the Media
 * Library, because it is part of the design rather than content. That means
 * it is versioned in git with the rest of the theme and deploys alongside
 * it, so staging and production never drift apart.
 *
 * Returns an empty string when the file is missing, so a panel degrades to
 * its plain pastel background instead of showing a broken image.
 *
 * @param  string $file Filename inside assets/images/, e.g. 'thc-floral.png'.
 * @return string Full URL, or '' if the file is not there.
 */
function lazed_art_url( $file ) {

	$file = trim( (string) $file );

	if ( '' === $file ) {
		return '';
	}

	// Filename only. Keeps this from being pointed anywhere outside the folder.
	$file = basename( $file );

	if ( ! file_exists( get_stylesheet_directory() . '/assets/images/' . $file ) ) {
		return '';
	}

	return get_stylesheet_directory_uri() . '/assets/images/' . $file;
}

/* -------------------------------------------------------------------------
 * Theme setup
 * ---------------------------------------------------------------------- */

function lazed_setup() {

	add_theme_support( 'custom-logo', array(
		'height'      => 120,
		'width'       => 400,
		'flex-height' => true,
		'flex-width'  => true,
	) );

	add_theme_support( 'title-tag' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	// WooCommerce gallery features. Safe, visual only.
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'lazed_setup', 20 );

/* -------------------------------------------------------------------------
 * Shop layout
 *
 * These are display preferences. Adjust the numbers freely; nothing here
 * can affect pricing or checkout.
 * ---------------------------------------------------------------------- */

/** Products per row in shop and category grids. */
function lazed_loop_columns() {
	return 3;
}
add_filter( 'storefront_loop_columns', 'lazed_loop_columns' );
add_filter( 'loop_shop_columns', 'lazed_loop_columns' );

/** Products shown before pagination kicks in. */
function lazed_products_per_page() {
	return 12;
}
add_filter( 'loop_shop_per_page', 'lazed_products_per_page', 20 );

/** Related products: 3 across, 3 shown, so the row stays even. */
function lazed_related_products_args( $args ) {
	$args['posts_per_page'] = 3;
	$args['columns']        = 3;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'lazed_related_products_args', 20 );

/* -------------------------------------------------------------------------
 * Announcement bar
 *
 * Edit the text on the line marked below. It appears on every page.
 * Set it to an empty string to hide the bar entirely.
 * ---------------------------------------------------------------------- */

function lazed_announcement_bar() {

	$message = 'Free shipping on orders over $50 · Discreet packaging · Third party lab tested'; // EDIT ME

	if ( '' === trim( $message ) ) {
		return;
	}

	printf(
		'<div class="lzd-announce">%s</div>',
		esc_html( $message )
	);
}
add_action( 'storefront_before_header', 'lazed_announcement_bar', 5 );

/* -------------------------------------------------------------------------
 * Footer
 * ---------------------------------------------------------------------- */

/** Remove the "Built with Storefront & WooCommerce" credit line. */
function lazed_remove_storefront_credit() {
	remove_action( 'storefront_footer', 'storefront_credit', 20 );
}
add_action( 'init', 'lazed_remove_storefront_credit' );

/**
 * Print the compliance block on every page.
 *
 * This is a legal requirement for a hemp retailer, so it is rendered by the
 * theme rather than placed in a widget that could be deleted by accident.
 * Do not remove without checking with whoever handles compliance.
 */
function lazed_compliance_notice() {
	?>
	<div class="lzd-compliance">
		<p>
			Must be 21 or older to purchase. Keep out of reach of children and pets.
			Do not drive or operate machinery after use. Consult a physician before use
			if you are pregnant, nursing, or taking medication.
		</p>
		<p>
			ALL PRODUCTS SOLD ON THIS SITE ARE DERIVED FROM 100% LEGAL HEMP AND IN
			COMPLIANCE WITH THE 2018 FARM BILL. The statements made regarding these
			products have not been evaluated by the Food and Drug Administration. The
			efficacy of these products and the testimonials made have not been confirmed
			by FDA approved research. These products are not intended to diagnose, treat,
			cure or prevent any disease.
		</p>
	</div>
	<?php
}
add_action( 'storefront_footer', 'lazed_compliance_notice', 30 );

/* -------------------------------------------------------------------------
 * Small touches
 * ---------------------------------------------------------------------- */

/**
 * Replace the default "Add to cart" wording on variable products.
 *
 * Variable products cannot be added in one click because WooCommerce needs
 * a variation selected first. "Pick your flavor" sets the right expectation
 * and reduces the number of people who click and wonder why nothing happened.
 */
function lazed_variable_add_to_cart_text( $text, $product ) {
	if ( $product && $product->is_type( 'variable' ) ) {
		return 'Pick your flavor';
	}
	return $text;
}
add_filter( 'woocommerce_product_add_to_cart_text', 'lazed_variable_add_to_cart_text', 10, 2 );

/**
 * Move the WooCommerce breadcrumb out of the way on single products.
 * Purely cosmetic.
 */
function lazed_tidy_breadcrumbs() {
	if ( is_product() ) {
		remove_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10 );
	}
}
add_action( 'wp', 'lazed_tidy_breadcrumbs' );

/**
 * Body classes so CSS can target specific contexts cleanly.
 */
function lazed_body_classes( $classes ) {
	$classes[] = 'lzd';
	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		$classes[] = 'lzd-checkout';
	}
	return $classes;
}
add_filter( 'body_class', 'lazed_body_classes' );
