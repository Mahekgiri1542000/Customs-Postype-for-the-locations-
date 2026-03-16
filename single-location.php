<?php
/**
 * Single Location Template (V2 + V3 Final)
 *
 * Dynamic Content mapping reference for Cornerstone:
 * - H1: {{dc:acf:post_field field="location_headline"}}
 * - Intro prose: {{dc:acf:post_field field="city_intro_prose"}}
 * - Map embed: {{dc:acf:post_field field="location_map_embed"}}
 * - Service title in relationship loop: {{dc:post:title}}
 * - Review subfields: {{dc:looper:field key="reviewer_name"}}, {{dc:looper:field key="review_text"}}
 * - FAQ subfields: {{dc:looper:field key="faq_question"}}, {{dc:looper:field key="faq_answer"}}
 */
    
get_header();

while ( have_posts() ) :
	the_post();

	$post_id = get_the_ID();

	// V3-first fields with V2 fallbacks where needed.
	$location_headline = trim( (string) get_field( 'location_headline', $post_id ) );
	$location_headline = $location_headline ?: trim( (string) get_field( 'loc_h1', $post_id ) );

	$hero_intro = get_field( 'hero_intro', $post_id );
	if ( empty( $hero_intro ) ) {
		$hero_intro = get_field( 'loc_intro', $post_id );
	}

	$hero_cta = get_field( 'hero_cta', $post_id );
	$cta_url  = is_array( $hero_cta ) ? (string) ( $hero_cta['url'] ?? '' ) : '';
	$cta_text = is_array( $hero_cta ) ? (string) ( $hero_cta['title'] ?? '' ) : '';
	$cta_text = $cta_text ?: 'Request a Free Estimate';
	if ( ! $cta_url ) {
		$cta_url = (string) get_field( 'loc_cta_1_url', $post_id );
	}

	$city_name = trim( (string) get_field( 'address_city', $post_id ) );
	$city_name = $city_name ?: trim( (string) get_field( 'loc_city_name', $post_id ) );
	$county    = trim( (string) get_field( 'loc_county_name', $post_id ) );

	$city_intro_prose = get_field( 'city_intro_prose', $post_id );
	if ( empty( $city_intro_prose ) ) {
		$city_intro_prose = get_field( 'loc_area_summary', $post_id );
	}

	$featured_services = get_field( 'featured_services', $post_id );
	if ( empty( $featured_services ) ) {
		$featured_services = get_field( 'loc_svcs', $post_id );
	}

	$local_reviews = get_field( 'local_reviews', $post_id );
	if ( empty( $local_reviews ) ) {
		$local_reviews = get_field( 'loc_reviews', $post_id );
	}

	$location_map_embed = (string) get_field( 'location_map_embed', $post_id );
	$has_map_embed      = ! empty( trim( $location_map_embed ) );

	$biz_name  = trim( (string) get_field( 'loc_biz_name', $post_id ) );
	$biz_phone = trim( (string) get_field( 'loc_biz_phone', $post_id ) );
	$street    = trim( (string) get_field( 'loc_addr_1', $post_id ) );
	$zip       = trim( (string) get_field( 'postal_code', $post_id ) );
	$zip       = $zip ?: trim( (string) get_field( 'loc_addr_zip', $post_id ) );

	$local_faqs = get_field( 'local_faqs', $post_id );
	if ( empty( $local_faqs ) ) {
		$local_faqs = get_field( 'loc_faqs', $post_id );
	}

	$highway_directions = trim( (string) get_field( 'highway_directions', $post_id ) );
	if ( ! $highway_directions ) {
		$highway_directions = trim( (string) get_field( 'loc_highways', $post_id ) );
	}

	$local_landmarks = trim( (string) get_field( 'local_landmarks', $post_id ) );
	$loc_landmarks_local = get_field( 'loc_landmarks_local', $post_id );
	if ( ! $local_landmarks && is_array( $loc_landmarks_local ) ) {
		$rows = array();
		foreach ( $loc_landmarks_local as $row ) {
			if ( ! empty( $row['name'] ) ) {
				$rows[] = $row['name'];
			}
		}
		$local_landmarks = implode( "\n", $rows );
	}

	// Sort reviews: featured first, then newest date.
	$sorted_reviews = array();
	if ( is_array( $local_reviews ) && ! empty( $local_reviews ) ) {
		$featured = array();
		$regular  = array();
		foreach ( $local_reviews as $review ) {
			if ( ! empty( $review['featured'] ) ) {
				$featured[] = $review;
			} else {
				$regular[] = $review;
			}
		}
		usort(
			$regular,
			static function ( $a, $b ) {
				return strcmp( (string) ( $b['date'] ?? '' ), (string) ( $a['date'] ?? '' ) );
			}
		);
		$sorted_reviews = array_merge( $featured, $regular );
	}

	// Sort FAQs: featured first.
	$sorted_faqs = array();
	if ( is_array( $local_faqs ) && ! empty( $local_faqs ) ) {
		$featured = array();
		$regular  = array();
		foreach ( $local_faqs as $faq ) {
			if ( ! empty( $faq['featured'] ) ) {
				$featured[] = $faq;
			} else {
				$regular[] = $faq;
			}
		}
		$sorted_faqs = array_merge( $featured, $regular );
	}
	?>

	<main id="main" class="location-page">

		<?php // SECTION 1 — HERO. ?>
		<?php if ( $location_headline ) : ?>
			<section class="loc-section loc-hero">
				<div class="container">
					<h1><?php echo esc_html( $location_headline ); ?></h1>
					<p class="loc-brand-signal">The Floor 4 U</p>
					<?php if ( $hero_intro ) : ?>
						<div class="loc-hero-intro"><?php echo wp_kses_post( $hero_intro ); ?></div>
					<?php endif; ?>
					<?php if ( $cta_url ) : ?>
						<p><a class="x-btn" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( $cta_text ); ?></a></p>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php // SECTION 2 — LOCAL INTRODUCTION. ?>
		<?php if ( $city_intro_prose ) : ?>
			<section class="loc-section loc-intro">
				<div class="container">
					<h2><?php echo esc_html( sprintf( 'Expert Flooring Installation Services in %s', $city_name ?: 'Your Area' ) ); ?></h2>
					<div class="loc-intro-prose"><?php echo wp_kses_post( $city_intro_prose ); ?></div>
					<?php if ( is_array( $featured_services ) && ! empty( $featured_services ) ) : ?>
						<ul class="loc-inline-links">
							<?php foreach ( array_slice( $featured_services, 0, 3 ) as $service_item ) :
								$service_post = $service_item instanceof WP_Post ? $service_item : get_post( (int) $service_item );
								if ( ! $service_post ) {
									continue;
								}
								?>
								<li><a href="<?php echo esc_url( get_permalink( $service_post ) ); ?>"><?php echo esc_html( get_the_title( $service_post ) ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php // SECTION 3 — CORE SERVICES GRID (relationship, not hardcoded). ?>
		<?php if ( is_array( $featured_services ) && ! empty( $featured_services ) ) : ?>
			<section class="loc-section loc-services-grid">
				<div class="container">
					<h2><?php echo esc_html( sprintf( 'Luxury Vinyl Plank and Hardwood for %s Homes', $city_name ?: 'Local' ) ); ?></h2>
					<div class="loc-service-grid">
						<?php foreach ( $featured_services as $service_item ) :
							$service_post = $service_item instanceof WP_Post ? $service_item : get_post( (int) $service_item );
							if ( ! $service_post ) {
								continue;
							}
							$thumb = get_the_post_thumbnail_url( $service_post, 'medium' );
							?>
							<article class="loc-service-card">
								<?php if ( $thumb ) : ?>
									<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( get_the_title( $service_post ) ); ?>" loading="lazy" width="400" height="300">
								<?php endif; ?>
								<h3><a href="<?php echo esc_url( get_permalink( $service_post ) ); ?>"><?php echo esc_html( get_the_title( $service_post ) ); ?></a></h3>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php // SECTION 4 — LOCALIZED SOCIAL PROOF. ?>
		<?php if ( ! empty( $sorted_reviews ) ) : ?>
			<section class="loc-section loc-reviews">
				<div class="container">
					<h2><?php echo esc_html( sprintf( 'Customer Reviews from %s and Will County', $city_name ?: 'Local Customers' ) ); ?></h2>
					<div class="loc-review-grid">
						<?php foreach ( $sorted_reviews as $review ) :
							$reviewer_name = ! empty( $review['reviewer_name'] ) ? $review['reviewer_name'] : ( $review['name'] ?? '' );
							$review_text   = ! empty( $review['review_text'] ) ? $review['review_text'] : ( $review['body'] ?? '' );
							$review_rating = ! empty( $review['review_rating'] ) ? (int) $review['review_rating'] : (int) ( $review['rating'] ?? 0 );
							if ( ! $reviewer_name || ! $review_text ) {
								continue;
							}
							?>
							<article class="loc-review-card">
								<h3><?php echo esc_html( $reviewer_name ); ?></h3>
								<?php if ( $review_rating > 0 ) : ?>
									<p class="loc-stars" aria-label="<?php echo esc_attr( $review_rating . ' out of 5 stars' ); ?>"><?php echo wp_kses_post( str_repeat( '&#9733;', min( 5, $review_rating ) ) ); ?></p>
								<?php endif; ?>
								<p><?php echo esc_html( $review_text ); ?></p>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php // SECTION 5 — TRUST & AUTHORITY (NAP + map). ?>
		<?php if ( $has_map_embed ) : ?>
			<section class="loc-section loc-trust-authority">
				<div class="container">
					<h2>Why Choose The Floor 4 U as Your Local Flooring Contractor</h2>
					<?php if ( $biz_name || $biz_phone || $street || $city_name ) : ?>
						<address class="loc-nap">
							<?php if ( $biz_name ) : ?><strong><?php echo esc_html( $biz_name ); ?></strong><br><?php endif; ?>
							<?php if ( $street ) : ?><?php echo esc_html( $street ); ?><br><?php endif; ?>
							<?php if ( $city_name ) : ?><?php echo esc_html( $city_name ); ?><?php endif; ?><?php if ( $zip ) : ?>, <?php echo esc_html( $zip ); ?><?php endif; ?><br>
							<?php if ( $biz_phone ) : ?><?php echo esc_html( $biz_phone ); ?><?php endif; ?>
						</address>
					<?php endif; ?>

					<div class="loc-map-block">
						<?php
						$static_src = get_the_post_thumbnail_url( $post_id, 'large' );
						if ( ! $static_src ) {
							$static_src = get_stylesheet_directory_uri() . '/screenshot.png';
						}
						?>
						<img id="loc-static-map" src="<?php echo esc_url( $static_src ); ?>" alt="<?php echo esc_attr( sprintf( 'Static map fallback for %s', $city_name ?: 'service area' ) ); ?>" loading="lazy" width="1200" height="630">
						<?php if ( $has_map_embed ) : ?>
							<div id="loc-map-embed" hidden></div>
							<button id="loc-enable-map" type="button" class="x-btn">Show Interactive Map</button>
							<script>
							( function() {
								var btn = document.getElementById( 'loc-enable-map' );
								if ( ! btn ) return;
								btn.addEventListener( 'click', function() {
									var wrap = document.getElementById( 'loc-map-embed' );
									var staticMap = document.getElementById( 'loc-static-map' );
									if ( ! wrap || ! staticMap ) return;
									if ( wrap.innerHTML === '' ) {
										wrap.innerHTML = <?php echo wp_json_encode( wp_kses_post( $location_map_embed ) ); ?>;
									}
									wrap.hidden = false;
									staticMap.style.display = 'none';
									btn.style.display = 'none';
								} );
							} )();
							</script>
						<?php endif; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php // SECTION 6 — FAQ ACCORDION. ?>
		<?php if ( ! empty( $sorted_faqs ) ) : ?>
			<section class="loc-section loc-faq">
				<div class="container">
					<h2><?php echo esc_html( sprintf( 'Frequently Asked Questions About New Floors in %s', $city_name ?: 'Your Area' ) ); ?></h2>
					<div class="loc-faq-accordion">
						<?php foreach ( $sorted_faqs as $faq ) :
							$question = ! empty( $faq['faq_question'] ) ? $faq['faq_question'] : ( $faq['q'] ?? '' );
							$answer   = ! empty( $faq['faq_answer'] ) ? $faq['faq_answer'] : ( $faq['a'] ?? '' );
							if ( ! $question || ! $answer ) {
								continue;
							}
							?>
							<details class="loc-faq-item">
								<summary><?php echo esc_html( $question ); ?></summary>
								<div><?php echo wp_kses_post( $answer ); ?></div>
							</details>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php // BOTTOM OF PAGE — proximity signals only at bottom. ?>
		<?php if ( $highway_directions ) : ?>
			<section class="loc-section loc-directions-bottom">
				<div class="container">
					<h2><?php echo esc_html( sprintf( 'Flooring Service Areas in %s and Will County', $city_name ?: 'Your Area' ) ); ?></h2>
					<p><?php echo nl2br( esc_html( $highway_directions ) ); ?></p>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $local_landmarks ) : ?>
			<section class="loc-section loc-landmarks-bottom">
				<div class="container">
					<h2><?php echo esc_html( sprintf( 'Communities We Serve in %s', $city_name ?: 'Your Area' ) ); ?></h2>
					<p><?php echo nl2br( esc_html( $local_landmarks ) ); ?></p>
				</div>
			</section>
		<?php endif; ?>

	</main>

<?php endwhile; ?>

<?php get_footer(); ?>
