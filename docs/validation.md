# Validação local

Data: 10/10/2026. Plataforma: Linux amd64, Docker 29.8.2, Docker Compose 5.6.0; Podman 6.1.3 com o mesmo provedor Compose.

| Verificação | Resultado registrado |
| --- | --- |
| Sintaxe PHP e Bash do código próprio | OK |
| `docker compose config --quiet` | OK |
| `podman compose config --quiet` | OK |
| README e GIFs anteriores | OK — preservados byte por byte |
| HTTP principal: `/`, `/wp-login.php`, `/wp-admin/` | OK — 200, 200 e 302; formulário identificado |
| Login administrativo HTTP e perfil | OK — cookie autenticado e suporte Two Factor |
| REST API | OK — resposta JSON com namespace `wp/v2` na imagem final |
| WordPress → banco / credencial administrativa | OK — `SELECT 1`, senha privada e permissões dedicadas |
| Cron externo sem tráfego HTTP | OK — teste final agendado 20:37:46 UTC; executado 20:39:09 UTC pelo serviço, sem consumo manual |
| Health checks finais | OK — WordPress, MariaDB e cron saudáveis na imagem final |
| Logs finais da aplicação e cron | OK — sem PHP Fatal Error ou erro de conexão com o banco |
| Core / PHP / MariaDB / WP-CLI | 7.1.3 / 8.3.35 / 11.4.13 / 2.12.0 |
| Core checksums | OK — consulta oficial via WP-CLI; template Docker dispensável removido |
| Plugins checksums | OK — verificação final dos oito plugins oficiais via WP-CLI, sem itens pulados |
| Proteção HTTP de config, XML-RPC e PHP em uploads | OK — HTTP 403 |
| Headers de segurança | OK — nosniff, Referrer-Policy, Permissions-Policy |
| Containers / banco / arquivos | OK — aplicação/cron UID 1000, sem capabilities/privileged; MariaDB UID 999; rede interna/sem publicação; `.env` 600; sem arquivos/diretórios 777 |
| Produção e proxy HTTPS | OK para flags — debug desativado, file mods bloqueados, cabeçalho ignorado até proxy explicitamente confiável; sem TLS local |
| Snapshot editorial | OK — export/import de posts, páginas, menus, opções e URLs PHP serializadas; usuários e opção privada preservados; posts privados/protegidos e metadados de segredo excluídos |
| Reconstrução limpa | OK — cópia de 48 arquivos candidatos ao Git, sem Core/plugins/volumes/secrets prévios; banco/usuário criados automaticamente; seed e tema restaurados; novo administrador autenticado |
| Backup e restore operacional | OK — SQL + wp-content, arquivo privado; conteúdo/configuração e SHA-256 do CSS restaurados |
| Bloqueio real de login | OK — cinco POSTs inválidos geraram bloqueio temporário por IP; registro verificado e bloqueio de teste removido |
| Separação de plugins em produção | OK — Query Monitor inativo e seu symlink removido; desenvolvimento reativado e symlink restaurado |

O README já tinha mudanças de caminhos de imagens e os GIFs já estavam movidos antes desta tarefa. Essas mudanças do autor foram preservadas. SHA-256 do README no início: `448d261ed2383f69b3818487815869d00cb13b9ba34c602ecbe56cccbabea978`.

Os testes em execução usam `./scripts/healthcheck.sh` e `./tests/integration.sh`. A validação de Podman indicada acima cobre o manifesto; não representa teste de execução dos containers nesse engine.


O host apresentou pressão de I/O de 50–93%. O primeiro bootstrap MariaDB local ultrapassou seu prazo interno e foi concluído com as funções oficiais; os bancos novos dos testes seguintes inicializaram sozinhos. A primeira tolerância de saúde do WordPress foi insuficiente para downloads frios neste disco; foi ampliada para 30 minutos, mantendo HTTP/SQL reais como critérios. Uma falha transitória de DNS do cron foi registrada e se recuperou no ciclo seguinte; login HTTP principal com timeout de 60 segundos foi repetido e passou. Falhas transitórias de rede em checksums foram repetidas até verificar o Core e todos os oito plugins, sem desativar TLS.

O banco agora só fica saudável após `SELECT 1` por TCP com a conta dedicada e o banco do WordPress. Query Monitor não define sua classe de banco em CLI; por isso a limpeza de produção também remove exclusivamente o symlink que aponta para esse plugin, preservando outros drop-ins.

O método inicial de instalação por prompt ecoou a senha nos logs locais. Foi substituído pela API nativa sem eco; a senha foi rotacionada e o container com esse log foi removido. A auditoria dos arquivos candidatos ao Git incluiu as credenciais novas e a senha retirada, sem encontrar esses valores.

A listagem final confirmou os oito plugins ativos, mas registrou um aviso transitório na consulta automática de atualizações. Uma consulta posterior pela API HTTP do próprio WordPress à API oficial de plugins passou com verificação TLS habilitada. Isso valida a conectividade; não afirma que as versões fixadas serão sempre as mais recentes.

O ambiente temporário `sabuwu-repro-test`, seus containers, volumes e arquivos privados de teste foram removidos. O projeto principal permanece em execução; não houve commit, push nem staging.

## Limpeza de plugins e organização — 13/10/2026

Os registros acima descrevem o ambiente de 10/10/2026, antes da limpeza. O ambiente local agora mantém All-In-One Security, Two Factor, Yoast SEO e Query Monitor em desenvolvimento. LiteSpeed Cache e UpdraftPlus foram retirados; Akismet e WP Mail SMTP foram retirados por estarem sem configuração e permanecem como integrações condicionais no manifesto. `twentytwentyfive` permanece como tema reserva; os outros dois temas padrão foram retirados.

Um backup privado anterior às remoções foi guardado em `backups/20261013-190730/`, incluindo banco, wp-content, configuração e artefatos dos plugins. A comparação dos dados de nove tabelas editoriais e de usuários confirmou igualdade com o backup após a limpeza e os testes. Os eventos dos plugins retirados e suas pastas geradas foram limpos; opções salvas dos plugins foram preservadas.

| Verificação | Resultado |
| --- | --- |
| Sintaxe PHP/Bash e `docker compose config --quiet` | OK |
| Testes existentes do tema | OK — 38 verificações; fixtures removidas e transação revertida |
| Teste existente de integração | OK — proteção HTTP, configuração, separação de segredos e evento executado pelo cron externo |
| Instalador com WP-CLI simulado e predicados PHP | OK — nove testes de escopos, integrações condicionais e preservação de drop-ins externos |
| Entrypoint em árvore temporária | OK — instalação limpa, preservação de arquivos existentes e Core já presente |
| Referência HTML e perfil GitHub | OK — HTML movido sem alterações; conteúdo anterior do README e GIFs preservados |
| Documentação e pacote do tema | OK — links locais válidos e ZIP sincronizado com os 23 arquivos do tema |
| Imagem final e reinício | OK — scripts e manifesto sincronizados por SHA-256; WordPress, banco e cron saudáveis; quatro plugins ativos e tema Sabuwu preservado |
| HTTP após o reinício | OK — home, blog, projetos, perfil, experiência, contato, login e busca retornaram 200 |

Os testes temporários do instalador usam PHP 8.5 no host e um substituto do WP-CLI; não validam hooks reais. Os testes do tema e da integração foram executados no WordPress local com PHP 8.3. A cópia do Core continua usando `cp -rn`, que preserva arquivos existentes, mas falha se encontrar um symlink preexistente quebrado no lugar de um diretório de plugin.
