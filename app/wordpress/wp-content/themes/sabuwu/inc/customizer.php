<?php
/**
 * Editable portfolio copy and original design examples.
 *
 * @package Sabuwu
 */

defined('ABSPATH') || exit;

/**
 * Flat defaults shared by the templates and the Customizer.
 *
 * @return array<string, bool|string>
 */
function sabuwu_defaults(): array {
    return [
        'brand' => 'SABUWU.dev',
        'content_language' => 'pt-BR',
        'copyright_name' => 'sabuwu — Software Developer',
        'seo_title' => 'sabuwu — Software Developer',
        'seo_description' => 'Portfólio de desenvolvedor Full Stack com foco em produto, engenharia e impacto.',
        'availability_show' => true,
        'availability_text' => 'Disponível para oportunidades',
        'github_url' => 'https://github.com/sabuwu',
        'discord_url' => 'https://discord.com/users/1530788587845718147',
        'email' => 'sabuwu091@gmail.com',
        'resume_url' => '',
        'hero_kicker' => 'Software Developer / 2026',
        'hero_line_1' => 'BUILD.',
        'hero_line_2' => 'SOLVE.',
        'hero_line_3' => 'IMPROVE.',
        'hero_note' => 'Desenvolvedor Full Stack focado em transformar problemas reais em software simples, rápido e fácil de manter — de APIs e automações a produtos web completos.',
        'hero_projects_label' => 'Explorar projetos',
        'hero_github_label' => 'GitHub ↗',
        'hero_resume_label' => 'Currículo ↗',
        'meta_base_label' => 'Base',
        'meta_base_value' => 'Brasil',
        'meta_focus_label' => 'Foco',
        'meta_focus_value' => 'Web, APIs & Automação',
        'meta_stack_label' => 'Stack',
        'meta_stack_value' => 'Python / FullStack / LAMP',
        'meta_interests_label' => 'Interesses',
        'meta_interests_value' => 'Linux / Infra / IA',
        'ticker_items' => "Full Stack Development\nAPIs\nAutomation\nLinux\nPerformance\nOpen Source",
        'projects_eyebrow' => '01 / Selected work',
        'projects_title' => 'Projetos que provam capacidade.',
        'projects_lead' => 'Menos “eu sei tecnologia X”.',
        'projects_lead_soft' => 'Mais problema, decisão e resultado.',
        'projects_show_demo' => true,
        'profile_eyebrow' => '02 / Why me',
        'profile_title' => 'O que diferencia meu trabalho.',
        'profile_lead' => 'Código bom não é só o que funciona.',
        'profile_lead_soft' => 'É o que outra pessoa consegue entender e continuar.',
        'proof_1_title' => 'README reproduzível',
        'proof_1_body' => 'Setup claro, dependências documentadas e instruções objetivas.',
        'proof_2_title' => 'Decisões explicadas',
        'proof_2_body' => 'Não mostro só o resultado; mostro por que a arquitetura foi escolhida.',
        'proof_3_title' => 'Impacto mensurável',
        'proof_3_body' => 'Projetos apresentados com métricas, limitações e aprendizados reais.',
        'proof_4_title' => 'Produto + engenharia',
        'proof_4_body' => 'Preocupação com experiência, manutenção, performance e não só features.',
        'experience_eyebrow' => '03 / Experience',
        'experience_title' => 'Experiência demonstrada por entregas.',
        'experience_lead' => 'A trajetória importa.',
        'experience_lead_soft' => 'Mas o que você consegue entregar importa mais.',
        'xp_1_period' => '2026 — atual',
        'xp_1_title' => 'Projetos autorais / Full Stack',
        'xp_1_description' => 'APIs, aplicações web, observabilidade, automações e infraestrutura.',
        'xp_1_type' => 'Independent',
        'xp_2_period' => '2025 — 2026',
        'xp_2_title' => 'Freelance / Web & integrações',
        'xp_2_description' => 'Sites, integrações com serviços externos, formulários e automações.',
        'xp_2_type' => 'Freelance',
        'xp_3_period' => '2025 — atual',
        'xp_3_title' => 'Open source & laboratório pessoal',
        'xp_3_description' => 'Estudos avançados de Linux, ferramentas CLI, servidores e projetos públicos.',
        'xp_3_type' => 'Open source',
        'contact_eyebrow' => '04 / Contact',
        'contact_title' => 'Vamos conversar.',
        'contact_lead' => '',
        'contact_lead_soft' => '',
        'contact_heading' => "PRECISA DE\nALGUÉM QUE\nENTREGA?",
        'contact_note' => 'Estou aberto a oportunidades em desenvolvimento web, backend, automação e infraestrutura. Se o problema for interessante, melhor ainda.',
        'contact_github_label' => 'GitHub ↗',
        'contact_discord_label' => 'Discord ↗',
    ];
}

