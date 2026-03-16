<?php
/**
 * Archive template: Project CPT
 * Uses CSS Grid – 3 cols desktop / 2 cols tablet / 1 col mobile.
 * ACF fields: prj_title, prj_summary, prj_city, prj_state, prj_date,
 *             prj_svcs (relationship), prj_img (image array), prj_status.
 */

get_header(); ?>

<main id="prj-archive" class="prj-archive-wrap">

	<!-- ── PAGE HEADER ───────────────────────────────────────────────── -->
	<header class="prj-archive__header">
		<div class="prj-archive__header-inner">
			<p class="prj-archive__eyebrow">Our Work</p>
			<h1 class="prj-archive__title">
				<?php
				if ( is_tax() ) {
					single_term_title();
				} else {
					echo 'Flooring Projects';
				}
				?>
			</h1>
			<p class="prj-archive__subtitle">
				Real results from real homes and businesses across the Chicago Southland.
			</p>
		</div>
	</header>
	<!-- ── END PAGE HEADER ───────────────────────────────────────────── -->

	<div class="prj-archive__container">

		<?php if ( have_posts() ) : ?>

			<!-- ── FILTER BAR (taxonomy: community) ──────────────────────── -->
			<?php
			$communities = get_terms( array(
				'taxonomy'   => 'community',
				'hide_empty' => true,
				'number'     => 12,
			) );
			if ( ! empty( $communities ) && ! is_wp_error( $communities ) ) : ?>
			<nav class="prj-filter" aria-label="Filter projects by community">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ); ?>"
				   class="prj-filter__btn<?php echo ( ! is_tax() ) ? ' is-active' : ''; ?>">
					All Projects
				</a>
				<?php foreach ( $communities as $term ) : ?>
				<a href="<?php echo esc_url( get_term_link( $term ) ); ?>"
				   class="prj-filter__btn<?php echo ( is_tax( 'community', $term->term_id ) ) ? ' is-active' : ''; ?>">
					<?php echo esc_html( $term->name ); ?>
				</a>
				<?php endforeach; ?>
			</nav>
			<?php endif; ?>
			<!-- ── END FILTER BAR ────────────────────────────────────────── -->

			<!-- ── GRID ──────────────────────────────────────────────────── -->
			<div class="prj-grid">

			<?php while ( have_posts() ) : the_post();

				// ── ACF fields ──────────────────────────────────────────────
				$prj_title   = get_field( 'prj_title' )   ?: get_the_title();
				$prj_summary = get_field( 'prj_summary' ) ?: get_the_excerpt();
				$prj_city    = get_field( 'prj_city' )    ?: '';
				$prj_state   = get_field( 'prj_state' )   ?: 'IL';
				$prj_date    = get_field( 'prj_date' )     ?: '';
				$prj_img     = get_field( 'prj_img' );
				$prj_svcs    = get_field( 'prj_svcs' );   // array of WP_Post objects
				$prj_ba      = get_field( 'prj_ba' );      // bool

				// Image – ACF field first, then featured image, then placeholder
				if ( ! empty( $prj_img['url'] ) ) {
					$img_url = esc_url( $prj_img['url'] );
					$img_alt = esc_attr( $prj_img['alt'] ?: $prj_title );
				} elseif ( has_post_thumbnail() ) {
					$img_url = esc_url( get_the_post_thumbnail_url( null, 'medium_large' ) );
					$img_alt = esc_attr( get_the_title() );
				} else {
					$img_url = '';
					$img_alt = '';
				}

				// Date display
				$date_display = '';
				if ( $prj_date ) {
					$ts = strtotime( $prj_date );
					$date_display = $ts ? date_i18n( 'M Y', $ts ) : '';
				}

				// Location label
				$location_label = trim( $prj_city . ( $prj_city && $prj_state ? ', ' : '' ) . $prj_state );
				?>

				<!-- CARD -->
				<article class="prj-card" itemscope itemtype="https://schema.org/CreativeWork">

					<!-- Image / thumb -->
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
									<rect width="48" height="48" rx="6" fill="#1e2536"/>
									<path d="M10 34l10-12 7 8 5-6 6 10H10z" fill="#2d3555"/>
									<circle cx="16" cy="18" r="3" fill="#2d3555"/>
								</svg>
							</div>
						<?php endif; ?>

						<?php if ( $prj_ba ) : ?>
							<span class="prj-card__badge prj-card__badge--ba">Before &amp; After</span>
						<?php endif; ?>
					</a>

					<!-- Body -->
					<div class="prj-card__body">

						<!-- Meta row: location + date -->
						<div class="prj-card__meta">
							<?php if ( $location_label ) : ?>
							<span class="prj-card__meta-loc">
								<svg width="12" height="14" viewBox="0 0 12 14" fill="currentColor" aria-hidden="true">
									<path d="M6 0C3.24 0 1 2.24 1 5c0 3.75 5 9 5 9s5-5.25 5-9c0-2.76-2.24-5-5-5zm0 6.75A1.75 1.75 0 1 1 6 3.25a1.75 1.75 0 0 1 0 3.5z"/>
								</svg>
								<?php echo esc_html( $location_label ); ?>
							</span>
							<?php endif; ?>
							<?php if ( $date_display ) : ?>
							<span class="prj-card__meta-date"><?php echo esc_html( $date_display ); ?></span>
							<?php endif; ?>
						</div>

						<!-- Title -->
						<h2 class="prj-card__title" itemprop="name">
							<a href="<?php the_permalink(); ?>"><?php echo esc_html( $prj_title ); ?></a>
						</h2>

						<!-- Summary -->
						<?php if ( $prj_summary ) : ?>
						<p class="prj-card__summary" itemprop="description">
							<?php echo esc_html( wp_trim_words( $prj_summary, 20, '…' ) ); ?>
						</p>
						<?php endif; ?>

						<!-- Service tags -->
						<?php if ( ! empty( $prj_svcs ) ) : ?>
						<ul class="prj-card__tags" aria-label="Services">
							<?php foreach ( array_slice( $prj_svcs, 0, 3 ) as $svc ) :
								$svc_title = get_the_title( $svc->ID );
							?>
							<li class="prj-card__tag"><?php echo esc_html( $svc_title ); ?></li>
							<?php endforeach; ?>
						</ul>
						<?php endif; ?>

						<!-- CTA -->
						<a href="<?php the_permalink(); ?>" class="prj-card__cta" aria-label="View project: <?php echo esc_attr( $prj_title ); ?>">
							View Project <span aria-hidden="true">&rarr;</span>
						</a>

					</div><!-- /.prj-card__body -->

				</article><!-- /.prj-card -->

			<?php endwhile; ?>

			</div><!-- /.prj-grid -->

			<!-- ── PAGINATION ────────────────────────────────────────────── -->
			<nav class="prj-pagination" aria-label="Projects pagination">
				<?php
				echo paginate_links( array(
					'prev_text' => '&larr; Newer',
					'next_text' => 'Older &rarr;',
					'type'      => 'list',
				) );
				?>
			</nav>

		<?php else : ?>

			<p class="prj-archive__empty">No projects found. Check back soon!</p>

		<?php endif; ?>

	</div><!-- /.prj-archive__container -->

</main><!-- /#prj-archive -->

<?php get_footer(); ?>
