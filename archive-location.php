<?php
/**
 * Archive template: Location CPT
 * Reuses the same card/grid design language as project archive.
 */

get_header(); ?>

<main id="loc-archive" class="prj-archive-wrap">

	<header class="prj-archive__header">
		<div class="prj-archive__header-inner">
			<p class="prj-archive__eyebrow">Our Locations</p>
			<h1 class="prj-archive__title">Flooring Locations</h1>
			<p class="prj-archive__subtitle">Find your nearest service area, branch, or showroom location.</p>
		</div>
	</header>

	<div class="prj-archive__container">

		<?php if ( have_posts() ) : ?>

			<div class="prj-grid">
			<?php while ( have_posts() ) : the_post();
				$post_id = get_the_ID();

				$loc_title = trim( (string) get_field( 'location_headline', $post_id ) );
				if ( ! $loc_title ) {
					$loc_title = trim( (string) get_field( 'loc_h1', $post_id ) );
				}
				if ( ! $loc_title ) {
					$loc_title = get_the_title();
				}

				$loc_intro = get_field( 'hero_intro', $post_id );
				if ( empty( $loc_intro ) ) {
					$loc_intro = get_field( 'loc_intro', $post_id );
				}
				$loc_summary = $loc_intro ? wp_strip_all_tags( (string) $loc_intro ) : get_the_excerpt();

				$city = trim( (string) get_field( 'address_city', $post_id ) );
				if ( ! $city ) {
					$city = trim( (string) get_field( 'loc_city_name', $post_id ) );
				}
				$county = trim( (string) get_field( 'loc_county_name', $post_id ) );

				$mode = trim( (string) get_field( 'loc_mode', $post_id ) );
				if ( ! $mode ) {
					$mode = trim( (string) get_field( 'loc_mode_v3', $post_id ) );
				}
				$mode_label = $mode ? ucwords( str_replace( '_', ' ', $mode ) ) : '';

				$featured_services = get_field( 'featured_services', $post_id );
				if ( empty( $featured_services ) ) {
					$featured_services = get_field( 'loc_svcs', $post_id );
				}

				if ( has_post_thumbnail() ) {
					$img_url = esc_url( get_the_post_thumbnail_url( null, 'medium_large' ) );
					$img_alt = esc_attr( $loc_title );
				} else {
					$img_url = '';
					$img_alt = '';
				}

				$location_label = trim( $city . ( $city && $county ? ', ' : '' ) . $county );
				?>

				<article class="prj-card" itemscope itemtype="https://schema.org/Place">
					<a href="<?php the_permalink(); ?>" class="prj-card__img-wrap" tabindex="-1" aria-hidden="true">
						<?php if ( $img_url ) : ?>
							<img src="<?php echo $img_url; ?>"
							     alt="<?php echo $img_alt; ?>"
							     class="prj-card__img"
							     loading="lazy"
							     width="600" height="400">
						<?php else : ?>
							<div class="prj-card__img prj-card__img--placeholder">
								<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
									<rect width="48" height="48" rx="6" fill="#e9edf5"/>
									<path d="M10 34l10-12 7 8 5-6 6 10H10z" fill="#cfd7e6"/>
									<circle cx="16" cy="18" r="3" fill="#cfd7e6"/>
								</svg>
							</div>
						<?php endif; ?>
					</a>

					<div class="prj-card__body">
						<div class="prj-card__meta">
							<?php if ( $location_label ) : ?>
							<span class="prj-card__meta-loc">
								<svg width="12" height="14" viewBox="0 0 12 14" fill="currentColor" aria-hidden="true">
									<path d="M6 0C3.24 0 1 2.24 1 5c0 3.75 5 9 5 9s5-5.25 5-9c0-2.76-2.24-5-5-5zm0 6.75A1.75 1.75 0 1 1 6 3.25a1.75 1.75 0 0 1 0 3.5z"/>
								</svg>
								<?php echo esc_html( $location_label ); ?>
							</span>
							<?php endif; ?>
							<?php if ( $mode_label ) : ?>
							<span class="prj-card__meta-date"><?php echo esc_html( $mode_label ); ?></span>
							<?php endif; ?>
						</div>

						<h2 class="prj-card__title" itemprop="name">
							<a href="<?php the_permalink(); ?>"><?php echo esc_html( $loc_title ); ?></a>
						</h2>

						<?php if ( $loc_summary ) : ?>
						<p class="prj-card__summary" itemprop="description"><?php echo esc_html( wp_trim_words( $loc_summary, 20, '...' ) ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $featured_services ) && is_array( $featured_services ) ) : ?>
						<ul class="prj-card__tags" aria-label="Featured services">
							<?php foreach ( array_slice( $featured_services, 0, 3 ) as $service_item ) :
								$service_post = $service_item instanceof WP_Post ? $service_item : get_post( (int) $service_item );
								if ( ! $service_post ) {
									continue;
								}
							?>
							<li class="prj-card__tag"><?php echo esc_html( get_the_title( $service_post ) ); ?></li>
							<?php endforeach; ?>
						</ul>
						<?php endif; ?>

						<a href="<?php the_permalink(); ?>" class="prj-card__cta" aria-label="View location: <?php echo esc_attr( $loc_title ); ?>">
							View Location <span aria-hidden="true">&rarr;</span>
						</a>
					</div>
				</article>
			<?php endwhile; ?>
			</div>

			<nav class="prj-pagination" aria-label="Locations pagination">
				<?php
				echo paginate_links( array(
					'prev_text' => '&larr; Newer',
					'next_text' => 'Older &rarr;',
					'type'      => 'list',
				) );
				?>
			</nav>
		<?php else : ?>
			<p class="prj-archive__empty">No locations found.</p>
		<?php endif; ?>

	</div>
</main>

<?php get_footer(); ?>
