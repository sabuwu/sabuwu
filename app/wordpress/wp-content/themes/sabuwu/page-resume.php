<?php
/** Native, print-ready résumé page. */
defined('ABSPATH') || exit;
get_header();
?>
<main class="resume-page" id="content">
    <nav class="resume-toolbar" aria-label="<?php esc_attr_e('Controles do currículo', 'sabuwu'); ?>">
        <a class="resume-brand" href="<?php echo esc_url(home_url('/')); ?>">SABUWU.dev<span>.</span></a>
        <div class="resume-controls">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('← Voltar ao portfólio', 'sabuwu'); ?></a>
            <button type="button" id="print-resume"><?php esc_html_e('Imprimir / Salvar PDF ↗', 'sabuwu'); ?></button>
        </div>
    </nav>
    <article class="resume-sheet">
        <header class="resume-header">
            <div class="resume-identity">
                <p class="resume-eyebrow">SOFTWARE ENGINEERING / RÉSUMÉ</p>
                <h1>sabuwu<span>.</span></h1>
                <p class="resume-job-title">Software Developer <span>/</span> Backend &amp; Infrastructure</p>
                <p class="resume-specialties">Backend · Full Stack · Systems · DevOps</p>
            </div>
            <div class="resume-contact" aria-label="<?php esc_attr_e('Dados de contato', 'sabuwu'); ?>">
                <a href="mailto:sabuwu091@gmail.com">sabuwu091@gmail.com</a>
                <a href="https://github.com/sabuwu" target="_blank" rel="noopener noreferrer">github.com/sabuwu</a>
                <a href="<?php echo esc_url(home_url('/')); ?>">Developer portfolio ↗</a>
                <span>Brazil <span class="resume-muted">· City optional</span></span>
            </div>
        </header>

        <section class="resume-section" aria-labelledby="resume-summary-title">
            <div class="resume-section-heading"><h2 id="resume-summary-title">Professional summary</h2><span>01</span></div>
            <p class="resume-summary">Backend-oriented software developer with hands-on experience in web applications, automation and Linux-based environments. Works across PHP/Laravel, Python, JavaScript/TypeScript and Go, with additional exposure to systems programming, containers and cloud infrastructure. Brings critical thinking, collaborative leadership and an interest in maintainable software.</p>
        </section>

        <section class="resume-section" aria-labelledby="resume-skills-title">
            <div class="resume-section-heading"><h2 id="resume-skills-title">Technical skills</h2><span>02</span></div>
            <dl class="resume-skills">
                <div><dt>Core languages</dt><dd>PHP · Python · JavaScript / TypeScript · Go · Rust</dd></div>
                <div><dt>Frameworks</dt><dd>Laravel · React · Ruby on Rails · Tailwind CSS</dd></div>
                <div><dt>Frontend</dt><dd>HTML5 · CSS3 · Responsive interfaces</dd></div>
                <div><dt>Infrastructure</dt><dd>Linux · Docker · Podman · AWS · XAMPP / WAMP</dd></div>
                <div><dt>Additional languages</dt><dd>Ruby · C / C++ · Zig · C# · Java · Luau · Assembly</dd></div>
                <div><dt>Engineering</dt><dd>Backend development · API integration · Async programming · WebSockets · DevOps</dd></div>
            </dl>
        </section>

        <section class="resume-section" aria-labelledby="resume-experience-title">
            <div class="resume-section-heading"><h2 id="resume-experience-title">Professional experience</h2><span>03</span></div>
            <div class="resume-empty-experience">
                <div><strong>Professional roles / freelance engagements</strong><span class="resume-small-note">Add employer or client · Position · Dates</span></div>
                <p>Document two or three achievements that demonstrate ownership, technical decisions and measurable results. Do not retain this placeholder in applications.</p>
            </div>
        </section>

        <section class="resume-section" aria-labelledby="resume-projects-title">
            <div class="resume-section-heading"><h2 id="resume-projects-title">Selected projects</h2><span>04</span></div>
            <div class="resume-projects">
                <article>
                    <div><h3>YGG — Discord Automation Bot</h3><p>Developed Discord bot functionality for event-driven messaging, server interaction and terminal controls using asynchronous Python.</p></div>
                    <div class="resume-project-meta"><span>Python · Asyncio · Discord API</span><a href="https://github.com/sabuwu/YGG" target="_blank" rel="noopener noreferrer">Repository ↗</a></div>
                </article>
                <article>
                    <div><h3>Browser-Based Terminal</h3><p>Built a browser-facing Linux terminal prototype using PTY integration and WebSocket communication, with attention to terminal resizing and data flow.</p></div>
                    <div class="resume-project-meta"><span>Python · WebSockets · PTY · Linux</span><span class="resume-muted">Personal project</span></div>
                </article>
                <article>
                    <div><h3>Containerized Portfolio Infrastructure</h3><p>Configured a local WordPress environment with containerized services and database integration, working through service startup and connectivity issues.</p></div>
                    <div class="resume-project-meta"><span>Docker · WordPress · PHP · MariaDB</span><span class="resume-muted">Development environment</span></div>
                </article>
            </div>
        </section>

        <div class="resume-final-grid">
            <section class="resume-section" aria-labelledby="resume-education-title">
                <div class="resume-section-heading"><h2 id="resume-education-title">Education</h2><span>05</span></div>
                <p class="resume-pending"><strong>Education / training</strong><span>Institution · Qualification · Year</span></p>
            </section>
            <section class="resume-section" aria-labelledby="resume-languages-title">
                <div class="resume-section-heading"><h2 id="resume-languages-title">Languages</h2><span>06</span></div>
                <p class="resume-language-list">English · Portuguese · Spanish · German</p>
                <p class="resume-small-note">Add verified proficiency levels, if relevant.</p>
            </section>
        </div>
        <section class="resume-section resume-strengths" aria-labelledby="resume-strengths-title">
            <div class="resume-section-heading"><h2 id="resume-strengths-title">Additional strengths</h2><span>07</span></div>
            <p>Critical thinking · Team coordination · Risk assessment · Customer communication · SWOT analysis</p>
        </section>
        <footer class="resume-footer"><span>SABUWU.DEV / SOFTWARE DEVELOPER</span><span>Built with HTML &amp; CSS · Accessible print layout</span></footer>
    </article>
    <p class="resume-draft-note">Template note: employment, education and proficiency fields need your verified details before applying. Skills and project descriptions should also be reviewed for accuracy.</p>
</main>
<?php get_footer(); ?>
