<?php
/**
 * Single Project Template
 * All ACF fields from group_prj_v1: prj_title, prj_summary, prj_date,
 * prj_status, prj_city, prj_county, prj_state, prj_loc, prj_coms,
 * prj_svcs, prj_scope, prj_challenge, prj_result, prj_materials,
 * prj_img, prj_gallery, prj_ba, prj_before, prj_after, prj_schema, prj_sameas.
 *
 * Layout:
 *   1. Hero (full-width image + gradient + meta)
 *   2. Body: content column + sidebar (2-col grid → stacked mobile)
 *      Content: Before/After | Gallery | Scope/Challenge/Result | Materials
 *      Sidebar: Quick Facts | CTA | Related Projects (compact)
 *   3. Full-width Related Projects strip
 *   4. Back-to-portfolio CTA bar
 */

get_header();

while ( have_posts() ) :
	the_post();

	$post_id = get_the_ID();

	// ── ACF fields ─────────────────────────────────────────────────────────
	$prj_title     = trim( (string) get_field( 'prj_title', $post_id ) ) ?: get_the_title();
	$prj_summary   = trim( (string) get_field( 'prj_summary', $post_id ) );
	$prj_date_raw  = (string) get_field( 'prj_date', $post_id );
	$prj_status    = (string) get_field( 'prj_status', $post_id );
	$prj_city      = trim( (string) get_field( 'prj_city', $post_id ) );
	$prj_county    = trim( (string) get_field( 'prj_county', $post_id ) );
	$prj_state     = trim( (string) get_field( 'prj_state', $post_id ) ) ?: 'IL';
	$prj_loc       = get_field( 'prj_loc', $post_id );   // array of WP_Post
	$prj_svcs      = get_field( 'prj_svcs', $post_id );  // array of WP_Post
	$prj_scope     = get_field( 'prj_scope', $post_id );
	$prj_challenge = get_field( 'prj_challenge', $post_id );
	$prj_result    = get_field( 'prj_result', $post_id );
	$prj_materials = get_field( 'prj_materials', $post_id ); // repeater
	$prj_img       = get_field( 'prj_img', $post_id );  // image array
	$prj_gallery   = get_field( 'prj_gallery', $post_id ); // gallery
	$prj_ba        = (bool) get_field( 'prj_ba', $post_id );
	$prj_before    = $prj_ba ? get_field( 'prj_before', $post_id ) : null;
	$prj_after     = $prj_ba ? get_field( 'prj_after', $post_id )  : null;

	// Related location post (first item from relationship)
	$loc_post = null;
	if ( ! empty( $prj_loc ) ) {
		$loc_post = $prj_loc[0] instanceof WP_Post ? $prj_loc[0] : get_post( (int) $prj_loc[0] );
	}

	// Hero image (ACF field → featured image → null)
	$hero_url = '';
	$hero_alt = '';
	if ( ! empty( $prj_img['url'] ) ) {
		$hero_url = esc_url( $prj_img['url'] );
		$hero_alt = esc_attr( $prj_img['alt'] ?: $prj_title );
	} elseif ( has_post_thumbnail() ) {
		$hero_url = esc_url( get_the_post_thumbnail_url( null, 'full' ) );
		$hero_alt = esc_attr( $prj_title );
	}

	// Date
	$date_display = '';
	if ( $prj_date_raw ) {
		$ts = strtotime( $prj_date_raw );
		$date_display = $ts ? date_i18n( 'F j, Y', $ts ) : '';
	}

	// Location label
	$loc_label = trim( $prj_city . ( $prj_city && $prj_state ? ', ' : '' ) . $prj_state );

	// B/A images
	$before_url = ! empty( $prj_before['url'] ) ? esc_url( $prj_before['url'] ) : '';
	$after_url  = ! empty( $prj_after['url'] )  ? esc_url( $prj_after['url'] )  : '';
	$before_alt = ! empty( $prj_before['alt'] )  ? esc_attr( $prj_before['alt'] ) : esc_attr( 'Before – ' . $prj_title );
	$after_alt  = ! empty( $prj_after['alt'] )   ? esc_attr( $prj_after['alt'] )  : esc_attr( 'After – ' . $prj_title );

	// Related projects — same location page, exclude current
	$related_posts = array();
	if ( $loc_post ) {
		$related_query = new WP_Query( array(
			'post_type'      => 'project',
			'posts_per_page' => 3,
			'post__not_in'   => array( $post_id ),
			'meta_query'     => array(
				array(
					'key'     => 'prj_loc',
					'value'   => '"' . $loc_post->ID . '"',
					'compare' => 'LIKE',
				),
			),
		) );
		$related_posts = $related_query->posts;
		wp_reset_postdata();
	}
	?>

	<main id="prj-single" class="prjs-wrap">

		<!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
		     SECTION 1 — HERO
		━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
		<section class="prjs-hero<?php echo $hero_url ? ' prjs-hero--has-img' : ''; ?>">

			<?php if ( $hero_url ) : ?>
				<img class="prjs-hero__bg"
				     src="<?php echo $hero_url; ?>"
				     alt="<?php echo $hero_alt; ?>"
				     width="1400" height="700">
				<div class="prjs-hero__overlay" aria-hidden="true"></div>
			<?php else : ?>
				<div class="prjs-hero__overlay prjs-hero__overlay--solid" aria-hidden="true"></div>
			<?php endif; ?>

			<div class="prjs-hero__inner">

				<!-- Breadcrumb -->
				<nav class="prjs-breadcrumb" aria-label="Breadcrumb">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
					<span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ); ?>">Projects</a>
					<span aria-hidden="true">/</span>
					<span aria-current="page"><?php echo esc_html( $prj_title ); ?></span>
				</nav>

				<!-- Title block -->
				<div class="prjs-hero__content">
					<?php if ( $prj_ba ) : ?>
						<span class="prjs-badge prjs-badge--ba">Before &amp; After</span>
					<?php endif; ?>

					<h1 class="prjs-hero__title"><?php echo esc_html( $prj_title ); ?></h1>

					<?php if ( $prj_summary ) : ?>
						<p class="prjs-hero__summary"><?php echo esc_html( $prj_summary ); ?></p>
					<?php endif; ?>

					<!-- Meta pill row -->
					<div class="prjs-hero__meta">
						<?php if ( $loc_label ) : ?>
						<span class="prjs-meta-pill">
							<svg width="12" height="14" viewBox="0 0 12 14" fill="currentColor" aria-hidden="true">
								<path d="M6 0C3.24 0 1 2.24 1 5c0 3.75 5 9 5 9s5-5.25 5-9c0-2.76-2.24-5-5-5zm0 6.75A1.75 1.75 0 1 1 6 3.25a1.75 1.75 0 0 1 0 3.5z"/>
							</svg>
							<?php echo esc_html( $loc_label ); ?>
						</span>
						<?php endif; ?>
						<?php if ( $date_display ) : ?>
						<span class="prjs-meta-pill">
							<svg width="13" height="13" viewBox="0 0 13 13" fill="currentColor" aria-hidden="true">
								<path d="M4 0v1H1.5A1.5 1.5 0 0 0 0 2.5v9A1.5 1.5 0 0 0 1.5 13h10A1.5 1.5 0 0 0 13 11.5v-9A1.5 1.5 0 0 0 11.5 1H9V0H8v1H5V0H4zm0 2v1h1V2h3v1h1V2h1.5a.5.5 0 0 1 .5.5V4H1V2.5a.5.5 0 0 1 .5-.5H4zm-3 3h11v6.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5V5z"/>
							</svg>
							<?php echo esc_html( $date_display ); ?>
						</span>
						<?php endif; ?>
						<?php if ( ! empty( $prj_svcs ) ) :
							foreach ( array_slice( $prj_svcs, 0, 2 ) as $s ) : ?>
							<span class="prjs-meta-pill prjs-meta-pill--svc"><?php echo esc_html( get_the_title( $s->ID ) ); ?></span>
						<?php endforeach; endif; ?>
					</div>
				</div><!-- /.prjs-hero__content -->

			</div><!-- /.prjs-hero__inner -->
		</section>
		<!-- ━━ END HERO ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->


		<!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
		     SECTION 2 — BODY (content + sidebar)
		━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
		<div class="prjs-body">
			<div class="prjs-container">
				<div class="prjs-layout">

					<!-- ── MAIN CONTENT COLUMN ─────────────────────────── -->
					<div class="prjs-content">

						<?php // ── Before / After ────────────────────────── ?>
						<?php if ( $prj_ba && ( $before_url || $after_url ) ) : ?>
						<section class="prjs-section prjs-ba">
							<h2 class="prjs-section__title">Before &amp; After</h2>
							<div class="prjs-ba__grid">
								<?php if ( $before_url ) : ?>
								<figure class="prjs-ba__item">
									<img src="<?php echo $before_url; ?>"
									     alt="<?php echo $before_alt; ?>"
									     loading="lazy" width="700" height="500">
									<figcaption class="prjs-ba__label prjs-ba__label--before">Before</figcaption>
								</figure>
								<?php endif; ?>
								<?php if ( $after_url ) : ?>
								<figure class="prjs-ba__item">
									<img src="<?php echo $after_url; ?>"
									     alt="<?php echo $after_alt; ?>"
									     loading="lazy" width="700" height="500">
									<figcaption class="prjs-ba__label prjs-ba__label--after">After</figcaption>
								</figure>
								<?php endif; ?>
							</div>
						</section>
						<?php endif; ?>

						<?php // ── Gallery ───────────────────────────────── ?>
						<?php if ( ! empty( $prj_gallery ) ) : ?>
						<section class="prjs-section prjs-gallery">
							<h2 class="prjs-section__title">Project Gallery</h2>
							<div class="prjs-gallery__grid">
								<?php foreach ( $prj_gallery as $gimg ) :
									$g_url = ! empty( $gimg['url'] )  ? esc_url( $gimg['url'] )  : '';
									$g_alt = ! empty( $gimg['alt'] )  ? esc_attr( $gimg['alt'] ) : esc_attr( $prj_title );
									if ( ! $g_url ) { continue; }
								?>
								<a href="<?php echo $g_url; ?>"
								   class="prjs-gallery__item"
								   target="_blank"
								   rel="noopener"
								   aria-label="Gallery image: <?php echo $g_alt; ?>">
									<img src="<?php echo $g_url; ?>"
									     alt="<?php echo $g_alt; ?>"
									     loading="lazy" width="500" height="375">
								</a>
								<?php endforeach; ?>
							</div>
						</section>
						<?php endif; ?>

						<?php // ── Project Narrative ─────────────────────── ?>
						<?php if ( $prj_scope || $prj_challenge || $prj_result ) : ?>
						<section class="prjs-section prjs-narrative">
							<h2 class="prjs-section__title">Project Details</h2>
							<div class="prjs-narrative__tabs" role="tablist" aria-label="Project details">
								<?php
								$tabs = array();
								if ( $prj_scope     ) { $tabs['scope']     = array( 'label' => 'Scope of Work',    'content' => $prj_scope ); }
								if ( $prj_challenge ) { $tabs['challenge']  = array( 'label' => 'The Challenge',    'content' => $prj_challenge ); }
								if ( $prj_result    ) { $tabs['result']     = array( 'label' => 'The Result',       'content' => $prj_result ); }
								$first = true;
								foreach ( $tabs as $id => $tab ) :
								?>
								<button class="prjs-tab-btn<?php echo $first ? ' is-active' : ''; ?>"
								        role="tab"
								        aria-selected="<?php echo $first ? 'true' : 'false'; ?>"
								        aria-controls="prjs-tab-<?php echo esc_attr( $id ); ?>"
								        data-tab="<?php echo esc_attr( $id ); ?>">
									<?php echo esc_html( $tab['label'] ); ?>
								</button>
								<?php $first = false; endforeach; ?>
							</div>
							<?php
							$first = true;
							foreach ( $tabs as $id => $tab ) :
							?>
							<div class="prjs-tab-panel<?php echo $first ? ' is-active' : ''; ?>"
							     id="prjs-tab-<?php echo esc_attr( $id ); ?>"
							     role="tabpanel"
							     tabindex="0">
								<div class="prjs-narrative__body">
									<?php echo wp_kses_post( $tab['content'] ); ?>
								</div>
							</div>
							<?php $first = false; endforeach; ?>
						</section>
						<?php endif; ?>

						<?php // ── Materials ─────────────────────────────── ?>
						<?php if ( ! empty( $prj_materials ) ) : ?>
						<section class="prjs-section prjs-materials">
							<h2 class="prjs-section__title">Materials Used</h2>
							<ul class="prjs-materials__list">
								<?php foreach ( $prj_materials as $mat ) :
									$mat_name = trim( (string) ( $mat['name'] ?? '' ) );
									if ( ! $mat_name ) { continue; }
								?>
								<li class="prjs-materials__item">
									<svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
										<circle cx="7" cy="7" r="6" stroke="#e8b84b" stroke-width="1.5"/>
										<path d="M4.5 7l2 2 3-3" stroke="#e8b84b" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
									<?php echo esc_html( $mat_name ); ?>
								</li>
								<?php endforeach; ?>
							</ul>
						</section>
						<?php endif; ?>

					</div><!-- /.prjs-content -->

					<!-- ── SIDEBAR ─────────────────────────────────────── -->
					<aside class="prjs-sidebar" aria-label="Project details sidebar">

						<!-- Quick Facts card -->
						<div class="prjs-sidebar__card prjs-quick-facts">
							<h3 class="prjs-sidebar__card-title">Quick Facts</h3>
							<dl class="prjs-facts">
								<?php if ( $loc_label ) : ?>
								<dt>Location</dt>
								<dd>
									<?php if ( $loc_post ) : ?>
										<a href="<?php echo esc_url( get_permalink( $loc_post ) ); ?>"
										   class="prjs-facts__link">
											<?php echo esc_html( $loc_label ); ?>
										</a>
									<?php else : ?>
										<?php echo esc_html( $loc_label ); ?>
									<?php endif; ?>
								</dd>
								<?php endif; ?>

								<?php if ( $prj_county ) : ?>
								<dt>County</dt>
								<dd><?php echo esc_html( $prj_county ); ?></dd>
								<?php endif; ?>

								<?php if ( $date_display ) : ?>
								<dt>Completed</dt>
								<dd><?php echo esc_html( $date_display ); ?></dd>
								<?php endif; ?>

								<?php if ( ! empty( $prj_svcs ) ) : ?>
								<dt>Services</dt>
								<dd class="prjs-facts__services">
									<?php foreach ( $prj_svcs as $svc ) : ?>
									<span><?php echo esc_html( get_the_title( $svc->ID ) ); ?></span>
									<?php endforeach; ?>
								</dd>
								<?php endif; ?>

								<?php
								$coms = get_the_terms( $post_id, 'community' );
								if ( ! empty( $coms ) && ! is_wp_error( $coms ) ) : ?>
								<dt>Community</dt>
								<dd>
									<?php foreach ( $coms as $com ) :
										echo '<a href="' . esc_url( get_term_link( $com ) ) . '" class="prjs-facts__link">' . esc_html( $com->name ) . '</a> ';
									endforeach; ?>
								</dd>
								<?php endif; ?>
							</dl>
						</div><!-- /.prjs-quick-facts -->

						<!-- CTA card -->
						<div class="prjs-sidebar__card prjs-cta-card">
							<p class="prjs-cta-card__headline">Ready for your own transformation?</p>
							<p class="prjs-cta-card__body">The Floor 4 U serves all of the Chicago Southland. Get a free in-home estimate.</p>
							<a href="<?php echo esc_url( $loc_post ? get_permalink( $loc_post ) : home_url( '/contact/' ) ); ?>"
							   class="prjs-cta-card__btn">
								Request a Free Estimate
							</a>
							<a href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ); ?>"
							   class="prjs-cta-card__back">
								&larr; View All Projects
							</a>
						</div><!-- /.prjs-cta-card -->

					</aside><!-- /.prjs-sidebar -->

				</div><!-- /.prjs-layout -->
			</div><!-- /.prjs-container -->
		</div><!-- /.prjs-body -->


		<!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
		     SECTION 3 — RELATED PROJECTS
		━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
		<?php if ( ! empty( $related_posts ) ) : ?>
		<section class="prjs-related">
			<div class="prjs-container">
				<h2 class="prjs-related__title">More Projects in <?php echo esc_html( $prj_city ?: 'This Area' ); ?></h2>
				<div class="prjs-related__grid">
					<?php foreach ( $related_posts as $rp ) :
						$r_title   = get_field( 'prj_title', $rp->ID ) ?: get_the_title( $rp->ID );
						$r_summary = get_field( 'prj_summary', $rp->ID );
						$r_img     = get_field( 'prj_img', $rp->ID );
						$r_city    = get_field( 'prj_city', $rp->ID );
						$r_state   = get_field( 'prj_state', $rp->ID ) ?: 'IL';

						if ( ! empty( $r_img['url'] ) ) {
							$r_img_url = esc_url( $r_img['url'] );
							$r_img_alt = esc_attr( $r_img['alt'] ?: $r_title );
						} elseif ( has_post_thumbnail( $rp->ID ) ) {
							$r_img_url = esc_url( get_the_post_thumbnail_url( $rp->ID, 'medium_large' ) );
							$r_img_alt = esc_attr( $r_title );
						} else {
							$r_img_url = '';
							$r_img_alt = '';
						}
					?>
					<article class="prjs-related__card">
						<?php if ( $r_img_url ) : ?>
						<a href="<?php echo esc_url( get_permalink( $rp->ID ) ); ?>" class="prjs-related__img-wrap" tabindex="-1" aria-hidden="true">
							<img src="<?php echo $r_img_url; ?>" alt="<?php echo $r_img_alt; ?>" loading="lazy" width="450" height="300">
						</a>
						<?php endif; ?>
						<div class="prjs-related__body">
							<?php if ( $r_city ) : ?>
							<p class="prjs-related__loc"><?php echo esc_html( trim( $r_city . ', ' . $r_state ) ); ?></p>
							<?php endif; ?>
							<h3 class="prjs-related__name">
								<a href="<?php echo esc_url( get_permalink( $rp->ID ) ); ?>"><?php echo esc_html( $r_title ); ?></a>
							</h3>
							<?php if ( $r_summary ) : ?>
							<p class="prjs-related__summary"><?php echo esc_html( wp_trim_words( $r_summary, 12, '…' ) ); ?></p>
							<?php endif; ?>
						</div>
					</article>
					<?php endforeach; ?>
				</div><!-- /.prjs-related__grid -->
			</div>
		</section>
		<?php endif; ?>
		<!-- ━━ END RELATED PROJECTS ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->


		<!-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
		     SECTION 4 — BOTTOM CTA BAR
		━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ -->
		<section class="prjs-bottom-cta">
			<div class="prjs-container">
				<div class="prjs-bottom-cta__inner">
					<div class="prjs-bottom-cta__text">
						<h2>See What We Can Do for Your Home</h2>
						<p>Serving Frankfort, Mokena, New Lenox, Manhattan, Tinley Park, Orland Park &amp; throughout Will County.</p>
					</div>
					<div class="prjs-bottom-cta__actions">
						<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="prjs-btn prjs-btn--primary">
							Get a Free Estimate
						</a>
						<a href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ); ?>" class="prjs-btn prjs-btn--ghost">
							View All Projects
						</a>
					</div>
				</div>
			</div>
		</section>

	</main><!-- /#prj-single -->

	<!-- Tab panel JS (inline, no deps) -->
	<script>
	( function () {
		var buttons = document.querySelectorAll( '.prjs-tab-btn' );
		if ( ! buttons.length ) { return; }
		buttons.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				var tabId   = btn.getAttribute( 'data-tab' );
				var section = btn.closest( '.prjs-narrative' );
				if ( ! section ) { return; }
				// deactivate all in this section
				section.querySelectorAll( '.prjs-tab-btn' ).forEach( function ( b ) {
					b.classList.remove( 'is-active' );
					b.setAttribute( 'aria-selected', 'false' );
				} );
				section.querySelectorAll( '.prjs-tab-panel' ).forEach( function ( p ) {
					p.classList.remove( 'is-active' );
					} );
					// activate clicked
					btn.classList.add( 'is-active' );
					btn.setAttribute( 'aria-selected', 'true' );
					var panel = section.querySelector( '#prjs-tab-' + tabId );
					if ( panel ) {
						panel.classList.add( 'is-active' );
				}
			} );
		} );
	} )();
	</script>

<?php endwhile; ?>

<?php get_footer(); ?>
