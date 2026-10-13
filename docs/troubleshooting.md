# Diagnóstico

```bash
docker compose ps -a
docker compose logs --tail=100 bootstrap
docker compose logs --tail=100 database
docker compose logs --tail=100 wordpress
docker compose logs --tail=100 cron
curl -I http://localhost:8088/
curl -s http://localhost:8088/wp-login.php
./scripts/healthcheck.sh
```

**Porta ocupada:** altere `WORDPRESS_PORT` e `WORDPRESS_URL` em conjunto, depois recrie o serviço com `docker compose up -d`. Não encerre containers de outros projetos. Links armazenados podem ser ajustados com `wp search-replace` após backup.

**Permission denied:** compare `id -u`, `id -g`, `APP_UID`/`APP_GID` e `docker compose exec wordpress id`. Ajuste os IDs e reconstrua a imagem. Arquivos novos são criados pelo mesmo UID do bind mount; não use permissões 777. Em Podman rootless verifique o mapeamento de IDs e SELinux, conforme `setup.md`.

**Banco não conecta:** confira health check e logs do MariaDB; `DB_HOST` deve ser `database:3306`. As credenciais iniciais ficam no `.env` e nos volumes privados. Editar senha no `.env` não muda a senha de uma conta já criada. Restaure a configuração anterior ou altere a conta de forma administrativa, sem registrar a senha na linha de comando.

**Inicialização demorada:** os primeiros downloads dos plugins podem levar minutos. Confira logs antes de reiniciar. Falha de rede deve ser corrigida e o serviço reiniciado; não marque a aplicação funcional somente pelo estado `running`. O health check exige integração com banco e formulário de login real.

O banco tem 300 segundos de tolerância inicial, e WordPress 1800, para inicialização/downloads em hosts lentos. Essa tolerância de até 30 minutos evita que um primeiro setup demorado deixe o cron sem iniciar; o estado saudável continua exigindo HTTP e banco reais. O estado `starting` não comprova funcionamento. MariaDB pode avisar que `memory.pressure` não é gravável ou que `io_uring` está desativado pelo host; ele usa o mecanismo alternativo `libaio`. Confirme prontidão e consultas antes de tratar esses avisos como falha. Não amplie privilégios dos containers para silenciá-los.

**Cron unhealthy:** confira os logs, `CRON_INTERVAL` (10–3600 segundos), eventos devidos e `/tmp/portfolio-cron-heartbeat`. Execute `wp cron event run --due-now` para localizar erro do plugin. Não reative cron por visitas como solução silenciosa.

**Login bloqueado após teste:** aguarde o prazo configurado ou remova o bloqueio local com WP-CLI conforme a tabela All-In-One Security após confirmar o IP. Não desative a proteção permanente para resolver uma tentativa isolada. Testes devem usar nomes descartáveis, nunca a conta administrativa real.

**SMTP/Akismet ausentes ou sem funcionamento:** são integrações condicionais. Akismet exige `AKISMET_API_KEY` ou chave salva no painel; WP Mail SMTP exige `SMTP_HOST` ou transporte configurado no painel. Sem configuração, não são instalados como parte da base. Configurações existentes no painel são preservadas. Para comentários abertos, forneça uma chave válida do Akismet e teste a integração.

Em ambientes diferentes de produção, o mu-plugin bloqueia emails quando `SMTP_HOST` está vazio, mesmo com transporte salvo no painel. A presença do WP Mail SMTP não libera envio local. Após alterar variáveis, recrie os serviços com `docker compose up -d`; `restart` sozinho não reaplica variáveis modificadas. Execute `./scripts/install-plugins.sh` para reaplicar os critérios após alterar a configuração de uma integração e verifique `./scripts/wp.sh plugin list`.

**LiteSpeed Cache ou UpdraftPlus ausentes:** foram retirados do manifesto. O servidor Apache não usa as otimizações do LiteSpeed Cache; OPcache segue habilitado. Backups operacionais são feitos por `./scripts/backup.sh` e restaurados por `./scripts/restore.sh`, conforme [Comandos](commands.md).

**Checksums:** `wp-config.php` e código próprio são configurações esperadas; o Core deve passar `wp core verify-checksums`. Se um plugin divergir, preserve qualquer trabalho próprio e reinstale somente a dependência oficial afetada com a versão do manifesto. Regras geradas em `.htaccess`, logs e caches não devem entrar no commit.

**Import de conteúdo:** o snapshot substitui conteúdo editorial. Faça backup antes. Corrija referência de mídia se o original ficou somente em uploads locais ignorados. O snapshot não restaura usuários nem configurações privadas de plugins.

