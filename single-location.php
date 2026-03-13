<?php
/**
 * Single Location Template — Floor 4U Final Merged Spec
 * Uses all new field_loc_* keys from group_loc_v1.
 *
 * @package Floor4U
 */

get_header();

while ( have_posts() ) :
	the_post();
	$post_id = get_the_ID();

	// ── Identity ─────────────────────────────────────────────────────────────
	$loc_h1          = (string) get_field( 'loc_h1', $post_id );
	$loc_intro       = get_field( 'loc_intro', $post_id );
	$loc_status      = (string) get_field( 'loc_status', $post_id );
	$loc_cta_1_text  = (string) get_field( 'loc_cta_1_text', $post_id ) ?: 'Request an Estimate';
	$loc_cta_1_url   = (string) get_field( 'loc_cta_1_url', $post_id );
	$loc_cta_2_text  = (string) get_field( 'loc_cta_2_text', $post_id );
	$loc_cta_2_url   = (string) get_field( 'loc_cta_2_url', $post_id );

	// ── Geography ────────────────────────────────────────────────────────────
	$loc_city_name       = (string) get_field( 'loc_city_name', $post_id );
	$loc_county_name     = (string) get_field( 'loc_county_name', $post_id );
	$loc_state_name      = (string) get_field( 'loc_state_name', $post_id ) ?: 'Illinois';
	$loc_area_summary    = get_field( 'loc_area_summary', $post_id );
	$loc_landmarks_local  = get_field( 'loc_landmarks_local', $post_id );
	$loc_landmarks_nearby = get_field( 'loc_landmarks_nearby', $post_id );
	$loc_highways        = (string) get_field( 'loc_highways', $post_id );
	$loc_communities_field = get_field( 'loc_communities', $post_id );

	// ── Business ─────────────────────────────────────────────────────────────
	$loc_mode         = (string) get_field( 'loc_mode', $post_id );
	$loc_lat          = get_field( 'loc_lat', $post_id );
	$loc_lng          = get_field( 'loc_lng', $post_id );
	$loc_addr_1       = (string) get_field( 'loc_addr_1', $post_id );
	$loc_addr_city    = (string) get_field( 'loc_addr_city', $post_id );
	$loc_addr_zip     = (string) get_field( 'loc_addr_zip', $post_id );
	$loc_biz_phone    = (string) get_field( 'loc_biz_phone', $post_id );
	$loc_biz_phone_raw = (string) get_field( 'loc_biz_phone_raw', $post_id );
	$has_address      = ( 'branch' === $loc_mode || 'hybrid' === $loc_mode ) && $loc_addr_1;
	$has_map          = ( 'branch' === $loc_mode || 'hybrid' === $loc_mode ) && $loc_lat && $loc_lng;

	// ── Services ─────────────────────────────────────────────────────────────
	$loc_svcs_intro = get_field( 'loc_svcs_intro', $post_id );
	$loc_svcs       = get_field( 'loc_svcs', $post_id );

	// ── Proof ────────────────────────────────────────────────────────────────
	$loc_prj_intro  = (string) get_field( 'loc_prj_intro', $post_id );
	$loc_prjs       = get_field( 'loc_prjs', $post_id );
	$loc_reviews_raw = get_field( 'loc_reviews', $post_id );
	$loc_gallery_ba  = get_field( 'loc_gallery_ba', $post_id );
	$loc_trust       = get_field( 'loc_trust', $post_id );
	$loc_faq_intro   = (string) get_field( 'loc_faq_intro', $post_id );
	$loc_faqs_raw    = get_field( 'loc_faqs', $post_id );

	// Sort reviews: featured first, then newest date.
	$loc_reviews = array();
	if ( is_array( $loc_reviews_raw ) && ! empty( $loc_reviews_raw ) ) {
		$featured_reviews = array_filter( $loc_reviews_raw, static function( $r ) { return ! empty( $r['featured'] ); } );
		$other_reviews    = array_filter( $loc_reviews_raw, static function( $r ) { return empty( $r['featured'] ); } );
		usort( $other_reviews, static function( $a, $b ) { return strcmp( (string) ( $b['date'] ?? '' ), (string) ( $a['date'] ?? '' ) ); } );
		$loc_reviews = array_values( array_merge( $featured_reviews, $other_reviews ) );
	}

	// Sort FAQs: featured first.
	$loc_faqs = array();
	if ( is_array( $loc_faqs_raw ) && ! empty( $loc_faqs_raw ) ) {
		$featured_faqs = array_filter( $loc_faqs_raw, static function( $f ) { return ! empty( $f['featured'] ); } );
		$other_faqs    = array_filter( $loc_faqs_raw, static function( $f ) { return empty( $f['featured'] ); } );
		$loc_faqs      = array_values( array_merge( $featured_faqs, $other_faqs ) );
	}
	?>

