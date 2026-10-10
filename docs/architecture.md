# Arquitetura

```text
Browser → 127.0.0.1:8088 → WordPress / Apache:8080 → MariaDB:3306
                              ↑                        ↑
                         cron / WP-CLI ────────────────┘
```

| Serviço | Responsabilidade |
| --- | --- |
| `bootstrap` | Etapa única: credenciais fortes a partir do `.env`, sem senha pública padrão. |
| `database` | MariaDB 11.4 com `utf8mb4`, usuário dedicado e health check `SELECT 1` por TCP nessa conta e banco. |
| `wordpress` | WordPress 7.1.3, PHP 8.3 e Apache; instalação automática e HTTP. |
| `cron` | Executa `wp cron event run --due-now` a cada 60 segundos, sem depender de visitas. |
| `wp-cli` | Mesmo PHP e código da aplicação; ferramenta sem porta, perfil `tools`. |

O banco pertence somente à rede `backend`, marcada `internal: true`, sem portas publicadas. A aplicação também usa `frontend` para baixar dependências e executar integrações externas. WordPress, cron e WP-CLI executam como `www-data`, com UID/GID configuráveis, `cap_drop: ALL` e `no-new-privileges`. Apache escuta em 8080 dentro do container. MariaDB usa o entrypoint oficial para preparar o volume e depois reduz privilégios para seu usuário de serviço.

`database_data` persiste o banco. `app_secrets` persiste as credenciais da aplicação; `db_secrets` contém a senha root, acessível somente pelo bootstrap e MariaDB. O `.env` permanece no host e só é montado no bootstrap. Os volumes não substituem o Git.

`app/wordpress/` é um bind mount: o Core e dependências oficiais existem localmente, mas são reconstruídos a partir de versões fixadas; `wp-config.php`, tema próprio, mu-plugin e plugins com prefixo `sabuwu-` são versionáveis. Imagens editoriais necessárias devem ficar em `uploads/portfolio/`; outros uploads permanecem locais. Os GIFs anteriores continuam em `assets/images/` e seus caminhos no README foram preservados.

`database/seeds/content.json` reconstrói posts, páginas, taxonomias, menus, metadados editoriais e uma lista explícita de opções. Usuários, comentários, sessões e configuração privada de plugins não fazem parte do snapshot. Backups completos ficam em `backups/`, ignorado pelo Git.

Health checks verificam banco, integração via WordPress e HTML de login. O cron registra um heartbeat somente após uma execução sem erros. Logs dos containers têm limite de 10 MB por arquivo e três arquivos.

