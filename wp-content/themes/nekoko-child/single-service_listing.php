<?php get_header(); ?>
<main class="nekoko-single-listing" style="padding:40px 20px;max-width:1200px;margin:0 auto;">
<?php while ( have_posts() ) : the_post(); ?>
<div style="display:grid;grid-template-columns:2fr 1fr;gap:40px;">
    <div>
        <?php if ( has_post_thumbnail() ) : ?>
        <img src="<?php the_post_thumbnail_url("large"); ?>" alt="<?php the_title_attribute(); ?>" style="width:100%;border-radius:12px;margin-bottom:24px;object-fit:cover;max-height:400px;">
        <?php endif; ?>
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
            <?php $cats = get_the_terms(get_the_ID(),"service_category"); if($cats&&!is_wp_error($cats)): ?>
            <span class="badge badge-approved"><?php echo esc_html($cats[0]->name); ?></span>
            <?php endif; ?>
            <?php $sl_grad = get_post_meta(get_the_ID(),"_nekoko_grad",true); if($sl_grad): ?>
            <span style="font-size:.85rem;color:#666;">📍 <?php echo esc_html($sl_grad); ?></span>
            <?php endif; ?>
            <?php $avg = get_post_meta(get_the_ID(),"_nekoko_avg_rating",true); $cnt = get_post_meta(get_the_ID(),"_nekoko_review_count",true); if($avg): ?>
            <span class="star-rating"><?php for($i=1;$i<=5;$i++) echo "<span class=\"star ".($i<=round($avg)?"filled":"")."\">&#9733;</span>"; ?></span>
            <span style="color:#666;font-size:.9rem;">(<?php echo intval($cnt); ?> recenzija)</span>
            <?php endif; ?>
        </div>
        <h1><?php the_title(); ?></h1>
        <?php $sl_price = (int) get_post_meta(get_the_ID(),"_nekoko_price",true); if($sl_price): ?>
        <div style="font-size:1.5rem;font-weight:800;color:var(--nekoko-blue);margin-bottom:16px;"><?php echo number_format($sl_price,0,'.','.'); ?> <span style="font-size:1rem;font-weight:400;">RSD</span></div>
        <?php endif; ?>
        <div class="listing-description"><?php the_content(); ?></div>
        <hr style="margin:32px 0;">
        <?php echo do_shortcode("[nekoko_reviews listing_id=".get_the_ID()."]"); ?>
    </div>
    <div>
        <div style="background:#fff;border-radius:12px;padding:24px;box-shadow:var(--nekoko-shadow);position:sticky;top:90px;">
            <?php $provider=get_user_by("id",get_the_author_meta("ID")); ?>
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
                <?php echo get_avatar($provider->ID,56,"","",["style"=>"border-radius:50%;"]); ?>
                <div><strong><?php echo esc_html($provider->display_name); ?></strong><br><span style="color:#666;font-size:.85rem;">Pružalac usluge</span></div>
            </div>
            <?php echo do_shortcode("[nekoko_booking_form listing_id=".get_the_ID()."]"); ?>
        </div>
    </div>
</div>
<?php endwhile; ?>
</main>
<?php get_footer(); ?>
