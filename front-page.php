<?php
/**
 * LAZED homepage.
 *
 * WordPress uses this file automatically for the site's front page.
 *
 * =============================================================================
 * HOW TO CHANGE THE HOMEPAGE
 * =============================================================================
 * Every piece of editable text lives in the $lzd array directly below. Change
 * it there and the page updates. You should not need to touch anything after
 * the line that says "TEMPLATE STARTS HERE".
 *
 * Nothing here is an image of text. The split banner is real HTML, so it is
 * editable, readable by Google, works with screen readers, and reflows on a
 * phone. Swapping a promo is a one line change, not a new graphic.
 *
 * Products are pulled live from WooCommerce by category. Add a product in
 * wp-admin and it appears here on its own, already styled.
 *
 * PAGE ORDER
 *   1. Split banner      THC | Mushrooms, each jumps to its lineup below
 *   2. Trust strip       reviews, shipping, lab testing
 *   3. Flavour marquee
 *   4. THC lineup        gummies, vapes, flower
 *   5. Mushroom lineup   gummies, trippy sippy
 *   6. The whole team    scrolling rail of everything
 *   7. Promises
 *   8. Free samples
 * =============================================================================
 *
 * @package lazed
 */

defined( 'ABSPATH' ) || exit;

$lzd = array(

	/* ---------------------------------------------------------------------
	 * 1. SPLIT BANNER
	 *
	 * Two panels. 'anchor' must match the 'id' of a family further down,
	 * which is what makes the button jump to that section.
	 * ------------------------------------------------------------------ */

	'split'        => array(

		array(
			'class'    => 'lzd-split-thc',
			// Background artwork. Drop the file into assets/images/ and name
			// it here. Leave '' for a plain pastel panel.
			'art'      => 'thc-floral.webp',
			// How the artwork sits in the panel, if it needs nudging:
			// 'center', 'top right', 'bottom right', etc.
			'art_pos'  => 'center',
			'badge'    => '',         // put a promo here to show the badge again
			'kicker'   => 'THC Gummies, Vapes &amp; Flower',
			'headline' => 'Munch. Laugh.<br>Lazed.',
			'text'     => 'Four strengths, from a gentle 25mg micro up to a serious 200mg. Same clean recipe every time.',
			'cta'      => 'See the THC lineup',
			'anchor'   => 'thc',
			// Pulls this product's photo from WooCommerce, so it follows the
			// Media Library and keeps working on staging. 303 = OG 600MG.
			'product'  => 303,
			// Optional override for a lifestyle shot. Wins over 'product'.
			'image'    => '',
		),

		array(
			'class'    => 'lzd-split-mush',
			'art'      => 'mushroom-swirl.webp',
			'art_pos'  => 'center',
			'badge'    => '',
			'kicker'   => 'Magic Mushroom Gummies &amp; Sips',
			'headline' => 'Lazed and<br>trippin&rsquo;.',
			'text'     => 'Our nootropic blend in a chewable gummy or a drinkable shot. Brighter, more social, a different kind of lift.',
			'cta'      => 'See the mushroom lineup',
			'anchor'   => 'mushrooms',
			'product'  => 2308,   // Magic Nootropic 4G
			'image'    => '',
		),
	),

	/* ---------------------------------------------------------------------
	 * 2. TRUST STRIP
	 * ------------------------------------------------------------------ */

	'trust'        => array(
		array( 'big' => '4.9★',  'small' => 'from 55+ verified reviews' ),
		array( 'big' => 'Free',  'small' => 'shipping on orders over $50' ),
		array( 'big' => 'Lab',   'small' => 'tested, every single batch' ),
	),

	/* ---------------------------------------------------------------------
	 * 3. FLAVOUR MARQUEE
	 * ------------------------------------------------------------------ */

	'marquee'      => array( '🍓 STRAWBERRY', '🍍 PINEAPPLE', '🍉 WATERMELON', '🍏 GREEN APPLE', '🥭 MANGO', '🫐 BLUEBERRY', '🍊 ORANGE', '🍋 LIME' ),

	/* ---------------------------------------------------------------------
	 * 4 & 5. THE LINEUPS
	 *
	 * Each family is a section with its own heading and several product
	 * groups inside it. 'category' must match a WooCommerce category SLUG,
	 * which you can find under Products > Categories in wp-admin.
	 *
	 * To reorder the page, reorder this array.
	 * ------------------------------------------------------------------ */

	'families'     => array(

		array(
			'id'     => 'thc',
			'class'  => '',
			'kicker' => 'THC',
			'title'  => 'The THC lineup',
			'intro'  => 'Start wherever you are comfortable. If you are new, half a gummy and a full hour is the honest answer.',
			'groups' => array(
				array( 'title' => 'Gummies', 'category' => 'gummies', 'limit' => 4, 'columns' => 4 ),
				// Comma separated slugs pull several categories into one row.
				array( 'title' => 'Vapes &amp; Flower', 'category' => 'vapes,flower', 'limit' => 5, 'columns' => 5 ),
			),
		),

		array(
			'id'     => 'mushrooms',
			'class'  => 'lzd-family-mush',
			'kicker' => 'Magic Mushrooms',
			'title'  => 'The mushroom lineup',
			'intro'  => 'Our nootropic blend, in whichever format suits the moment. Chew it or sip it.',
			'groups' => array(
				// Gummies and sips together on one row. 'narrow' centres it,
				// since two products in a full width grid look stranded.
				array( 'title' => 'Gummies &amp; Sips', 'category' => 'mushrooom-gummies,sips', 'limit' => 4, 'columns' => 2, 'narrow' => true ),
			),
		),
	),

	/* ---------------------------------------------------------------------
	 * 6. THE WHOLE TEAM  ·  scrolling rail
	 * Hovering pauses it so people can read and click.
	 * ------------------------------------------------------------------ */

	'rail_kicker'  => 'Everything under one roof',
	'rail_title'   => 'The whole team',
	'rail_intro'   => 'Hover to slow it down, then click anything that looks good.',
	'rail_limit'   => 12,

	/* ---------------------------------------------------------------------
	 * 7. PROMISES
	 * ------------------------------------------------------------------ */

	'promises'     => array(
		array( 'icon' => '🔬', 'name' => 'Lab tested',        'text' => 'Every batch goes to a third party lab, and the certificates are posted for anyone who wants to read them.' ),
		array( 'icon' => '📦', 'name' => 'Discreet shipping', 'text' => 'Plain packaging with no branding outside. Most orders arrive in two to four days.' ),
		array( 'icon' => '🌱', 'name' => 'Vegan &amp; clean',     'text' => 'Pectin based, gluten free, and made without unnecessary additives or fillers.' ),
		array( 'icon' => '💬', 'name' => 'Real answers',      'text' => 'Questions about dosing are welcome. We would rather you ask than guess.' ),
	),

	/* ---------------------------------------------------------------------
	 * 8. FREE SAMPLES
	 * ------------------------------------------------------------------ */

	'sample_title' => 'Still not sure? Try a free sample',
	'sample_text'  => 'One sample equals one gummy, on us. See how a dose actually feels before you commit to a full bag.',
	'sample_ctas'  => array(
		array( 'label' => 'Free THC sample 🥦',      'url' => '/product/sample-gummy-gummies-thc-og-600mg/' ),
		array( 'label' => 'Free mushroom sample 🍄', 'url' => '/product/sample-gummies-mushrooms-magic-nootropic-4g/' ),
	),
);