<main id="main" class="location-page" data-loc-mode="<?php echo esc_attr( $loc_mode ); ?>">

	<?php // ================================================================
	// HERO SECTION
	// ================================================================ ?>
	<section class="location-hero">
		<div class="container">

			<?php if ( 'coming_soon' === $loc_status ) : ?>
				<div class="location-status-banner location-status-coming-soon">
					<?php esc_html_e( 'Coming Soon', 'pro-child' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $loc_h1 ) : ?>
				<h1 class="location-h1"><?php echo esc_html( $loc_h1 ); ?></h1>
			<?php endif; ?>

			<?php if ( $loc_intro ) : ?>
				<div class="location-intro"><?php echo wp_kses_post( $loc_intro ); ?></div>
			<?php endif; ?>

			<?php if ( $loc_cta_1_url || $loc_cta_2_url ) : ?>
				<div class="location-ctas">
					<?php if ( $loc_cta_1_url ) : ?>
						<a class="btn btn-primary" href="<?php echo esc_url( $loc_cta_1_url ); ?>">
							<?php echo esc_html( $loc_cta_1_text ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $loc_cta_2_url && $loc_cta_2_text ) : ?>
						<a class="btn btn-secondary" href="<?php echo esc_url( $loc_cta_2_url ); ?>">
							<?php echo esc_html( $loc_cta_2_text ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $loc_biz_phone ) : ?>
				<p class="location-phone">
					<a href="tel:<?php echo esc_attr( $loc_biz_phone_raw ?: $loc_biz_phone ); ?>">
						<?php echo esc_html( $loc_biz_phone ); ?>
					</a>
				</p>
			<?php endif; ?>

		</div>
	</section>

	<?php // ================================================================
	// TRUST BULLETS
	// ================================================================ ?>
	<?php if ( $loc_trust && is_array( $loc_trust ) ) : ?>
	<section class="location-trust">
		<div class="container">
			<ul class="location-trust__list">
				<?php foreach ( $loc_trust as $tb ) : ?>
					<?php if ( ! empty( $tb['text'] ) ) : ?>
						<li class="location-trust__item"><?php echo esc_html( $tb['text'] ); ?></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php endif; ?>

	<?php // ================================================================
	// SERVICES SECTION
	// ================================================================ ?>
	<?php if ( $loc_svcs && is_array( $loc_svcs ) ) : ?>
	<section class="location-services">
		<div class="container">
			<h2 class="location-services__heading">
				<?php
				printf(
					/* translators: %s: city name */
					esc_html__( 'Flooring Services in %s', 'pro-child' ),
					esc_html( $loc_city_name )
				);
				?>
			</h2>

			<?php if ( $loc_svcs_intro ) : ?>
				<div class="location-services__intro"><?php echo wp_kses_post( $loc_svcs_intro ); ?></div>
			<?php endif; ?>

			<div class="location-services__grid">
				<?php foreach ( $loc_svcs as $svc ) :
					$svc = $svc instanceof WP_Post ? $svc : get_post( (int) $svc );
					if ( ! $svc ) continue;
					$svc_thumb = get_the_post_thumbnail_url( $svc, 'medium' );
					$svc_summary = get_field( 'svc_summary', $svc->ID );
					?>
					<div class="location-services__card">
						<?php if ( $svc_thumb ) : ?>
							<div class="location-services__card-img">
								<img src="<?php echo esc_url( $svc_thumb ); ?>"
									alt="<?php echo esc_attr( get_the_title( $svc ) ); ?>"
									loading="lazy" width="400" height="300">
							</div>
						<?php endif; ?>
						<div class="location-services__card-body">
							<h3 class="location-services__card-title">
								<a href="<?php echo esc_url( get_permalink( $svc ) ); ?>">
									<?php echo esc_html( get_the_title( $svc ) ); ?>
								</a>
							</h3>
							<?php if ( $svc_summary ) : ?>
								<p><?php echo esc_html( $svc_summary ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php // ================================================================
	// PROJECTS SECTION
	// ================================================================ ?>
	<?php if ( $loc_prjs && is_array( $loc_prjs ) ) : ?>
	<section class="location-projects">
		<div class="container">
			<h2 class="location-projects__heading">
				<?php
				printf(
					/* translators: %s: city name */
					esc_html__( 'Recent Flooring Projects Near %s', 'pro-child' ),
					esc_html( $loc_city_name )
				);
				?>
			</h2>

			<?php if ( $loc_prj_intro ) : ?>
				<p class="location-projects__intro"><?php echo esc_html( $loc_prj_intro ); ?></p>
			<?php endif; ?>

			<div class="location-projects__grid">
				<?php foreach ( $loc_prjs as $prj ) :
					$prj = $prj instanceof WP_Post ? $prj : get_post( (int) $prj );
					if ( ! $prj ) continue;
					$prj_thumb   = get_the_post_thumbnail_url( $prj, 'medium' );
					$prj_summary = get_field( 'prj_summary', $prj->ID );
					$prj_city    = get_field( 'prj_city', $prj->ID );
					?>
					<div class="location-projects__card">
						<?php if ( $prj_thumb ) : ?>
							<div class="location-projects__card-img">
								<img src="<?php echo esc_url( $prj_thumb ); ?>"
									alt="<?php echo esc_attr( get_the_title( $prj ) ); ?>"
									loading="lazy" width="400" height="300">
							</div>
						<?php endif; ?>
						<div class="location-projects__card-body">
							<h3 class="location-projects__card-title">
								<a href="<?php echo esc_url( get_permalink( $prj ) ); ?>">
									<?php echo esc_html( get_the_title( $prj ) ); ?>
								</a>
							</h3>
							<?php if ( $prj_city ) : ?>
								<p class="location-projects__card-meta"><?php echo esc_html( $prj_city ); ?></p>
							<?php endif; ?>
							<?php if ( $prj_summary ) : ?>
								<p><?php echo esc_html( $prj_summary ); ?></p>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php // ================================================================
	// REVIEWS SECTION
	// ================================================================ ?>
	<?php if ( ! empty( $loc_reviews ) ) : ?>
	<section class="location-reviews">
		<div class="container">
			<h2 class="location-reviews__heading">
				<?php
				printf(
					/* translators: %s: city name */
					esc_html__( 'What %s Homeowners Are Saying', 'pro-child' ),
					esc_html( $loc_city_name )
				);
				?>
			</h2>
			<div class="location-reviews__grid">
				<?php foreach ( $loc_reviews as $rev ) : ?>
					<div class="location-reviews__card<?php echo ! empty( $rev['featured'] ) ? ' location-reviews__card--featured' : ''; ?>">
						<?php if ( ! empty( $rev['rating'] ) ) : ?>
							<div class="location-reviews__stars" aria-label="<?php echo esc_attr( $rev['rating'] ) . ' ' . esc_attr__( 'out of 5 stars', 'pro-child' ); ?>">
								<?php echo str_repeat( '&#9733;', (int) $rev['rating'] ); ?>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $rev['body'] ) ) : ?>
							<blockquote class="location-reviews__body">
								<p><?php echo esc_html( $rev['body'] ); ?></p>
							</blockquote>
						<?php endif; ?>
						<footer class="location-reviews__footer">
							<?php if ( ! empty( $rev['name'] ) ) : ?>
								<cite class="location-reviews__name"><?php echo esc_html( $rev['name'] ); ?></cite>
							<?php endif; ?>
							<?php if ( ! empty( $rev['city'] ) ) : ?>
								<span class="location-reviews__city"> &mdash; <?php echo esc_html( $rev['city'] ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $rev['date'] ) ) : ?>
								<time class="location-reviews__date" datetime="<?php echo esc_attr( substr( (string) $rev['date'], 0, 4 ) . '-' . substr( (string) $rev['date'], 4, 2 ) . '-' . substr( (string) $rev['date'], 6, 2 ) ); ?>">
									<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( (string) $rev['date'] ) ) ); ?>
								</time>
							<?php endif; ?>
						</footer>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php // ================================================================
	// BEFORE / AFTER GALLERY
	// ================================================================ ?>
	<?php if ( $loc_gallery_ba && is_array( $loc_gallery_ba ) ) : ?>
	<section class="location-gallery">
		<div class="container">
			<h2 class="location-gallery__heading">
				<?php esc_html_e( 'Before &amp; After Gallery', 'pro-child' ); ?>
			</h2>
			<div class="location-gallery__grid">
				<?php foreach ( $loc_gallery_ba as $img ) :
					if ( empty( $img['url'] ) ) continue;
					?>
					<figure class="location-gallery__item">
						<img src="<?php echo esc_url( $img['sizes']['medium'] ?? $img['url'] ); ?>"
							alt="<?php echo esc_attr( $img['alt'] ?? '' ); ?>"
							loading="lazy"
							width="<?php echo esc_attr( $img['sizes']['medium-width'] ?? $img['width'] ?? '' ); ?>"
							height="<?php echo esc_attr( $img['sizes']['medium-height'] ?? $img['height'] ?? '' ); ?>">
					</figure>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php // ================================================================
	// FAQs SECTION (no FAQPage schema — page-support only)
	// ================================================================ ?>
	<?php if ( ! empty( $loc_faqs ) ) : ?>
	<section class="location-faqs">
		<div class="container">
			<h2 class="location-faqs__heading">
				<?php
				printf(
					/* translators: %s: city name */
					esc_html__( 'Flooring FAQ for %s Homeowners', 'pro-child' ),
					esc_html( $loc_city_name )
				);
				?>
			</h2>
			<?php if ( $loc_faq_intro ) : ?>
				<p class="location-faqs__intro"><?php echo esc_html( $loc_faq_intro ); ?></p>
			<?php endif; ?>
			<dl class="location-faqs__list">
				<?php foreach ( $loc_faqs as $faq ) :
					if ( empty( $faq['q'] ) ) continue;
					?>
					<div class="location-faqs__item">
						<dt class="location-faqs__question"><?php echo esc_html( $faq['q'] ); ?></dt>
						<dd class="location-faqs__answer"><?php echo wp_kses_post( $faq['a'] ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</div>
	</section>
	<?php endif; ?>

	<?php // ================================================================
	// AREA SUMMARY
	// ================================================================ ?>
	<?php if ( $loc_area_summary ) : ?>
	<section class="location-area-summary">
		<div class="container">
			<h2 class="location-area-summary__heading">
				<?php
				printf(
					/* translators: %s: city name */
					esc_html__( 'Serving %s and Surrounding Communities', 'pro-child' ),
					esc_html( $loc_city_name )
				);
				?>
			</h2>
			<div class="location-area-summary__body"><?php echo wp_kses_post( $loc_area_summary ); ?></div>
		</div>
	</section>
	<?php endif; ?>

	<?php // ================================================================
	// COMMUNITIES SERVED
	// ================================================================ ?>
	<?php
	// Resolve communities: ACF taxonomy field returns term objects or term IDs.
	$communities_list = array();
	if ( ! empty( $loc_communities_field ) ) {
		foreach ( (array) $loc_communities_field as $com_item ) {
			if ( $com_item instanceof WP_Term ) {
				$communities_list[] = $com_item;
			} elseif ( is_int( $com_item ) || ( is_string( $com_item ) && ctype_digit( $com_item ) ) ) {
				$t = get_term( (int) $com_item, 'community' );
				if ( $t && ! is_wp_error( $t ) ) {
					$communities_list[] = $t;
				}
			}
		}
	}
	?>
	<?php if ( ! empty( $communities_list ) ) : ?>
	<section class="location-communities">
		<div class="container">
			<h2 class="location-communities__heading">
				<?php esc_html_e( 'Neighborhoods &amp; Communities Served', 'pro-child' ); ?>
			</h2>
			<ul class="location-communities__list">
				<?php foreach ( $communities_list as $ct ) : ?>
					<li class="location-communities__item">
						<a href="<?php echo esc_url( get_term_link( $ct ) ); ?>">
							<?php echo esc_html( $ct->name ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php endif; ?>

	<?php // ================================================================
	// STATIC MAP (branch / hybrid with coordinates only)
	// ================================================================ ?>
	<?php if ( $has_map ) : ?>
	<section class="location-map" id="location-map-section">
		<div class="container">
			<div class="location-map__wrapper">
				<?php
				$map_img_url = 'https://maps.googleapis.com/maps/api/staticmap'
					. '?center=' . (float) $loc_lat . ',' . (float) $loc_lng
					. '&zoom=14&size=800x400&maptype=roadmap'
					. '&markers=color:red%7C' . (float) $loc_lat . ',' . (float) $loc_lng;
				?>
				<img
					id="location-map-static"
					class="location-map__static"
					src="<?php echo esc_url( $map_img_url ); ?>"
					alt="<?php echo esc_attr( sprintf( __( 'Map showing location in %s', 'pro-child' ), $loc_addr_city ?: $loc_city_name ) ); ?>"
					loading="lazy"
					width="800"
					height="400">
				<div id="location-map-interactive" class="location-map__interactive" style="display:none;" aria-live="polite">
					<iframe
						src="https://www.google.com/maps?q=<?php echo rawurlencode( trim( $loc_addr_1 . ' ' . ( $loc_addr_city ?: $loc_city_name ) . ' IL' ) ); ?>&output=embed"
						width="800" height="400" style="border:0;" allowfullscreen="" loading="lazy"
						referrerpolicy="no-referrer-when-downgrade"
						title="<?php esc_attr_e( 'Interactive Map', 'pro-child' ); ?>">
					</iframe>
				</div>
				<button type="button" class="location-map__btn btn btn-sm" id="location-map-toggle">
					<?php esc_html_e( 'Show Interactive Map', 'pro-child' ); ?>
				</button>
				<?php if ( $has_address ) : ?>
				<address class="location-map__address">
					<?php echo esc_html( $loc_addr_1 ); ?><br>
					<?php echo esc_html( ( $loc_addr_city ?: $loc_city_name ) . ', IL ' . $loc_addr_zip ); ?>
				</address>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<script>
	( function() {
		var btn = document.getElementById( 'location-map-toggle' );
		if ( ! btn ) return;
		btn.addEventListener( 'click', function() {
			var st = document.getElementById( 'location-map-static' );
			var iv = document.getElementById( 'location-map-interactive' );
			if ( iv.style.display === 'none' ) {
				iv.style.display = 'block';
				st.style.display = 'none';
				btn.textContent = '<?php echo esc_js( __( 'Show Static Map', 'pro-child' ) ); ?>';
			} else {
				iv.style.display = 'none';
				st.style.display = 'block';
				btn.textContent = '<?php echo esc_js( __( 'Show Interactive Map', 'pro-child' ) ); ?>';
			}
		} );
	} )();
	</script>
	<?php endif; ?>

	<?php // ================================================================
	// MAIN CONTENT (WP editor / blocks)
	// ================================================================ ?>
	<?php if ( get_the_content() ) : ?>
	<section class="location-content">
		<div class="container">
			<?php the_content(); ?>
		</div>
	</section>
	<?php endif; ?>

	<?php // ================================================================
	// LOCAL LANDMARKS
	// ================================================================ ?>
	<?php if ( ( $loc_landmarks_local && is_array( $loc_landmarks_local ) ) || ( $loc_landmarks_nearby && is_array( $loc_landmarks_nearby ) ) ) : ?>
	<section class="location-landmarks">
		<div class="container">
			<?php if ( $loc_landmarks_local && is_array( $loc_landmarks_local ) ) : ?>
				<h2 class="location-landmarks__heading">
					<?php
					printf(
						/* translators: %s: city name */
						esc_html__( 'Local Landmarks Near Your Flooring Project in %s', 'pro-child' ),
						esc_html( $loc_city_name )
					);
					?>
				</h2>
				<ul class="location-landmarks__list">
					<?php foreach ( $loc_landmarks_local as $lm ) :
						if ( empty( $lm['name'] ) ) continue;
						?>
						<li class="location-landmarks__item">
							<?php echo esc_html( $lm['name'] ); ?>
							<?php if ( ! empty( $lm['type'] ) ) : ?>
								<span class="location-landmarks__type">(<?php echo esc_html( $lm['type'] ); ?>)</span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( $loc_landmarks_nearby && is_array( $loc_landmarks_nearby ) ) : ?>
				<h3 class="location-landmarks__heading location-landmarks__heading--nearby">
					<?php esc_html_e( 'Nearby Areas', 'pro-child' ); ?>
				</h3>
				<ul class="location-landmarks__list">
					<?php foreach ( $loc_landmarks_nearby as $lm ) :
						if ( empty( $lm['name'] ) ) continue;
						?>
						<li class="location-landmarks__item">
							<?php echo esc_html( $lm['name'] ); ?>
							<?php if ( ! empty( $lm['type'] ) ) : ?>
								<span class="location-landmarks__type">(<?php echo esc_html( $lm['type'] ); ?>)</span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php // ================================================================
	// HIGHWAY DIRECTIONS
	// ================================================================ ?>
	<?php if ( $loc_highways ) : ?>
	<section class="location-directions">
		<div class="container">
			<h2 class="location-directions__heading">
				<?php
				printf(
					/* translators: %s: city name */
					esc_html__( 'Getting to %s — Flooring Service Area', 'pro-child' ),
					esc_html( $loc_city_name )
				);
				?>
			</h2>
			<p class="location-directions__body"><?php echo nl2br( esc_html( $loc_highways ) ); ?></p>
		</div>
	</section>
	<?php endif; ?>

</main>

<?php endwhile; ?>

<?php get_footer(); ?>
