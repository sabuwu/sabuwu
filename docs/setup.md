# Execução local

Requisitos: Git, Docker Engine com Compose ou Podman com um provedor Compose, Bash, curl e Python 3 para os scripts no host. PHP no host é opcional. Não precisa instalar WordPress, MariaDB ou WP-CLI globalmente.

```bash
git clone <repositorio>
cd <repositorio>
cp .env.example .env
docker compose up -d
./scripts/healthcheck.sh
```

A primeira execução constrói a imagem, gera senhas e salts aleatórios, preenche o `.env` ignorado pelo Git, cria o banco, copia o Core oficial para `app/wordpress/`, instala os plugins nas versões do manifesto, cria o administrador e restaura o conteúdo editorial versionado. Precisa de Internet para imagens, WP-CLI e plugins. Execuções seguintes preservam o conteúdo e não recriam usuários. Um marcador de instalação nova mantém o import do seed pendente até ele concluir; falhas de download ou import podem ser retomadas sem sobrescrever um banco que já existia.

Site: <http://localhost:8088>. Login: <http://localhost:8088/wp-login.php>. Administração: <http://localhost:8088/wp-admin/>. O usuário inicial é `portfolio_owner`; consulte `WORDPRESS_ADMIN_PASSWORD` no `.env` local. Não publique esse arquivo nem cole sua senha em logs.

O serviço `bootstrap` termina com código 0 após preparar os segredos; isso é esperado. Os serviços `database`, `wordpress` e `cron` devem ficar saudáveis. `wp-cli` é uma ferramenta sob demanda.

## Configuração

Edite `.env` **antes** de iniciar. Senhas vazias são preenchidas com 32 bytes aleatórios. Senhas fornecidas devem ter pelo menos 20 caracteres e não podem ser placeholders. `.env.example` não contém credenciais utilizáveis. Os arquivos privados nos volumes são derivados do `.env`; o volume separado de root do MariaDB não é montado no WordPress.

Para mudar a porta, altere **ambos** `WORDPRESS_PORT` e `WORDPRESS_URL`. `WORDPRESS_BIND=127.0.0.1` mantém o acesso no host local. Se seu usuário não for UID/GID 1000, ajuste `APP_UID` e `APP_GID` conforme `id -u` e `id -g` antes do build. Não troque senhas de um banco já inicializado apenas editando `.env`: o MariaDB não altera contas existentes automaticamente.

```bash
docker compose up -d --build
docker compose ps
docker compose logs --tail=100 wordpress database cron
docker compose down
```

`down` preserva os volumes e os arquivos locais. Para um reset completo, faça backup, pare os serviços e execute `docker compose down --volumes`; isso **apaga o banco e os volumes de segredos deste projeto**. Os arquivos em `app/wordpress/` ficam preservados. O próximo `up` cria outro banco e restaura o seed. A senha do administrador volta ao valor do `.env` apenas nessa nova instalação.

## Podman

```bash
podman compose up -d --build
COMPOSE_ENGINE=podman ./scripts/healthcheck.sh
COMPOSE_ENGINE=podman ./scripts/wp.sh plugin list
podman compose down
```

Use uma versão recente de Podman e um provedor que respeite `depends_on.condition`, perfis e health checks. O manifesto usa recursos Compose padrão; a validação do provedor disponível é registrada em `docs/validation.md`. As montagens têm `:z` para permitir compartilhamento em hosts SELinux.

Em Podman rootless, os IDs dentro do container são mapeados para IDs do host. Verifique a escrita em `app/wordpress/` e no `.env`. Prefira configurar o mapeamento de usuário do seu provedor (`keep-id`) ou usar um worktree dedicado com permissões ajustadas por `podman unshare`; não use `chmod 777` e não altere recursivamente a propriedade de um repositório com trabalho em andamento.

