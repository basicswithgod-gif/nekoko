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
        </aside>
        <section>
            <h1 style="margin-bottom:32px;"><?php single_cat_title("Usluge: "); the_archive_title(); ?><?php if(!is_archive()) echo "Sve usluge"; ?></h1>
            <?php if(have_posts()): ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px;">
                <?php while(have_posts()): the_post(); ?>
                <article class="listing-card">
                    <?php if(has_post_thumbnail()): ?><img class="card-image" src="<?php the_post_thumbnail_url("medium"); ?>" alt="<?php the_title_attribute(); ?>"><?php else: ?><div class="card-image" style="background:var(--nekoko-light);display:flex;align-items:center;justify-content:center;font-size:3rem;">🏷️</div><?php endif; ?>
                    <div class="card-body">
                        <?php $cats=get_the_terms(get_the_ID(),"service_category"); if($cats&&!is_wp_error($cats)): ?>
                        <div class="card-category"><?php echo esc_html($cats[0]->name); ?></div>
                        <?php endif; ?>
                        <h3><a href="<?php the_permalink(); ?>" style="color:var(--nekoko-dark);"><?php the_title(); ?></a></h3>
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
            <div style="text-align:center;padding:80px 0;"><p style="font-size:1.2rem;color:#666;">Jos nema usluga u ovoj kategoriji.</p><a href="<?php echo esc_url(home_url()); ?>" class="nekoko-btn">Nazad na pocetnu</a></div>
            <?php endif; ?>
        </section>
    </div>
</main>
<?php get_footer(); ?>