/**
 * Resolve a named theme setting without persisting the fallback.
 *
 * Escape the returned value for its HTML context in the template.
 *
 * @param string $key Unprefixed setting name.
 * @return bool|string
 */
function sabuwu_setting(string $key) {
    $defaults = sabuwu_defaults();
    return get_theme_mod('sabuwu_' . $key, $defaults[$key] ?? '');
}

/**
 * Original example projects. Real published projects replace these examples.
 *
 * @return array<int, array<string, mixed>>
 */
function sabuwu_default_projects(): array {
    return [
        [
            'id' => 0,
            'title' => "Pulse\nSystem Monitor",
            'description' => 'Dashboard full stack para acompanhar CPU, RAM, disco e eventos do sistema em tempo real, com foco em observabilidade e diagnóstico rápido.',
            'tags' => ['React', 'TypeScript', 'Node.js', 'WebSocket', 'PostgreSQL', 'Docker'],
            'metrics' => [
                ['value' => '−35%', 'label' => 'tempo médio para localizar gargalos'],
                ['value' => '<120ms', 'label' => 'latência média da API'],
                ['value' => '24/7', 'label' => 'coleta contínua de métricas'],
            ],
            'demo_url' => '',
            'repository_url' => 'https://github.com/sabuwu',
            'case_url' => '',
        ],
        [
            'id' => 0,
            'title' => "Scout\nJobs Engine",
            'description' => 'Coletor e normalizador de vagas públicas que transforma múltiplas fontes em uma API pesquisável por stack, senioridade e modelo de trabalho.',
            'tags' => ['Python', 'FastAPI', 'Scraping', 'Redis', 'PostgreSQL'],
            'metrics' => [
                ['value' => '1.2K+', 'label' => 'registros processados'],
                ['value' => '−62%', 'label' => 'duplicatas após normalização'],
                ['value' => 'REST', 'label' => 'API própria documentada'],
            ],
            'demo_url' => '',
            'repository_url' => 'https://github.com/sabuwu',
            'case_url' => '',
        ],
        [
            'id' => 0,
            'title' => "Atlas\nFile Manager",
            'description' => 'Gerenciador de arquivos web inspirado em aplicações desktop, com upload em partes, autenticação, busca e integração com armazenamento externo.',
            'tags' => ['Go', 'React', 'JWT', 'S3', 'CI/CD'],
            'metrics' => [
                ['value' => 'Chunks', 'label' => 'uploads grandes com retomada'],
                ['value' => 'RBAC', 'label' => 'controle de acesso por função'],
                ['value' => 'CI', 'label' => 'pipeline automatizado'],
            ],
            'demo_url' => '',
            'repository_url' => 'https://github.com/sabuwu',
            'case_url' => '',
        ],
    ];
}

/** Sanitize a Customizer checkbox to an actual boolean. */
function sabuwu_sanitize_checkbox($value): bool {
    return in_array($value, [true, 1, '1'], true);
}

/** Preserve a valid language tag for screen readers and search engines. */
function sabuwu_sanitize_language($value): string {
    $language = str_replace('_', '-', sanitize_text_field($value));
    return preg_match('/^[a-z]{2,3}(?:-[a-z0-9]{2,8})*$/iD', $language) ? $language : 'pt-BR';
}

/** Allow only web URLs and suppress empty placeholder links. */
function sabuwu_sanitize_url($value): string {
    if (!is_scalar($value)) {
        return '';
    }
    $value = trim((string) $value);
    return '' === $value || '#' === $value ? '' : esc_url_raw($value, ['http', 'https']);
}

