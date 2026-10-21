# WordPress e conteúdo

Core: **7.1.3**. PHP: **8.3**. WP-CLI: **2.12.0**, com SHA-256 verificado no build. As imagens base WordPress e MariaDB são fixadas por digest. Não edite `wp-admin`, `wp-includes` ou arquivos do Core para adicionar funcionalidades.

O tema `sabuwu` e `mu-plugins/portfolio-hardening.php` são código próprio. O tema usa APIs nativas, sem construtor externo. O mu-plugin desativa pingbacks/XML-RPC, evita revelar erros detalhados de login, controla envio local de email e fornece um evento de diagnóstico de cron. Gutenberg e REST API continuam disponíveis.

O tema Sabuwu 2.3.0 integra o layout da [referência HTML](reference/portfolio.html), com textos editáveis no Personalizador, currículo em `/resume/` e cadastro nativo de projetos. Consulte [Portfólio WordPress](portfolio.md) para editar o conteúdo ou instalar o pacote em outra instalação.

## Plugins

| Plugin | Versão fixada | Critério de instalação e ativação | Motivo / configuração |
| --- | --- | --- | --- |
| All-In-One Security | 5.4.10 | Base, todos os ambientes | Firewall e bloqueio de login. |
| Two Factor | 0.17.0 | Base, todos os ambientes | Suporte TOTP; cadastro do dispositivo é individual. |
| Yoast SEO | 28.6 | Base, todos os ambientes | Títulos, sitemap e Open Graph. |
| Query Monitor | 4.0.7 | Somente `local` e `development` | Diagnóstico; desativado nos demais ambientes. |
| Akismet | 5.7.2 | Condicional: chave no ambiente ou configuração salva no painel | Antispam de comentários; validação remota exige chave válida. |
| WP Mail SMTP | 4.10.2 | Condicional: `SMTP_HOST` ou transporte configurado no painel | Integração de email; configurações já salvas no painel são preservadas. |

Versões e escopos estão em `config/plugins.lock`: `all` para a base, `development` para diagnóstico, `akismet` e `smtp` para as integrações condicionais. O instalador baixa os arquivos versionados de `downloads.wordpress.org` via WP-CLI e não atualiza automaticamente para a versão mais recente. Configuração salva no painel pode habilitar uma integração mesmo sem variáveis correspondentes no ambiente.

Comentários existentes e suas configurações são mantidos. Para usar antispam em posts com comentários abertos, configure uma chave válida do Akismet pelo painel ou em `AKISMET_API_KEY`; apenas instalar o plugin não comprova funcionamento. Sem essa configuração, ele não faz parte da instalação base.

Escolheu-se All-In-One Security como alternativa à preferência por Wordfence para permitir setup local sem cadastrar licença/conta. Não instalar outro firewall nem outro limitador de login. TOTP fica disponível em Usuários → Seu perfil, pelo Two Factor. O módulo TOTP interno do All-In-One Security é desabilitado para evitar duplicação com Two Factor. A proteção local usa cinco tentativas, janela de 15 minutos e bloqueio temporário de 15 minutos.

LiteSpeed Cache foi retirado: o servidor é Apache e não usa suas otimizações. OPcache do PHP permanece habilitado. UpdraftPlus também foi retirado, pois o backup operacional usa `scripts/backup.sh` e `scripts/restore.sh`, descritos em [Comandos](commands.md). Não se adicionou Redis sem necessidade medida.

O site local usa `blog_public=0`; no lançamento público, habilite indexação, revise título/descrição/SEO e configure HTTPS. Em ambientes diferentes de produção, o mu-plugin bloqueia emails quando `SMTP_HOST` está vazio, mesmo que o WP Mail SMTP tenha transporte salvo no painel. Essa configuração salva mantém o plugin instalado, mas não libera envio local. SMTP e Akismet exigem credenciais e teste externo para confirmar funcionamento.

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

Fontes: [WordPress](https://wordpress.org/download/), [All-In-One Security](https://wordpress.org/plugins/all-in-one-wp-security-and-firewall/), [Two Factor](https://wordpress.org/plugins/two-factor/).
