<?php
/**
 * Shop page, grouped by category.
 *
 * The main Shop page shows a THC lineup and a mushroom lineup, each split
 * into labelled rows, in the order below. Products inside
 * each section follow the drag order from Products > Sorting.
 *
 * If a shopper picks a sort option, searches or filters, WooCommerce's
 * normal grid is used instead, so nothing is ever hidden.
 *
 * Loaded by lazed_grouped_shop_template() in functions.php.
 * Products and prices are read live from WooCommerce. Nothing is stored here.
 *
 * @package lazed-theme
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * EDIT HERE. To reorder the shop, reorder this list.
 * 'categories' takes category slugs, comma separated.
 * Any category not listed here still shows, at the bottom, so new
 * categories never go missing.
 * ---------------------------------------------------------------------- */
$lzd_shop_sections = array(

	array(
		'id'     => 'thc-lineup',
		'kicker' => '',
		'title'  => 'The THC lineup',
		'groups' => array(
			// Laptop: one row of 4. Phone: 2 + 2.
			array( 'title' => 'Gummies', 'categories' => 'gummies', 'columns' => 4 ),
			// Two rows under one label. Laptop: vapes 2, then flower 3.
			// Phone: vapes 2, then flower 2 + 1 on the left at normal size.
			array(
				'title' => 'Vapes &amp; Flower',
				'rows'  => array(
					array( 'categories' => 'vapes', 'columns' => 3 ),
					array( 'categories' => 'flower', 'columns' => 3 ),
				),
			),
		),
	),

	array(
		'id'     => 'mushroom-lineup',
		'kicker' => '',
		'title'  => 'The mushroom lineup',
		'groups' => array(
			array( 'title' => 'Gummies &amp; Sips', 'categories' => 'mushrooom-gummies,sips', 'columns' => 2, 'narrow' => true ),
		),
	),
);

/* ---------------------------------------------------------------------- */

// Add any other category that has products and is not listed above.
$lzd_listed = array();
foreach ( $lzd_shop_sections as $lzd_s ) {
	foreach ( $lzd_s['groups'] as $lzd_g ) {
		$lzd_rows = isset( $lzd_g['rows'] ) ? $lzd_g['rows'] : array( $lzd_g );
		foreach ( $lzd_rows as $lzd_r ) {
			$lzd_listed = array_merge( $lzd_listed, array_map( 'trim', explode( ',', $lzd_r['categories'] ) ) );
		}
	}
}
$lzd_extra = get_terms( array(
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'parent'     => 0,
) );
if ( ! is_wp_error( $lzd_extra ) ) {
	foreach ( $lzd_extra as $lzd_term ) {
		if ( in_array( $lzd_term->slug, $lzd_listed, true ) || 'uncategorized' === $lzd_term->slug ) {
			continue;
		}
		$lzd_shop_sections[] = array(
			'id'     => 'cat-' . $lzd_term->slug,
			'kicker' => '',
			'title'  => esc_html( $lzd_term->name ),
			'groups' => array(
				array( 'title' => '', 'categories' => $lzd_term->slug, 'columns' => 4 ),
			),
		);
	}
}

// Build each grid now. Drop empty groups, then empty sections, so the
// jump buttons only point at sections that exist.
foreach ( $lzd_shop_sections as $lzd_i => $lzd_s ) {
	foreach ( $lzd_s['groups'] as $lzd_j => $lzd_g ) {
		// A group is one grid, or several stacked 'rows' under one label.
		$lzd_rows = isset( $lzd_g['rows'] ) ? $lzd_g['rows'] : array( $lzd_g );
		$lzd_grid = '';
		foreach ( $lzd_rows as $lzd_r ) {
			$lzd_row = do_shortcode( sprintf(
				'[products limit="-1" columns="%d" category="%s" orderby="menu_order" order="ASC" paginate="false"]',
				absint( $lzd_r['columns'] ),
				esc_attr( $lzd_r['categories'] )
			) );
			if ( false !== strpos( $lzd_row, '<li' ) ) {
				$lzd_grid .= '<div class="lzd-shop-row">' . $lzd_row . '</div>';
			}
		}
		if ( '' === $lzd_grid ) {
			unset( $lzd_shop_sections[ $lzd_i ]['groups'][ $lzd_j ] );
			continue;
		}
		$lzd_shop_sections[ $lzd_i ]['groups'][ $lzd_j ]['grid'] = $lzd_grid;
	}
	if ( empty( $lzd_shop_sections[ $lzd_i ]['groups'] ) ) {
		unset( $lzd_shop_sections[ $lzd_i ] );
	}
}

get_header();
?>

<div id="lzd-home" class="lzd-shop">

	<header class="lzd-shop-head">
		<div class="col-full">
			<h1><?php woocommerce_page_title(); ?></h1>
			<nav class="lzd-shop-jump" aria-label="Jump to a category">
				<?php foreach ( $lzd_shop_sections as $lzd_s ) : ?>
					<a href="#<?php echo esc_attr( $lzd_s['id'] ); ?>"><?php echo wp_kses_post( $lzd_s['title'] ); ?></a>
				<?php endforeach; ?>
			</nav>
		</div>
	</header>

	<?php $lzd_alt = false; ?>
	<?php foreach ( $lzd_shop_sections as $lzd_s ) : ?>
		<section class="lzd-family<?php echo $lzd_alt ? ' lzd-family-alt' : ''; ?>" id="<?php echo esc_attr( $lzd_s['id'] ); ?>">
			<div class="col-full">
				<header class="lzd-family-head">
					<?php if ( $lzd_s['kicker'] ) : ?>
						<span class="lzd-kicker"><?php echo esc_html( $lzd_s['kicker'] ); ?></span>
					<?php endif; ?>
					<h2><?php echo wp_kses_post( $lzd_s['title'] ); ?></h2>
				</header>
				<?php foreach ( $lzd_s['groups'] as $lzd_g ) : ?>
					<div class="lzd-group<?php echo ! empty( $lzd_g['narrow'] ) ? ' lzd-group-narrow' : ''; ?>">
						<?php if ( ! empty( $lzd_g['title'] ) ) : ?>
							<div class="lzd-group-title"><h3><?php echo wp_kses_post( $lzd_g['title'] ); ?></h3></div>
						<?php else : ?>
							<div class="lzd-group-rule" aria-hidden="true"></div>
						<?php endif; ?>
						<?php echo $lzd_g['grid']; // phpcs:ignore WordPress.Security.EscapeOutput -- WooCommerce shortcode output. ?>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
		<?php $lzd_alt = ! $lzd_alt; ?>
	<?php endforeach; ?>

</div>

<?php
get_footer();
