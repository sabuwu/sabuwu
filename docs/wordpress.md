# WordPress e conteúdo

Core: **7.1.3**. PHP: **8.3**. WP-CLI: **2.12.0**, com SHA-256 verificado no build. As imagens base WordPress e MariaDB são fixadas por digest. Não edite `wp-admin`, `wp-includes` ou arquivos do Core para adicionar funcionalidades.

O tema `sabuwu` e `mu-plugins/portfolio-hardening.php` são código próprio. O tema usa APIs nativas, sem construtor externo. O mu-plugin desativa pingbacks/XML-RPC, evita revelar erros detalhados de login, controla envio local de email e fornece um evento de diagnóstico de cron. Gutenberg e REST API continuam disponíveis.

## Plugins

| Plugin | Versão fixada | Estado esperado em desenvolvimento | Motivo / configuração |
| --- | --- | --- | --- |
| Akismet | 5.7.2 | Ativo | Antispam obrigatório; validação remota exige `AKISMET_API_KEY`. |
| LiteSpeed Cache | 7.9.1 | Ativo | Solicitado; Apache não oferece o page cache LiteSpeed. |
| All-In-One Security | 5.4.10 | Ativo | Solução principal sem licença externa: firewall e bloqueio de login. |
| Two Factor | 0.17.0 | Ativo | Suporte TOTP; cadastro do dispositivo é individual. |
| UpdraftPlus | 1.26.8 | Ativo | Backup operacional; destino remoto/agendamento dependem de configuração. |
| WP Mail SMTP | 4.10.2 | Ativo | Transporte SMTP configurável pelo ambiente. |
| Yoast SEO | 28.6 | Ativo | Única solução SEO: títulos, sitemap e Open Graph. |
| Query Monitor | 4.0.7 | Ativo somente em desenvolvimento | Diagnóstico; o manifesto desativa em produção. |

Versões e escopo estão em `config/plugins.lock`. O instalador baixa os arquivos versionados de `downloads.wordpress.org` via WP-CLI e não atualiza automaticamente para a versão mais recente. Os metadados oficiais indicam compatibilidade com 7.1.3, exceto Query Monitor, cujo `tested` era 7.0.7 na consulta; o funcionamento local deve ser confirmado nos testes.

Escolheu-se All-In-One Security como alternativa à preferência por Wordfence para permitir setup local sem cadastrar licença/conta. Não instalar outro firewall nem outro limitador de login. TOTP fica disponível em Usuários → Seu perfil, pelo Two Factor. O módulo TOTP interno do All-In-One Security é desabilitado para evitar duplicação com Two Factor. A proteção local usa cinco tentativas, janela de 15 minutos e bloqueio temporário de 15 minutos.

No Apache, LiteSpeed Cache oferece recursos independentes de servidor, como otimizações de CSS/JS e banco, quando configurados. O cache de páginas exige LiteSpeed/OpenLiteSpeed ou integração QUIC.cloud; não está implementado aqui. Otimização remota de imagens exige configuração externa. OPcache do PHP está habilitado. Não se adicionou Redis sem necessidade medida.

O site local usa `blog_public=0`; no lançamento público, habilite indexação, revise título/descrição/SEO e configure HTTPS. SMTP vazio bloqueia emails no desenvolvimento, evitando mensagens reais; SMTP e Akismet não podem ser considerados funcionais sem credenciais e teste externo.

## WP-Cron

`DISABLE_WP_CRON=true`. O serviço `cron` executa `wp cron event run --due-now` como usuário da aplicação e pausa `CRON_INTERVAL=60` segundos entre ciclos. O tempo de execução também conta no intervalo observado; eventos atrasados são executados no próximo ciclo. O container não depende de tráfego HTTP. Um erro impede atualização do heartbeat e faz o health check detectar falha.

```bash
./scripts/wp.sh cron event list
./scripts/wp.sh cron event run --due-now
./scripts/wp.sh cron event schedule portfolio_cron_probe now
# Aguarde o próximo ciclo e confirme que o serviço executou o evento:
./scripts/wp.sh option get portfolio_cron_last_probe
docker compose logs --tail=30 cron
```

`wp cron test` testa o mecanismo HTTP desativado e não serve como prova do cron externo. O teste deve agendar um evento e observar sua execução pelo serviço.

## Versionamento

Após editar posts, páginas, menus ou configurações editoriais, execute `./scripts/db-export.sh`, revise `database/seeds/content.json` e acrescente os originais relevantes em `wp-content/uploads/portfolio/`. O snapshot não inclui usuários, posts privados/protegidos por senha ou na lixeira, comentários, revisões, sessões ou opções privadas de plugins. A filtragem não identifica dados privados escritos no corpo de posts ou em metadados arbitrários: a revisão humana continua necessária. Autores são atribuídos ao administrador do ambiente de destino. Mantenha IDs e caminhos das mídias coerentes. Miniaturas descartáveis podem ser regeneradas com `wp media regenerate --yes`.

Atualizações de segurança têm prioridade: consulte releases oficiais, altere versões/digests, reconstrua, confira checksums e rode os testes. Atualizações de recursos ou versões principais exigem backup e validação prévia em ambiente descartável. Atualizações automáticas são desativadas para impedir divergência do manifesto; isso exige manutenção ativa, inclusive antes de publicar.

Fontes: [WordPress](https://wordpress.org/download/), [LiteSpeed Cache](https://wordpress.org/plugins/litespeed-cache/), [All-In-One Security](https://wordpress.org/plugins/all-in-one-wp-security-and-firewall/), [Two Factor](https://wordpress.org/plugins/two-factor/).

