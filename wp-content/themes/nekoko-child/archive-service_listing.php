<?php get_header(); ?>
<main style="padding:60px 20px;max-width:1200px;margin:0 auto;">
    <div style="display:grid;grid-template-columns:240px 1fr;gap:40px;">
        <aside style="background:#fff;border-radius:12px;padding:24px;box-shadow:var(--nekoko-shadow);height:fit-content;">
            <h3 style="margin-top:0;">Kategorije</h3>
            <ul style="list-style:none;padding:0;margin:0;">
                <?php foreach(get_terms(["taxonomy"=>"service_category","hide_empty"=>false]) as $cat): ?>
                <li style="margin-bottom:8px;"><a href="<?php echo esc_url(get_term_link($cat)); ?>" style="color:var(--nekoko-dark);display:flex;justify-content:space-between;"><?php echo esc_html($cat->name); ?><span style="color:#999;"><?php echo $cat->count; ?></span></a></li>
                <?php endforeach; ?>
            </ul>
            <hr style="margin:20px 0;">
            <a href="<?php echo esc_url(home_url('/pretraga/')); ?>" class="nekoko-btn" style="width:100%;text-align:center;display:block;box-sizing:border-box;">Napredno filtriranje</a>
        </aside>
        <section>
            <h1 style="margin-bottom:32px;"><?php the_archive_title('', false) ?: 'Sve usluge'; ?></h1>
            <?php if(have_posts()): ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px;">
                <?php while(have_posts()): the_post();
                    $a_cats  = get_the_terms(get_the_ID(),"service_category");
                    $a_grad  = get_post_meta(get_the_ID(),"_nekoko_grad",true);
                    $a_price = (int)get_post_meta(get_the_ID(),"_nekoko_price",true);
                    $a_avg   = (float)get_post_meta(get_the_ID(),"_nekoko_avg_rating",true);
                    $a_cnt   = (int)get_post_meta(get_the_ID(),"_nekoko_review_count",true);
                ?>
                <article class="listing-card">
                    <?php if(has_post_thumbnail()): ?>
                    <a href="<?php the_permalink(); ?>"><img class="card-image" src="<?php the_post_thumbnail_url("medium"); ?>" alt="<?php the_title_attribute(); ?>"></a>
                    <?php else: ?>
                    <a href="<?php the_permalink(); ?>"><div class="card-image" style="background:var(--nekoko-light);display:flex;align-items:center;justify-content:center;font-size:3rem;">&#127991;</div></a>
                    <?php endif; ?>
                    <div class="card-body">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                            <?php if($a_cats&&!is_wp_error($a_cats)): ?><div class="card-category"><?php echo esc_html($a_cats[0]->name); ?></div><?php endif; ?>
                            <?php if($a_grad): ?><span style="font-size:.75rem;color:#666;">&#128205; <?php echo esc_html($a_grad); ?></span><?php endif; ?>
                        </div>
                        <h3><a href="<?php the_permalink(); ?>" style="color:var(--nekoko-dark);"><?php the_title(); ?></a></h3>
                        <?php if($a_avg): ?>
                        <div class="star-rating" style="margin-bottom:6px;"><?php for($i=1;$i<=5;$i++) echo '<span class="star '.($i<=round($a_avg)?'filled':'').'">&#9733;</span>'; ?><span style="color:#999;font-size:.8rem;margin-left:4px;">(<?php echo $a_cnt; ?>)</span></div>
                        <?php endif; ?>
                        <?php if($a_price): ?><div style="font-size:.95rem;font-weight:700;color:var(--nekoko-blue);margin-bottom:10px;"><?php echo number_format($a_price,0,'.','.'); ?> RSD</div><?php endif; ?>
                        <p style="color:#666;font-size:.875rem;margin:0 0 12px;"><?php echo esc_html(wp_trim_words(get_the_excerpt(),15)); ?></p>
                        <div class="card-meta">
                            <span><?php echo get_avatar(get_the_author_meta("ID"),20,"","",["style"=>"border-radius:50%;"]); ?></span>
                            <span><?php the_author(); ?></span>
                        </div>
                    </div>
                </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(); ?>
            <?php else: ?>
            <div style="text-align:center;padding:80px 0;"><p style="font-size:1.2rem;color:#666;">Nema usluga u ovoj kategoriji.</p><a href="<?php echo esc_url(home_url()); ?>" class="nekoko-btn">Nazad na pocetnu</a></div>
            <?php endif; ?>
        </section>
    </div>
</main>
<?php get_footer(); ?>
