# Documentação do projeto

O portfólio é servido pelo WordPress com o tema nativo Sabuwu. Comece por [Execução local](setup.md); use [Portfólio WordPress](portfolio.md) para editar o site pelo painel.

## Organização das pastas

| Caminho | Conteúdo |
| --- | --- |
| `app/wordpress/` | Aplicação montada nos containers; Core oficial reconstruído pela imagem. |
| `app/wordpress/wp-content/themes/sabuwu/` | Tema próprio: templates, estilos, JavaScript e instruções de instalação. |
| `app/wordpress/wp-content/mu-plugins/` | Código próprio de proteção, email local e diagnóstico de cron. |
| `app/wordpress/wp-content/plugins/` | Plugins oficiais reproduzidos a partir do manifesto; arquivos locais ignorados pelo Git. |
| `app/wordpress/wp-content/uploads/portfolio/` | Caminho reservado para originais editoriais versionáveis; outros uploads ficam locais. |
| `assets/images/` | GIFs usados pelo README de perfil GitHub. |
| `config/` | Manifesto de versões e critérios de instalação dos plugins. |
| `database/seeds/` | Snapshot editorial JSON para novas instalações. |
| `database/exports/` | Exports locais, ignorados pelo Git. |
| `backups/` | Backups operacionais privados, ignorados pelo Git. |
| `docker/` | Imagem WordPress, bootstrap, configuração PHP/Apache e serviço de cron. |
| `scripts/` | Operação do ambiente, instalação, snapshot editorial, backup e restauração. |
| `tests/` | Verificações de integração e do tema no WordPress local. |
| `dist/` | ZIP instalável do tema. |
| `docs/reference/` | HTML estático de referência do design; não é a aplicação WordPress. |

O bind mount, os scripts e o WordPress usam os caminhos em `app/wordpress/`; mantenha essa estrutura ao trabalhar no tema. `sabuwu` é o tema principal e `twentytwentyfive` permanece como tema reserva local. Dependências e artefatos gerados não precisam ser versionados.

## Guias

- [Execução local](setup.md): requisitos, configuração e inicialização.
- [Portfólio WordPress](portfolio.md): conteúdo editável, navegação e instalação do ZIP.
- [WordPress e conteúdo](wordpress.md): plugins mantidos, integrações condicionais e snapshot editorial.
- [Arquitetura](architecture.md): serviços, redes, volumes e arquivos persistidos.
- [Comandos](commands.md): manutenção, backup, restauração e integridade.
- [Segurança](security.md): controles configurados e preparação para produção.
- [Diagnóstico](troubleshooting.md): problemas comuns e investigação.
- [Registro de validação](validation.md): resultados históricos das verificações do ambiente.
- [Referência visual](reference/portfolio.html): HTML original do portfólio.

As instruções que acompanham o pacote do tema ficam em [README do tema](../app/wordpress/wp-content/themes/sabuwu/README.md).
