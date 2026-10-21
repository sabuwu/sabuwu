# Tema Sabuwu

Tema nativo WordPress **2.3.0** para portfólio, currículo e blog, com conteúdo editável pelo painel. Requer WordPress 6.4 ou superior e PHP 8.0 ou superior, conforme o cabeçalho de `style.css`.

## Instalação

Envie o ZIP do tema em **Aparência → Temas → Adicionar novo → Enviar tema** e ative **Sabuwu**. Também é possível copiar esta pasta para `wp-content/themes/sabuwu/` de uma instalação existente. O tema funciona com APIs nativas do WordPress; seu layout e cadastro de projetos não exigem plugins adicionais.

O pacote contém somente o tema. Posts, páginas, projetos, mídia e configurações permanecem no banco e nos uploads de cada instalação. Faça backup antes de atualizar uma instalação existente.

Abra o painel como administrador após instalar ou atualizar. O tema cria as páginas **Projetos**, **Perfil**, **Experiência**, **Contato** e **Currículo** (`/resume/`) quando faltarem. Com uma página inicial estática e sem página de posts definida, também cria e configura **Blog**, preservando a página inicial selecionada. Para usar o portfólio como home, selecione uma página estática em **Configurações → Leitura**; o modelo **Portfólio** também pode ser atribuído a uma página. **Seus posts mais recentes** mostra a listagem do blog.

## Edição do conteúdo

1. Em **Aparência → Personalizar → Portfólio Sabuwu**, altere apresentação, disponibilidade, tecnologias, perfil, experiência, contatos e SEO. Publique as alterações para salvá-las.
2. Em **Projetos → Adicionar projeto**, informe título, resumo, conteúdo do estudo de caso e os campos de tecnologias, links e métricas. Projetos publicados aparecem no portfólio; **Ordem** define a sequência, com números menores primeiro.
3. Os projetos de demonstração aparecem enquanto não houver projetos publicados. Desative **Exibir projetos de demonstração** no Personalizador para ocultá-los.
4. Publique posts normalmente para atualizar o blog e as três publicações recentes na home do portfólio. Configure a página de posts em **Configurações → Leitura**.
5. Para substituir a navegação padrão, atribua um menu à localização **Navegação principal** em **Aparência → Menus**.

O currículo completo é servido pela página `/resume/`, com layout responsivo e impressão em A4. Os caminhos antigos `/resume.html`, `/curriculo.html` e `/curriculo/` redirecionam para ela. O botão de currículo da home aponta para essa página por padrão; para oferecer um PDF em vez da página, envie o arquivo pela biblioteca de mídia e cole a URL no Personalizador. Ajuste o idioma do conteúdo ao traduzir o portfólio. O tema integra título e descrição com Yoast SEO quando o plugin estiver ativo.

Se um estudo de caso retornar 404 após a ativação, abra **Configurações → Links permanentes** e salve uma vez para atualizar as rotas.

## Arquivos

- `functions.php`: integração WordPress, recursos, navegação e páginas iniciais.
- `inc/`: controles do Personalizador e cadastro de projetos.
- `template-parts/`: seções do portfólio e publicações recentes.
- `front-page.php`, `page-*.php`, `index.php` e `single-sabuwu_project.php`: páginas e listagens.
- `style.css`: identificação e estilos do tema.
- `assets/`: estilos do editor, currículo e JavaScript.

Edite os arquivos desta pasta ao desenvolver o tema; alterações no Core WordPress são independentes do pacote.