/**
 * Register native WordPress controls for every portfolio text block.
 *
 * @param WP_Customize_Manager $customize WordPress Customizer instance.
 */
function sabuwu_customize_register(WP_Customize_Manager $customize): void {
    $defaults = sabuwu_defaults();
    $customize->add_panel('sabuwu_portfolio', [
        'title' => esc_html__('Portfólio Sabuwu', 'sabuwu'),
        'description' => esc_html__('Edite os textos, contatos e informações do portfólio. Os projetos reais são gerenciados em Projetos no painel.', 'sabuwu'),
        'priority' => 30,
    ]);

    $sections = [
        'identity' => esc_html__('Identidade e disponibilidade', 'sabuwu'),
        'links' => esc_html__('Contatos e links', 'sabuwu'),
        'seo' => esc_html__('SEO do portfólio', 'sabuwu'),
        'hero' => esc_html__('Apresentação e faixa de tecnologias', 'sabuwu'),
        'projects' => esc_html__('Projetos', 'sabuwu'),
        'profile' => esc_html__('Perfil e diferenciais', 'sabuwu'),
        'experience' => esc_html__('Experiência', 'sabuwu'),
        'contact' => esc_html__('Seção de contato', 'sabuwu'),
    ];

    foreach ($sections as $section => $title) {
        $customize->add_section('sabuwu_' . $section, [
            'title' => $title,
            'panel' => 'sabuwu_portfolio',
        ]);
    }

    $controls = [
        'brand' => ['identity', esc_html__('Marca no cabeçalho', 'sabuwu')],
        'content_language' => ['identity', esc_html__('Idioma do conteúdo', 'sabuwu'), 'text', esc_html__('Código do idioma dos textos do portfólio, por exemplo pt-BR ou en-US.', 'sabuwu')],
        'copyright_name' => ['identity', esc_html__('Nome e título no rodapé', 'sabuwu')],
        'seo_title' => ['seo', esc_html__('Título da página para a aba e os buscadores', 'sabuwu')],
        'seo_description' => ['seo', esc_html__('Descrição da página para os buscadores', 'sabuwu'), 'textarea'],
        'availability_show' => ['identity', esc_html__('Exibir disponibilidade', 'sabuwu'), 'checkbox'],
        'availability_text' => ['identity', esc_html__('Texto de disponibilidade', 'sabuwu')],
        'github_url' => ['links', esc_html__('URL do GitHub', 'sabuwu'), 'url'],
        'discord_url' => ['links', esc_html__('URL do Discord', 'sabuwu'), 'url'],
        'email' => ['links', esc_html__('E-mail de contato', 'sabuwu'), 'email'],
        'resume_url' => ['links', esc_html__('URL do currículo', 'sabuwu'), 'url', esc_html__('Envie o PDF à Biblioteca de mídia e cole sua URL. Deixe vazio para ocultar o botão.', 'sabuwu')],
        'hero_kicker' => ['hero', esc_html__('Texto acima do título', 'sabuwu')],
        'hero_line_1' => ['hero', esc_html__('Título: primeira linha', 'sabuwu')],
        'hero_line_2' => ['hero', esc_html__('Título: segunda linha', 'sabuwu')],
        'hero_line_3' => ['hero', esc_html__('Título: terceira linha em contorno', 'sabuwu')],
        'hero_note' => ['hero', esc_html__('Descrição da apresentação', 'sabuwu'), 'textarea'],
        'hero_projects_label' => ['hero', esc_html__('Texto do botão de projetos', 'sabuwu')],
        'hero_github_label' => ['hero', esc_html__('Texto do botão do GitHub', 'sabuwu')],
        'hero_resume_label' => ['hero', esc_html__('Texto do botão do currículo', 'sabuwu')],
        'ticker_items' => ['hero', esc_html__('Itens da faixa de tecnologias', 'sabuwu'), 'textarea', esc_html__('Digite um item por linha. A faixa repete os itens automaticamente.', 'sabuwu')],
        'projects_show_demo' => ['projects', esc_html__('Exibir projetos de demonstração', 'sabuwu'), 'checkbox', esc_html__('Exibe os três exemplos do HTML enquanto não houver projetos publicados. Os exemplos e suas métricas devem ser substituídos por entregas reais; desaparecem automaticamente ao publicar o primeiro projeto.', 'sabuwu')],
        'contact_heading' => ['contact', esc_html__('Título grande do contato', 'sabuwu'), 'textarea', esc_html__('Digite uma linha do título por linha do campo.', 'sabuwu')],
        'contact_note' => ['contact', esc_html__('Descrição do contato', 'sabuwu'), 'textarea'],
        'contact_github_label' => ['contact', esc_html__('Texto do botão do GitHub', 'sabuwu')],
        'contact_discord_label' => ['contact', esc_html__('Texto do botão do Discord', 'sabuwu')],
    ];

    $metadata_labels = [
        'base' => esc_html__('Base', 'sabuwu'),
        'focus' => esc_html__('Foco', 'sabuwu'),
        'stack' => esc_html__('Stack', 'sabuwu'),
        'interests' => esc_html__('Interesses', 'sabuwu'),
    ];
    foreach ($metadata_labels as $key => $label) {
        $controls['meta_' . $key . '_label'] = ['hero', sprintf(esc_html__('%s: rótulo', 'sabuwu'), $label)];
        $controls['meta_' . $key . '_value'] = ['hero', sprintf(esc_html__('%s: informação', 'sabuwu'), $label)];
    }

    foreach (['projects', 'profile', 'experience', 'contact'] as $section) {
        $controls[$section . '_eyebrow'] = [$section, esc_html__('Índice e subtítulo da seção', 'sabuwu')];
        $controls[$section . '_title'] = [$section, esc_html__('Título lateral da seção', 'sabuwu')];
        $controls[$section . '_lead'] = [$section, esc_html__('Chamada: texto principal', 'sabuwu'), 'textarea'];
        $controls[$section . '_lead_soft'] = [$section, esc_html__('Chamada: texto em destaque suave', 'sabuwu'), 'textarea'];
    }

    for ($index = 1; $index <= 4; $index++) {
        $controls['proof_' . $index . '_title'] = ['profile', sprintf(esc_html__('Diferencial %d: título', 'sabuwu'), $index)];
        $controls['proof_' . $index . '_body'] = ['profile', sprintf(esc_html__('Diferencial %d: descrição', 'sabuwu'), $index), 'textarea'];
    }

    for ($index = 1; $index <= 3; $index++) {
        $controls['xp_' . $index . '_period'] = ['experience', sprintf(esc_html__('Experiência %d: período', 'sabuwu'), $index)];
        $controls['xp_' . $index . '_title'] = ['experience', sprintf(esc_html__('Experiência %d: título', 'sabuwu'), $index)];
        $controls['xp_' . $index . '_description'] = ['experience', sprintf(esc_html__('Experiência %d: descrição', 'sabuwu'), $index), 'textarea'];
        $controls['xp_' . $index . '_type'] = ['experience', sprintf(esc_html__('Experiência %d: modalidade', 'sabuwu'), $index)];
    }

    foreach ($controls as $key => $control) {
        $type = $control[2] ?? 'text';
        $sanitizer = $key === 'content_language' ? 'sabuwu_sanitize_language' : 'sanitize_text_field';
        switch ($type) {
            case 'checkbox':
                $sanitizer = 'sabuwu_sanitize_checkbox';
                break;
            case 'textarea':
                $sanitizer = 'sanitize_textarea_field';
                break;
            case 'url':
                $sanitizer = 'sabuwu_sanitize_url';
                break;
            case 'email':
                $sanitizer = 'sanitize_email';
                break;
        }

        $id = 'sabuwu_' . $key;
        $customize->add_setting($id, [
            'type' => 'theme_mod',
            'default' => $defaults[$key],
            'capability' => 'edit_theme_options',
            'sanitize_callback' => $sanitizer,
            'transport' => 'refresh',
        ]);
        $customize->add_control($id, [
            'section' => 'sabuwu_' . $control[0],
            'label' => $control[1],
            'type' => $type,
            'description' => $control[3] ?? '',
        ]);
    }
}
add_action('customize_register', 'sabuwu_customize_register');
