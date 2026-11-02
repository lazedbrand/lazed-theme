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

define( 'LAZED_VERSION', '0.6.2' );

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

/**
 * Main Shop page: show products grouped by category (see shop-grouped.php).
 * Falls back to the normal WooCommerce grid when a shopper sorts, searches,
 * filters or pages through results, so nothing is ever hidden.
 */
function lazed_grouped_shop_template( $template ) {
	if ( ! function_exists( 'is_shop' ) || ! is_shop() || is_search() || is_paged() ) {
		return $template;
	}
	foreach ( array( 'orderby', 'min_price', 'max_price', 'rating_filter', 's' ) as $key ) {
		if ( isset( $_GET[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- read only.
			return $template;
		}
	}
	foreach ( array_keys( $_GET ) as $key ) { // phpcs:ignore WordPress.Security.NonceVerification -- read only.
		if ( 0 === strpos( $key, 'filter_' ) ) {
			return $template;
		}
	}
	$grouped = locate_template( 'shop-grouped.php' );
	return $grouped ? $grouped : $template;
}
add_filter( 'template_include', 'lazed_grouped_shop_template', 99 );

/**
 * Account button in the laptop header, just left of the cart.
 * Says "Log in" for visitors and "My account" once someone is logged in.
 * Phones already have My account in Storefront's bottom bar, so it is
 * hidden there by CSS.
 */
function lazed_header_account() {
	if ( ! function_exists( 'wc_get_page_permalink' ) ) {
		return;
	}
	$label = is_user_logged_in() ? 'My account' : 'Log in';
	?>
	<a class="lzd-account" aria-label="<?php echo esc_attr( $label ); ?>" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
		<svg aria-hidden="true" focusable="false" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/></svg>
		<span><?php echo esc_html( $label ); ?></span>
	</a>
	<?php
}
add_action( 'storefront_header', 'lazed_header_account', 55 );

/* -------------------------------------------------------------------------
 * Announcement bar
 *
 * Edit the text on the line marked below. It appears on every page.
 * Set it to an empty string to hide the bar entirely.
 * ---------------------------------------------------------------------- */

function lazed_announcement_bar() {

	// Edit in Appearance > Customize > Homepage deal banner > Top bar message.
	$message = get_theme_mod( 'lazed_announce_text', 'Free shipping on orders over $50 · Discreet packaging' );

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
 * Footer link menus. Set them in Appearance > Menus > Manage Locations.
 * Until a menu is assigned, the default links below are shown, so the
 * footer is never empty.
 */
function lazed_footer_menus() {
	register_nav_menus( array(
		'lazed-footer-help'    => 'Footer: Help column',
		'lazed-footer-company' => 'Footer: Company column',
	) );
}
add_action( 'after_setup_theme', 'lazed_footer_menus', 25 );

/** One footer column: a menu if one is assigned, otherwise the defaults. */
function lazed_footer_column( $location, $title, $defaults ) {
	?>
	<nav class="lzd-foot-col" aria-label="<?php echo esc_attr( $title ); ?>">
		<h2 class="lzd-foot-title"><?php echo esc_html( $title ); ?></h2>
		<?php
		if ( has_nav_menu( $location ) ) {
			wp_nav_menu( array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => 'lzd-foot-links',
				'depth'          => 1,
			) );
		} else {
			echo '<ul class="lzd-foot-links">';
			foreach ( $defaults as $label => $path ) {
				printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( $path ) ), esc_html( $label ) );
			}
			echo '</ul>';
		}
		?>
	</nav>
	<?php
}

/** Top of the footer: wordmark and tagline on the left, link columns on the right. */
function lazed_footer_main() {
	?>
	<div class="lzd-foot-main">
		<div class="lzd-foot-brand">
			<a class="lzd-wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="LAZED home">
				<span>L</span><span>A</span><span>Z</span><span>E</span><span>D</span>
			</a>
			<p class="lzd-foot-tagline">Feel the difference.</p>
		</div>
		<div class="lzd-foot-cols">
			<?php
			lazed_footer_column( 'lazed-footer-help', 'Help', array(
				'FAQ'             => '/faq/',
				'Contact'         => '/contact/',
				'Shipping policy' => '/shipping-policy/',
				'Refund policy'   => '/refund-policy/',
			) );
			lazed_footer_column( 'lazed-footer-company', 'Company', array(
				'About'     => '/about/',
				'News'      => '/news/',
				'COA'       => '/coa/',
				// 'Wholesale' => '/wholesale/', // Paused for now. Remove the // to bring it back.
			) );
			?>
		</div>
	</div>
	<?php
}
add_action( 'storefront_footer', 'lazed_footer_main', 15 );

