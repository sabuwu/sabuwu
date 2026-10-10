# Comandos

```bash
docker compose up -d --build
docker compose down
docker compose ps
docker compose logs --tail=100 -f wordpress database cron
docker compose restart wordpress
./scripts/wp.sh plugin list
./scripts/wp.sh theme list
./scripts/wp.sh core version
./scripts/install-plugins.sh
./scripts/wp.sh cron event list
./scripts/wp.sh cron event run --due-now
./scripts/healthcheck.sh
```

Use `COMPOSE_ENGINE=podman` antes dos scripts para selecionar Podman. `scripts/compose.sh` também aceita comandos Compose e detecta o engine disponível. WP-CLI requer a aplicação inicializada.

`wp.sh` usa o container WordPress em execução, evitando criar um container para cada comando. Para manutenção com esse serviço parado, use `docker compose run --rm -T --no-deps wp-cli <comando>`; o banco deve estar disponível.

## Conteúdo versionado

```bash
./scripts/db-export.sh
# Revise database/seeds/content.json e os arquivos de mídia.
./scripts/db-import.sh database/seeds/content.json
# Em automação autorizada: acrescente --yes como segundo argumento.
```

O export usa as APIs do WordPress e produz um snapshot JSON portátil, equivalente editorial de um dump filtrado. O import substitui as tabelas editoriais, preserva usuários e segredos e ajusta URLs serializadas. Teste em um ambiente descartável antes de importar sobre conteúdo importante. O seed é restaurado automaticamente em um banco novo; depois de atualizar o seed, reconstrua a imagem para incorporá-lo.

## Backup e restauração privados

```bash
./scripts/backup.sh
./scripts/restore.sh backups/<timestamp>
```

O backup contém SQL completo e `wp-content`; **contém hashes de usuários e configurações privadas do banco**. Não inclui `.env`, salts nem volumes de segredos. É privado, local e ignorado. Não trate esse arquivo como snapshot para commit. A restauração interrompe WordPress e cron; usa confirmação explícita (`--yes` em automação autorizada). Mantenha a URL e os segredos originais do ambiente ou planeje a migração antes de restaurar.

## Diagnóstico e integridade

```bash
./scripts/wp.sh core verify-checksums
./scripts/wp.sh plugin verify-checksums --all --strict
./scripts/wp.sh eval 'global $wpdb; echo $wpdb->get_var("SELECT 1");'
docker compose exec wordpress id
docker compose exec cron cat /tmp/portfolio-cron-heartbeat
curl -I http://localhost:8088/wp-login.php
git status --short
git check-ignore .env
```

