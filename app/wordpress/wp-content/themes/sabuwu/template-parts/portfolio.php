<?php defined('ABSPATH') || exit; ?>
<main id="content">
    <section class="hero" id="inicio" aria-labelledby="hero-heading">
        <div class="container hero-grid">
            <div class="reveal">
                <div class="kicker"><?php echo esc_html(sabuwu_setting('hero_kicker')); ?></div>
                <h1 id="hero-heading"><?php echo esc_html(sabuwu_setting('hero_line_1')); ?><br><?php echo esc_html(sabuwu_setting('hero_line_2')); ?><br><span class="outline"><?php echo esc_html(sabuwu_setting('hero_line_3')); ?></span></h1>
                <p class="hero-note"><?php echo esc_html(sabuwu_setting('hero_note')); ?></p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="#projetos"><?php echo esc_html(sabuwu_setting('hero_projects_label')); ?></a>
                    <?php foreach (['github_url' => 'hero_github_label', 'resume_url' => 'hero_resume_label'] as $key => $label) : ?>
                        <?php if (sabuwu_setting($key)) : ?>
                            <a class="btn btn-ghost" href="<?php echo esc_url(sabuwu_setting($key)); ?>"<?php echo sabuwu_link_attributes(sabuwu_setting($key)); ?>><?php echo esc_html(sabuwu_setting($label)); ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <aside class="hero-side reveal" aria-label="<?php esc_attr_e('Informações profissionais', 'sabuwu'); ?>">
                <?php foreach (['base', 'focus', 'stack', 'interests'] as $key) : ?>
                    <?php if (sabuwu_setting('meta_' . $key . '_value')) : ?>
                        <div class="meta">
                            <span><?php echo esc_html(sabuwu_setting('meta_' . $key . '_label')); ?></span>
                            <strong><?php echo esc_html(sabuwu_setting('meta_' . $key . '_value')); ?></strong>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </aside>
        </div>
    </section>

    <?php $ticker = array_values(array_filter(array_map('trim', preg_split('/\R/u', sabuwu_setting('ticker_items')) ?: []))); ?>
    <?php if ($ticker) : ?>
        <div class="ticker" aria-hidden="true">
            <div class="ticker-track">
                <?php for ($copy = 0; $copy < 2; $copy++) : ?>
                    <div class="ticker-group">
                        <?php foreach ($ticker as $item) : ?><span><?php echo esc_html($item); ?></span><?php endforeach; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>

    <section id="projetos" aria-labelledby="projects-heading">
        <div class="container section-shell">
            <aside class="section-index">
                <small><?php echo esc_html(sabuwu_setting('projects_eyebrow')); ?></small>
                <h2 id="projects-heading"><?php echo esc_html(sabuwu_setting('projects_title')); ?></h2>
            </aside>
            <div class="section-content">
                <p class="lead reveal"><?php echo sabuwu_lines(sabuwu_setting('projects_lead')); ?><br><span class="soft"><?php echo sabuwu_lines(sabuwu_setting('projects_lead_soft')); ?></span></p>
                <?php $projects = sabuwu_get_projects(); ?>
                <div class="projects">
                    <?php foreach ($projects as $index => $project) : ?>
                        <article class="project reveal">
                            <div class="project-no"><?php echo esc_html(sprintf('%02d — %02d', $index + 1, count($projects))); ?></div>
                            <div>
                                <h3><?php echo sabuwu_lines($project['title']); ?></h3>
                                <p><?php echo esc_html($project['description']); ?></p>
                                <?php if ($project['tags']) : ?>
                                    <div class="tags"><?php foreach ($project['tags'] as $tag) : ?><span class="tag"><?php echo esc_html($tag); ?></span><?php endforeach; ?></div>
                                <?php endif; ?>
                                <?php sabuwu_project_links($project); ?>
                            </div>
                            <?php if ($project['metrics']) : ?>
                                <div class="project-metrics">
                                    <?php foreach ($project['metrics'] as $metric) : ?>
                                        <div class="metric"><strong><?php echo esc_html($metric['value']); ?></strong><span><?php echo esc_html($metric['label']); ?></span></div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                    <?php if (!$projects) : ?><p class="empty-projects"><?php esc_html_e('Novos projetos em breve.', 'sabuwu'); ?></p><?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section id="perfil" aria-labelledby="profile-heading">
        <div class="container section-shell">
            <aside class="section-index">
                <small><?php echo esc_html(sabuwu_setting('profile_eyebrow')); ?></small>
                <h2 id="profile-heading"><?php echo esc_html(sabuwu_setting('profile_title')); ?></h2>
            </aside>
            <div class="section-content">
                <p class="lead reveal"><?php echo sabuwu_lines(sabuwu_setting('profile_lead')); ?> <span class="soft"><?php echo sabuwu_lines(sabuwu_setting('profile_lead_soft')); ?></span></p>
                <div class="proof-grid">
                    <?php for ($i = 1; $i <= 4; $i++) : ?>
                        <?php if (sabuwu_setting('proof_' . $i . '_title')) : ?>
                            <article class="proof reveal">
                                <span class="num"><?php echo esc_html(sprintf('%02d', $i)); ?></span>
                                <div><h3><?php echo esc_html(sabuwu_setting('proof_' . $i . '_title')); ?></h3><p><?php echo esc_html(sabuwu_setting('proof_' . $i . '_body')); ?></p></div>
                            </article>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </section>

    <section id="experiencia" aria-labelledby="experience-heading">
        <div class="container section-shell">
            <aside class="section-index">
                <small><?php echo esc_html(sabuwu_setting('experience_eyebrow')); ?></small>
                <h2 id="experience-heading"><?php echo esc_html(sabuwu_setting('experience_title')); ?></h2>
            </aside>
            <div class="section-content">
                <p class="lead reveal"><?php echo sabuwu_lines(sabuwu_setting('experience_lead')); ?> <span class="soft"><?php echo sabuwu_lines(sabuwu_setting('experience_lead_soft')); ?></span></p>
                <div class="experience">
                    <?php for ($i = 1; $i <= 3; $i++) : ?>
                        <?php if (sabuwu_setting('xp_' . $i . '_title')) : ?>
                            <article class="xp reveal">
                                <span class="xp-period"><?php echo esc_html(sabuwu_setting('xp_' . $i . '_period')); ?></span>
                                <div><h3><?php echo esc_html(sabuwu_setting('xp_' . $i . '_title')); ?></h3><p><?php echo esc_html(sabuwu_setting('xp_' . $i . '_description')); ?></p></div>
                                <span class="type"><?php echo esc_html(sabuwu_setting('xp_' . $i . '_type')); ?></span>
                            </article>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="contact" id="contato" aria-labelledby="contact-heading">
        <div class="container section-shell">
            <aside class="section-index">
                <small><?php echo esc_html(sabuwu_setting('contact_eyebrow')); ?></small>
                <h2 id="contact-heading"><?php echo esc_html(sabuwu_setting('contact_title')); ?></h2>
            </aside>
            <div class="section-content reveal">
                <?php if (sabuwu_setting('contact_lead') || sabuwu_setting('contact_lead_soft')) : ?>
                    <p class="lead"><?php echo sabuwu_lines(sabuwu_setting('contact_lead')); ?> <span class="soft"><?php echo sabuwu_lines(sabuwu_setting('contact_lead_soft')); ?></span></p>
                <?php endif; ?>
                <h2 class="big"><?php echo sabuwu_lines(sabuwu_setting('contact_heading')); ?></h2>
                <p><?php echo esc_html(sabuwu_setting('contact_note')); ?></p>
                <div class="contact-actions">
                    <?php if (sabuwu_setting('email')) : ?><a class="btn btn-primary" href="<?php echo esc_url('mailto:' . sabuwu_setting('email')); ?>"><?php echo esc_html(sabuwu_setting('email')); ?></a><?php endif; ?>
                    <?php foreach (['discord_url' => 'contact_discord_label', 'github_url' => 'contact_github_label'] as $key => $label) : ?>
                        <?php if (sabuwu_setting($key)) : ?><a class="btn btn-ghost" href="<?php echo esc_url(sabuwu_setting($key)); ?>"<?php echo sabuwu_link_attributes(sabuwu_setting($key)); ?>><?php echo esc_html(sabuwu_setting($label)); ?></a><?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</main>