/** Bottom row: copyright and small links on the left, payment methods on the right. */
function lazed_footer_bottom() {
	?>
	<div class="lzd-foot-bottom">
		<p>
			&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Lazed. 21+ only.
			&middot; <a href="<?php echo esc_url( get_privacy_policy_url() ? get_privacy_policy_url() : home_url( '/privacy-policy/' ) ); ?>">Privacy policy</a>
			&middot; <a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>">Terms</a>
		</p>
		<p class="lzd-foot-pay">Visa &middot; Mastercard &middot; Amex &middot; Discover &middot; Apple Pay</p>
	</div>
	<?php
}
add_action( 'storefront_footer', 'lazed_footer_bottom', 40 );

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

/* -------------------------------------------------------------------------
 * Homepage deal banner
 *
 * One big promo banner at the top of the homepage. The images are chosen in
 * Appearance > Customize > Homepage deal banner, so swapping the monthly
 * deal never needs a code change. A wide image is used on laptops and a
 * square one on phones. If no banner is set, the homepage falls back to the
 * THC | Mushrooms split banner.
 * ---------------------------------------------------------------------- */

function lazed_customize_banner( $wp_customize ) {

	$wp_customize->add_section( 'lazed_banner', array(
		'title'       => 'Homepage deal banner',
		'priority'    => 30,
		'description' => 'The big banner at the top of the homepage. Swap these each month for the current deal. Laptop: 2400 x 900. Phone: 1080 x 1080.',
	) );

	$images = array(
		'lazed_banner_wide'  => 'Laptop banner (wide, 2400 x 900)',
		'lazed_banner_phone' => 'Phone banner (square, 1080 x 1080)',
	);

	foreach ( $images as $id => $label ) {
		$wp_customize->add_setting( $id, array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $id, array(
			'label'     => $label,
			'section'   => 'lazed_banner',
			'mime_type' => 'image',
		) ) );
	}

	// Top bar message, shown above the header on every page. Leave empty to hide the bar.
	$wp_customize->add_setting( 'lazed_announce_text', array(
		'default'           => 'Free shipping on orders over $50 · Discreet packaging',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'lazed_announce_text', array(
		'label'       => 'Top bar message (every page)',
		'description' => 'The dark strip above the header. Leave empty to hide it.',
		'section'     => 'lazed_banner',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'lazed_banner_alt', array(
		'default'           => 'Free shipping on orders over $50',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'lazed_banner_alt', array(
		'label'       => 'Describe the deal (for screen readers and Google)',
		'section'     => 'lazed_banner',
		'type'        => 'text',
	) );
}
add_action( 'customize_register', 'lazed_customize_banner' );

/**
 * Print the deal banner. Returns false when no banner image is set, so the
 * homepage knows to show the split banner instead.
 */
function lazed_deal_banner() {

	$wide  = absint( get_theme_mod( 'lazed_banner_wide', 0 ) );
	$phone = absint( get_theme_mod( 'lazed_banner_phone', 0 ) );

	$wide_src  = $wide ? wp_get_attachment_image_src( $wide, 'full' ) : false;
	$phone_src = $phone ? wp_get_attachment_image_src( $phone, 'full' ) : false;

	if ( ! $wide_src && ! $phone_src ) {
		return false;
	}

	// If only one is set, use it everywhere.
	$wide_src  = $wide_src ? $wide_src : $phone_src;
	$phone_src = $phone_src ? $phone_src : $wide_src;
	$alt       = get_theme_mod( 'lazed_banner_alt', 'Free shipping on orders over $50' );

	// Width and height let the browser reserve the right space before the
	// image arrives, so the page below does not jump down when it loads.
	?>
	<section class="lzd-deal">
		<div class="col-full">
			<figure class="lzd-deal-frame">
				<picture>
					<source media="(max-width: 768px)" srcset="<?php echo esc_url( $phone_src[0] ); ?>" width="<?php echo absint( $phone_src[1] ); ?>" height="<?php echo absint( $phone_src[2] ); ?>">
					<img src="<?php echo esc_url( $wide_src[0] ); ?>" width="<?php echo absint( $wide_src[1] ); ?>" height="<?php echo absint( $wide_src[2] ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="eager" fetchpriority="high" decoding="async">
				</picture>
			</figure>
		</div>
	</section>
	<?php
	return true;
}
