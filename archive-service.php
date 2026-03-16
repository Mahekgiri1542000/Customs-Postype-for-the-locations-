<?php
/**
 * Archive template: Service CPT
 * Reuses the same card/grid design language as project archive.
 */

get_header(); ?>

<main id="svc-archive" class="prj-archive-wrap">

	<header class="prj-archive__header">
		<div class="prj-archive__header-inner">
			<p class="prj-archive__eyebrow">Our Services</p>
			<h1 class="prj-archive__title">Flooring Services</h1>
			<p class="prj-archive__subtitle">Explore installation, refinishing, and specialty flooring services.</p>
		</div>
	</header>

	<div class="prj-archive__container">

		<?php if ( have_posts() ) : ?>

			<div class="prj-grid">
			<?php while ( have_posts() ) : the_post();
				$post_id = get_the_ID();

				$svc_title = trim( (string) get_field( 'svc_h1', $post_id ) );
				if ( ! $svc_title ) {
					$svc_title = trim( (string) get_field( 'svc_name', $post_id ) );
				}
				if ( ! $svc_title ) {
					$svc_title = get_the_title();
				}

				$svc_summary = trim( (string) get_field( 'svc_summary', $post_id ) );
				if ( ! $svc_summary ) {
					$svc_summary = get_the_excerpt();
				}

				$svc_type   = trim( (string) get_field( 'svc_type', $post_id ) );
				$svc_status = trim( (string) get_field( 'svc_status', $post_id ) );
				$status_map = array(
					'active'      => 'Active',
					'inactive'    => 'Inactive',
					'coming_soon' => 'Coming Soon',
				);
				$status_label = isset( $status_map[ $svc_status ] ) ? $status_map[ $svc_status ] : '';

				$svc_img = get_field( 'svc_img', $post_id );
				if ( ! empty( $svc_img['url'] ) ) {
					$img_url = esc_url( $svc_img['url'] );
					$img_alt = esc_attr( $svc_img['alt'] ?: $svc_title );
				} elseif ( has_post_thumbnail() ) {
					$img_url = esc_url( get_the_post_thumbnail_url( null, 'medium_large' ) );
					$img_alt = esc_attr( $svc_title );
				} else {
					$img_url = '';
					$img_alt = '';
				}

				$first_city = '';
				$svc_locs   = get_field( 'svc_locs', $post_id );
				if ( is_array( $svc_locs ) && ! empty( $svc_locs ) ) {
					$first_loc = $svc_locs[0] instanceof WP_Post ? $svc_locs[0] : get_post( (int) $svc_locs[0] );
					if ( $first_loc ) {
						$first_city = trim( (string) get_field( 'address_city', $first_loc->ID ) );
						if ( ! $first_city ) {
							$first_city = trim( (string) get_field( 'loc_city_name', $first_loc->ID ) );
						}
					}
				}

				$materials = get_field( 'svc_materials', $post_id );
				?>

				<article class="prj-card" itemscope itemtype="https://schema.org/Service">
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
							<?php if ( $first_city ) : ?>
							<span class="prj-card__meta-loc">
								<svg width="12" height="14" viewBox="0 0 12 14" fill="currentColor" aria-hidden="true">
									<path d="M6 0C3.24 0 1 2.24 1 5c0 3.75 5 9 5 9s5-5.25 5-9c0-2.76-2.24-5-5-5zm0 6.75A1.75 1.75 0 1 1 6 3.25a1.75 1.75 0 0 1 0 3.5z"/>
								</svg>
								<?php echo esc_html( $first_city ); ?>
							</span>
							<?php endif; ?>
							<?php if ( $status_label ) : ?>
							<span class="prj-card__meta-date"><?php echo esc_html( $status_label ); ?></span>
							<?php endif; ?>
						</div>

						<h2 class="prj-card__title" itemprop="name">
							<a href="<?php the_permalink(); ?>"><?php echo esc_html( $svc_title ); ?></a>
						</h2>

						<?php if ( $svc_summary ) : ?>
						<p class="prj-card__summary" itemprop="description"><?php echo esc_html( wp_trim_words( $svc_summary, 20, '...' ) ); ?></p>
						<?php endif; ?>

						<ul class="prj-card__tags" aria-label="Service tags">
							<?php if ( $svc_type ) : ?>
							<li class="prj-card__tag"><?php echo esc_html( $svc_type ); ?></li>
							<?php endif; ?>
							<?php
							$tag_count = $svc_type ? 1 : 0;
							if ( is_array( $materials ) ) {
								foreach ( $materials as $material ) {
									if ( $tag_count >= 3 ) {
										break;
									}
									$name = trim( (string) ( $material['name'] ?? '' ) );
									if ( ! $name ) {
										continue;
									}
									$tag_count++;
									?>
									<li class="prj-card__tag"><?php echo esc_html( $name ); ?></li>
									<?php
								}
							}
							?>
						</ul>

						<a href="<?php the_permalink(); ?>" class="prj-card__cta" aria-label="View service: <?php echo esc_attr( $svc_title ); ?>">
							View Service <span aria-hidden="true">&rarr;</span>
						</a>
					</div>
				</article>
			<?php endwhile; ?>
			</div>

			<nav class="prj-pagination" aria-label="Services pagination">
				<?php
				echo paginate_links( array(
					'prev_text' => '&larr; Newer',
					'next_text' => 'Older &rarr;',
					'type'      => 'list',
				) );
				?>
			</nav>
		<?php else : ?>
			<p class="prj-archive__empty">No services found.</p>
		<?php endif; ?>

	</div>
</main>

<?php get_footer(); ?>
