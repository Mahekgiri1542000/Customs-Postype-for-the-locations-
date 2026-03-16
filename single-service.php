<?php
/**
 * Single Service Template
 * Matches the same design language used by location/project pages.
 */

get_header();

while ( have_posts() ) :
	the_post();

	$post_id = get_the_ID();

	$svc_name      = trim( (string) get_field( 'svc_name', $post_id ) ) ?: get_the_title();
	$svc_h1        = trim( (string) get_field( 'svc_h1', $post_id ) ) ?: $svc_name;
	$svc_summary   = trim( (string) get_field( 'svc_summary', $post_id ) );
	$svc_body      = get_field( 'svc_body', $post_id );
	$svc_type      = trim( (string) get_field( 'svc_type', $post_id ) );
	$svc_status    = trim( (string) get_field( 'svc_status', $post_id ) );
	$svc_problems  = get_field( 'svc_problems', $post_id );
	$svc_benefits  = get_field( 'svc_benefits', $post_id );
	$svc_uses      = get_field( 'svc_uses', $post_id );
	$svc_materials = get_field( 'svc_materials', $post_id );
	$svc_faqs      = get_field( 'svc_faqs', $post_id );
	$svc_locs      = get_field( 'svc_locs', $post_id );
	$svc_prjs      = get_field( 'svc_prjs', $post_id );
	$svc_related   = get_field( 'svc_related', $post_id );
	$svc_gallery   = get_field( 'svc_gallery', $post_id );
	$svc_img       = get_field( 'svc_img', $post_id );

	$hero_url = '';
	$hero_alt = '';
	if ( ! empty( $svc_img['url'] ) ) {
		$hero_url = esc_url( $svc_img['url'] );
		$hero_alt = esc_attr( $svc_img['alt'] ?: $svc_h1 );
	} elseif ( has_post_thumbnail() ) {
		$hero_url = esc_url( get_the_post_thumbnail_url( null, 'full' ) );
		$hero_alt = esc_attr( $svc_h1 );
	}

	$status_map = array(
		'active'      => 'Active',
		'inactive'    => 'Inactive',
		'coming_soon' => 'Coming Soon',
	);
	$status_label = isset( $status_map[ $svc_status ] ) ? $status_map[ $svc_status ] : '';
	?>

	<main id="svc-single" class="svcs-wrap">

		<section class="svcs-hero<?php echo $hero_url ? ' svcs-hero--has-img' : ''; ?>">
			<?php if ( $hero_url ) : ?>
				<img class="svcs-hero__bg" src="<?php echo $hero_url; ?>" alt="<?php echo $hero_alt; ?>" width="1400" height="700">
				<div class="svcs-hero__overlay" aria-hidden="true"></div>
			<?php else : ?>
				<div class="svcs-hero__overlay svcs-hero__overlay--solid" aria-hidden="true"></div>
			<?php endif; ?>

			<div class="svcs-container svcs-hero__inner">
				<nav class="svcs-breadcrumb" aria-label="Breadcrumb">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
					<span aria-hidden="true">/</span>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'service' ) ); ?>">Services</a>
					<span aria-hidden="true">/</span>
					<span aria-current="page"><?php echo esc_html( $svc_name ); ?></span>
				</nav>

				<h1 class="svcs-hero__title"><?php echo esc_html( $svc_h1 ); ?></h1>
				<?php if ( $svc_summary ) : ?>
					<p class="svcs-hero__summary"><?php echo esc_html( $svc_summary ); ?></p>
				<?php endif; ?>
				<div class="svcs-hero__meta">
					<?php if ( $svc_type ) : ?><span class="svcs-pill"><?php echo esc_html( $svc_type ); ?></span><?php endif; ?>
					<?php if ( $status_label ) : ?><span class="svcs-pill svcs-pill--status"><?php echo esc_html( $status_label ); ?></span><?php endif; ?>
				</div>
			</div>
		</section>

		<div class="svcs-body">
			<div class="svcs-container svcs-layout">

				<div class="svcs-content">
					<?php if ( $svc_body ) : ?>
					<section class="svcs-card">
						<h2>Service Overview</h2>
						<div class="svcs-richtext"><?php echo wp_kses_post( $svc_body ); ?></div>
					</section>
					<?php endif; ?>

					<?php if ( $svc_problems || $svc_benefits || $svc_uses ) : ?>
					<section class="svcs-card">
						<h2>Why Homeowners Choose This Service</h2>
						<div class="svcs-split-grid">
							<?php if ( $svc_problems ) : ?>
							<div class="svcs-mini-card">
								<h3>Problems We Solve</h3>
								<div class="svcs-richtext"><?php echo wp_kses_post( $svc_problems ); ?></div>
							</div>
							<?php endif; ?>
							<?php if ( $svc_benefits ) : ?>
							<div class="svcs-mini-card">
								<h3>Benefits</h3>
								<div class="svcs-richtext"><?php echo wp_kses_post( $svc_benefits ); ?></div>
							</div>
							<?php endif; ?>
						</div>
						<?php if ( $svc_uses ) : ?>
						<div class="svcs-mini-card svcs-mini-card--full">
							<h3>Common Uses</h3>
							<div class="svcs-richtext"><?php echo wp_kses_post( $svc_uses ); ?></div>
						</div>
						<?php endif; ?>
					</section>
					<?php endif; ?>

					<?php if ( ! empty( $svc_materials ) ) : ?>
					<section class="svcs-card">
						<h2>Materials</h2>
						<ul class="svcs-materials">
							<?php foreach ( $svc_materials as $material ) :
								$name = trim( (string) ( $material['name'] ?? '' ) );
								if ( ! $name ) {
									continue;
								}
								?>
								<li><?php echo esc_html( $name ); ?></li>
							<?php endforeach; ?>
						</ul>
					</section>
					<?php endif; ?>

					<?php if ( ! empty( $svc_gallery ) ) : ?>
					<section class="svcs-card">
						<h2>Service Gallery</h2>
						<div class="svcs-gallery">
							<?php foreach ( $svc_gallery as $gimg ) :
								$g_url = ! empty( $gimg['url'] ) ? esc_url( $gimg['url'] ) : '';
								$g_alt = ! empty( $gimg['alt'] ) ? esc_attr( $gimg['alt'] ) : esc_attr( $svc_name );
								if ( ! $g_url ) {
									continue;
								}
								?>
								<img src="<?php echo $g_url; ?>" alt="<?php echo $g_alt; ?>" loading="lazy" width="500" height="375">
							<?php endforeach; ?>
						</div>
					</section>
					<?php endif; ?>

					<?php if ( ! empty( $svc_faqs ) ) : ?>
					<section class="svcs-card">
						<h2>Frequently Asked Questions</h2>
						<div class="svcs-faqs">
							<?php foreach ( $svc_faqs as $faq ) :
								$q = trim( (string) ( $faq['q'] ?? '' ) );
								$a = $faq['a'] ?? '';
								if ( ! $q || ! $a ) {
									continue;
								}
								?>
								<details>
									<summary><?php echo esc_html( $q ); ?></summary>
									<div class="svcs-richtext"><?php echo wp_kses_post( $a ); ?></div>
								</details>
							<?php endforeach; ?>
						</div>
					</section>
					<?php endif; ?>
				</div>

				<aside class="svcs-sidebar" aria-label="Service sidebar">
					<div class="svcs-card svcs-card--sidebar">
						<h3>Available In</h3>
						<?php if ( ! empty( $svc_locs ) && is_array( $svc_locs ) ) : ?>
						<ul class="svcs-links">
							<?php foreach ( $svc_locs as $loc_item ) :
								$loc_post = $loc_item instanceof WP_Post ? $loc_item : get_post( (int) $loc_item );
								if ( ! $loc_post ) {
									continue;
								}
								?>
								<li><a href="<?php echo esc_url( get_permalink( $loc_post ) ); ?>"><?php echo esc_html( get_the_title( $loc_post ) ); ?></a></li>
							<?php endforeach; ?>
						</ul>
						<?php else : ?>
						<p class="svcs-muted">Locations coming soon.</p>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $svc_related ) && is_array( $svc_related ) ) : ?>
					<div class="svcs-card svcs-card--sidebar">
						<h3>Related Services</h3>
						<ul class="svcs-links">
							<?php foreach ( array_slice( $svc_related, 0, 6 ) as $related_item ) :
								$related_post = $related_item instanceof WP_Post ? $related_item : get_post( (int) $related_item );
								if ( ! $related_post ) {
									continue;
								}
								?>
								<li><a href="<?php echo esc_url( get_permalink( $related_post ) ); ?>"><?php echo esc_html( get_the_title( $related_post ) ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php endif; ?>
				</aside>
			</div>
		</div>

		<?php if ( ! empty( $svc_prjs ) && is_array( $svc_prjs ) ) : ?>
		<section class="svcs-related-wrap">
			<div class="svcs-container">
				<h2 class="svcs-related-title">Recent Projects Using This Service</h2>
				<div class="svcs-related-grid">
					<?php foreach ( array_slice( $svc_prjs, 0, 3 ) as $project_item ) :
						$project_post = $project_item instanceof WP_Post ? $project_item : get_post( (int) $project_item );
						if ( ! $project_post ) {
							continue;
						}
						$p_title = get_field( 'prj_title', $project_post->ID ) ?: get_the_title( $project_post );
						$p_city  = trim( (string) get_field( 'prj_city', $project_post->ID ) );
						?>
						<article class="svcs-related-card">
							<h3><a href="<?php echo esc_url( get_permalink( $project_post ) ); ?>"><?php echo esc_html( $p_title ); ?></a></h3>
							<?php if ( $p_city ) : ?><p><?php echo esc_html( $p_city ); ?></p><?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php endif; ?>

	</main>

<?php endwhile; ?>

<?php get_footer(); ?>