/* =============================================================================
 * TEMPLATE STARTS HERE
 * Below this line is layout. You should rarely need to edit it.
 * ========================================================================== */

get_header();
?>

<div id="lzd-home">

	<!-- ===================== 1. SPLIT BANNER ===================== -->
	<section class="lzd-split">
		<?php
		foreach ( $lzd['split'] as $panel ) :

			/* Background artwork, if the file is present in assets/images/.
			   Applied as an inline background so each panel can use its own
			   file without needing a CSS rule per panel. */
			$art_url   = function_exists( 'lazed_art_url' ) && ! empty( $panel['art'] ) ? lazed_art_url( $panel['art'] ) : '';
			$art_style = '';

			if ( $art_url ) {
				$art_style = sprintf(
					' style="background-image:url(%s);background-position:%s"',
					esc_url( $art_url ),
					esc_attr( ! empty( $panel['art_pos'] ) ? $panel['art_pos'] : 'center' )
				);
			}
			?>
			<div class="lzd-split-panel <?php echo esc_attr( $panel['class'] ); ?><?php echo $art_url ? ' has-art' : ''; ?>"<?php echo $art_style; // phpcs:ignore WordPress.Security.EscapeOutput ?>>

				<?php if ( ! empty( $panel['badge'] ) ) : ?>
					<span class="lzd-split-badge"><?php echo esc_html( $panel['badge'] ); ?></span>
				<?php endif; ?>

				<span class="lzd-split-kicker"><?php echo wp_kses_post( $panel['kicker'] ); ?></span>
				<h2><?php echo wp_kses_post( $panel['headline'] ); ?></h2>
				<p><?php echo esc_html( $panel['text'] ); ?></p>

				<a class="button" href="#<?php echo esc_attr( $panel['anchor'] ); ?>"><?php echo esc_html( $panel['cta'] ); ?></a>

				<div class="lzd-split-art">
					<?php
					/* An explicit URL wins, for when you want a lifestyle shot.
					   Otherwise pull the photo straight from the WooCommerce
					   product, which means it survives re-uploads and resolves
					   correctly on staging instead of pointing at production. */
					if ( ! empty( $panel['image'] ) ) {
						printf( '<img src="%s" alt="" loading="eager">', esc_url( $panel['image'] ) );
					} elseif ( ! empty( $panel['product'] ) && function_exists( 'wc_get_product' ) ) {
						$panel_product = wc_get_product( absint( $panel['product'] ) );
						if ( $panel_product ) {
							echo wp_kses_post( $panel_product->get_image( 'woocommerce_single' ) );
						}
					}
					?>
				</div>

			</div>
		<?php endforeach; ?>
	</section>

	<!-- ===================== 2. TRUST STRIP ===================== -->
	<?php if ( ! empty( $lzd['trust'] ) ) : ?>
		<div class="lzd-trust">
			<div class="col-full lzd-trust-inner">
				<?php foreach ( $lzd['trust'] as $t ) : ?>
					<div class="lzd-trust-item">
						<b><?php echo esc_html( $t['big'] ); ?></b>
						<span><?php echo esc_html( $t['small'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<!-- ===================== 3. MARQUEE ===================== -->
	<?php if ( ! empty( $lzd['marquee'] ) ) : ?>
		<div class="lzd-marq" aria-hidden="true">
			<div class="lzd-marq-track">
				<?php for ( $pass = 0; $pass < 2; $pass++ ) : ?>
					<?php foreach ( $lzd['marquee'] as $word ) : ?>
						<span><?php echo esc_html( $word ); ?></span>
					<?php endforeach; ?>
				<?php endfor; ?>
			</div>
		</div>
	<?php endif; ?>

	<!-- ===================== 4 & 5. THE LINEUPS ===================== -->
	<?php foreach ( $lzd['families'] as $family ) : ?>

		<section class="lzd-family <?php echo esc_attr( $family['class'] ); ?>" id="<?php echo esc_attr( $family['id'] ); ?>">
			<div class="col-full">

				<header class="lzd-family-head">
					<span class="lzd-kicker"><?php echo wp_kses_post( $family['kicker'] ); ?></span>
					<h2><?php echo esc_html( $family['title'] ); ?></h2>
					<p><?php echo esc_html( $family['intro'] ); ?></p>
				</header>

				<?php foreach ( $family['groups'] as $group ) : ?>
					<div class="lzd-group<?php echo ! empty( $group['narrow'] ) ? ' lzd-group-narrow' : ''; ?>">
						<div class="lzd-group-title"><h3><?php echo wp_kses_post( $group['title'] ); ?></h3></div>
						<?php
						// Live from WooCommerce. Nothing about products is stored here.
						echo do_shortcode( sprintf(
							'[products limit="%d" columns="%d" category="%s" orderby="menu_order" paginate="false"]',
							absint( $group['limit'] ),
							absint( $group['columns'] ),
							esc_attr( $group['category'] )
						) );
						?>
					</div>
				<?php endforeach; ?>

			</div>
		</section>

	<?php endforeach; ?>

	<!-- ===================== 6. THE WHOLE TEAM ===================== -->
	<?php if ( function_exists( 'lazed_wave' ) ) { lazed_wave( 'var(--lzd-blush)' ); } ?>

	<section class="lzd-lineup">
		<div class="col-full">
			<header class="lzd-section-head">
				<span class="lzd-kicker"><?php echo esc_html( $lzd['rail_kicker'] ); ?></span>
				<h2><?php echo esc_html( $lzd['rail_title'] ); ?></h2>
				<p><?php echo esc_html( $lzd['rail_intro'] ); ?></p>
			</header>
		</div>

		<?php
		$rail_products = function_exists( 'wc_get_products' ) ? wc_get_products( array(
			'status'  => 'publish',
			'limit'   => absint( $lzd['rail_limit'] ),
			'orderby' => 'menu_order',
			'order'   => 'ASC',
		) ) : array();

		if ( $rail_products ) :
			?>
			<div class="lzd-rail">
				<div class="lzd-rail-track">
					<?php
					// Printed twice so the loop joins seamlessly.
					for ( $pass = 0; $pass < 2; $pass++ ) :
						foreach ( $rail_products as $rp ) :
							?>
							<a class="lzd-rail-item" href="<?php echo esc_url( $rp->get_permalink() ); ?>"
							   <?php echo 1 === $pass ? 'aria-hidden="true" tabindex="-1"' : ''; ?>>
								<div class="lzd-rail-media"><?php echo wp_kses_post( $rp->get_image( 'woocommerce_thumbnail' ) ); ?></div>
								<h4><?php echo esc_html( wp_trim_words( $rp->get_name(), 5, '' ) ); ?></h4>
								<div class="lzd-rail-price"><?php echo wp_kses_post( $rp->get_price_html() ); ?></div>
							</a>
							<?php
						endforeach;
					endfor;
					?>
				</div>
			</div>
		<?php endif; ?>
	</section>

	<?php if ( function_exists( 'lazed_wave' ) ) { lazed_wave( 'var(--lzd-cream)' ); } ?>

	<!-- ===================== 7. PROMISES ===================== -->
	<section class="lzd-section">
		<div class="col-full">
			<div class="lzd-promises">
				<?php foreach ( $lzd['promises'] as $p ) : ?>
					<div class="lzd-card lzd-promise">
						<span class="lzd-promise-icon"><?php echo esc_html( $p['icon'] ); ?></span>
						<h3><?php echo wp_kses_post( $p['name'] ); ?></h3>
						<p><?php echo wp_kses_post( $p['text'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ===================== 8. FREE SAMPLES ===================== -->
	<section class="lzd-samples">
		<div class="col-full">
			<h2><?php echo esc_html( $lzd['sample_title'] ); ?></h2>
			<p><?php echo esc_html( $lzd['sample_text'] ); ?></p>
			<div class="lzd-hero-cta">
				<?php foreach ( $lzd['sample_ctas'] as $i => $cta ) : ?>
					<a class="button<?php echo 0 === $i ? ' alt' : ''; ?>" href="<?php echo esc_url( $cta['url'] ); ?>"><?php echo esc_html( $cta['label'] ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

</div>

<?php
get_footer();
