# Portfólio WordPress

O tema nativo **Sabuwu 2.0.0** reproduz o design de `app/portifolio_design.html` e permite editar o conteúdo pelo WordPress, sem construtor de páginas. O HTML original permanece como referência; o site usa os arquivos PHP, CSS e JavaScript do tema `sabuwu`.

No ambiente Docker deste projeto, o tema já está ativo. Com os serviços iniciados, acesse <http://localhost:8088/> e edite o site em <http://localhost:8088/wp-admin/>. Para preparar o ambiente, siga [Execução local](setup.md).

1. Abra **Aparência → Personalizar → Portfólio Sabuwu** para alterar marca, disponibilidade, apresentação, tecnologias, perfil, experiência e contato. Em **SEO do portfólio**, edite o título da aba e a descrição para os buscadores. Clique em **Publicar** para salvar.
2. Em **Contatos e links**, informe GitHub, Discord, e-mail e currículo. Para o currículo, envie o PDF em **Mídia → Adicionar nova** e cole sua URL. Deixe a URL vazia para ocultar o botão.
3. Em **Projetos → Adicionar projeto**, preencha o título e o **Resumo**, usado como descrição no portfólio. O conteúdo do editor compõe a página do estudo de caso. Sem resumo, a descrição é extraída do conteúdo.
4. No bloco **Dados do projeto no portfólio**, separe tecnologias por vírgulas, informe os links disponíveis e até três métricas com valor e descrição. Sem URL de estudo de caso, o botão abre a página do próprio projeto. Links vazios ficam ocultos.
5. Defina a **Ordem** em **Atributos do projeto**: números menores aparecem primeiro. Projetos com a mesma ordem usam a data de publicação, do mais recente para o mais antigo. Publique o projeto para exibi-lo na página inicial.

Os três projetos e as métricas do HTML são exemplos de demonstração. Eles aparecem enquanto não houver projetos publicados e são substituídos automaticamente ao publicar o primeiro projeto. Para ocultá-los antes disso, desmarque **Exibir projetos de demonstração** em **Portfólio Sabuwu → Projetos**. Use suas próprias entregas e números verificáveis.

A página inicial exibe o portfólio automaticamente por `front-page.php`. Para exibi-lo em outra URL, crie uma página em **Páginas → Adicionar nova**, escolha o modelo **Portfólio** e publique. O modelo usa o mesmo conteúdo configurado no Personalizador. As demais páginas, posts, buscas e estudos de caso usam os templates normais do tema.

O menu padrão aponta para as seções da página. Para substituí-lo, atribua um menu à localização **Navegação principal** em **Aparência → Menus**. Links internos abrem na mesma aba; destinos externos abrem em outra aba com `rel="noopener noreferrer"`.

Para instalar em outro WordPress, envie [dist/sabuwu-wordpress.zip](../dist/sabuwu-wordpress.zip) em **Aparência → Temas → Adicionar novo → Enviar tema** e ative **Sabuwu**. O ZIP contém a pasta `sabuwu/`; também é possível copiá-la diretamente para `wp-content/themes/`. O pacote instala o tema; as edições e os projetos ficam no banco da instalação.

Ao atualizar uma instalação que já usa `sabuwu`, abra o painel como administrador para atualizar as rotas dos projetos. Se alguma página de projeto retornar 404, abra **Configurações → Links permanentes** e clique em **Salvar alterações** uma vez.

O idioma da página do portfólio é `pt-BR` por padrão. Ao traduzir os textos, ajuste **Idioma do conteúdo** no Personalizador para que leitores de tela usem a pronúncia correta. Páginas e posts comuns seguem o idioma configurado no WordPress.

Para verificar cadastro, ordenação, permissões, REST e controles do Personalizador no ambiente local:

```bash
docker compose exec -T wordpress wp --skip-plugins eval-file - < tests/portfolio.php
```

O teste exige ambiente `local` ou `development` e tabelas InnoDB. Usa apenas projetos temporários em uma transação revertida ao final, preservando o conteúdo existente.

Depois de editar o conteúdo local, exporte o snapshot editorial:

```bash
./scripts/db-export.sh
```

O arquivo `database/seeds/content.json` inclui as configurações do tema, os projetos e seus metadados. Revise o conteúdo antes de versioná-lo; arquivos de mídia precisam ser preservados separadamente conforme [WordPress e conteúdo](wordpress.md).
