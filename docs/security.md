# Segurança

## Implementado

| Camada | Configuração |
| --- | --- |
| Secrets | `.env` ignorado e modo 600; senhas/salts gerados com `random_bytes`; volumes privados; root do banco separado. |
| Banco | Usuário WordPress dedicado, `utf8mb4`, rede interna, nenhuma porta no host. |
| Containers | Imagens oficiais com digest, sem modo privilegiado/socket Docker, WordPress/cron/CLI sem root e sem capabilities. |
| WordPress | Editor PHP desativado; modificações pelo painel desativadas em produção; atualizações controladas pelo manifesto. |
| Login | All-In-One Security: bloqueio temporário após cinco tentativas; username inicial não é `admin`. |
| 2FA | Plugin Two Factor ativo; TOTP disponível, ainda depende de inscrição individual. |
| Firewall | Regras básicas All-In-One Security em `.htaccess`; Apache bloqueia arquivos sensíveis/XML-RPC. Assinaturas agressivas de query string ficam desativadas localmente para preservar buscas e desenvolvimento. |
| Uploads | PHP, PHAR, PHTML, CGI e SH bloqueados por Apache; sem alteração da lista padrão de uploads. |
| PHP | Erros fora da resposta HTTP, logs do servidor e OPcache. |
| HTTP | `nosniff`, Referrer-Policy e Permissions-Policy; sem política CSP incompatível com o painel. |
| Cron | Serviço externo não-root e heartbeat; execução independente de acessos HTTP. |
| Logs | Rotação de logs dos containers: três arquivos de 10 MB. Debug local em `/tmp/wordpress-debug.log`. |
| Backup | Snapshot editorial versionável e backup privado SQL + wp-content por `scripts/backup.sh`; restauração por `scripts/restore.sh`. |
| Código próprio | APIs WordPress; escape de dados no tema; import CLI sem endpoint público e valores SQL via `$wpdb->insert`. |

O serviço bootstrap usa root por poucos segundos para ajustar arquivos privados. MariaDB usa seu entrypoint oficial e roda o processo do banco como `mysql`. Nenhum container é privilegiado. Acesso ao banco root não é fornecido à aplicação.

## Recomendado para produção

Exigir inscrição TOTP para todos os administradores; testar recuperação antes de obrigar 2FA. Configurar destino externo e retenção dos backups gerados pelo script e testar restauração periódica. Para posts com comentários abertos, configurar uma chave válida do Akismet; para email, configurar o transporte e verificar entrega. Essas integrações são condicionais e preservam configurações salvas no painel. Testar regras/scanner do firewall com tráfego real e configurar monitoramento. O suporte instalado não prova eficácia completa nem substitui controles no proxy.

Em ambientes diferentes de produção, a proteção local bloqueia email quando `SMTP_HOST` está vazio, inclusive com WP Mail SMTP configurado pelo painel. Query Monitor permanece somente em `local` e `development`.

Terminar TLS em proxy confiável e publicar apenas esse proxy. Definir `WORDPRESS_URL=https://...`, `WORDPRESS_FORCE_SSL_ADMIN=true`, `WP_ENVIRONMENT_TYPE=production` e `WORDPRESS_DEBUG=false`. `TRUST_REVERSE_PROXY=true` só deve ser usado quando o WordPress não estiver diretamente acessível e o proxy sobrescrever `X-Forwarded-Proto`; o projeto não confia nesses cabeçalhos por padrão. Revisar mapeamento de IP do proxy antes de usar bloqueios por IP.

Adicionar HSTS somente com HTTPS validado. Construir CSP a partir dos recursos realmente usados, começando por `Content-Security-Policy-Report-Only`. Aplicar rate limiting no proxy a login e endpoints sujeitos a abuso sem bloquear indiscriminadamente a REST API. Não há HSTS, TLS, Redis nem proxy configurados no ambiente local.

Não usar o `.env` local em produção. Provisionar credenciais próprias, atualizar imagens/plugins/Core e auditar configurações. O modo produção impede instalação de plugins pelo painel HTTP; o instalador local via WP-CLI continua autorizado a aplicar o manifesto. O setup local não é uma implantação de produção automatizada.

