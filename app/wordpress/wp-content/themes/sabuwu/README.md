# Sabuwu — tema de portfólio WordPress

**Versão 2.0.0.** Tema nativo que converte o design de `portifolio_design.html` em um portfólio editável pelo WordPress. Inclui página inicial, modelo de página Portfólio, projetos com estudo de caso e templates para páginas, posts, busca, comentários e erro 404. Dispensa plugins de construção de páginas.

Requisitos: WordPress 6.4 ou superior e PHP 8.0 ou superior.

1. No WordPress, abra **Aparência → Temas → Adicionar novo → Enviar tema**.
2. Selecione `sabuwu-wordpress.zip`, instale e ative **Sabuwu**. O arquivo deve conter a pasta `sabuwu/` na raiz.
3. Como alternativa, copie esta pasta para `wp-content/themes/sabuwu/` e ative o tema pelo painel.
4. Abra **Aparência → Personalizar → Portfólio Sabuwu**, edite os textos e contatos e clique em **Publicar**. Em **SEO do portfólio**, ajuste o título da aba e a descrição para os buscadores.

O portfólio aparece automaticamente na página inicial. Para usar outra página, selecione o modelo **Portfólio** no editor de páginas. Ambos usam o mesmo conteúdo do Personalizador. Um menu atribuído à localização **Navegação principal** substitui os links padrão das seções.

Para adicionar trabalhos, abra **Projetos → Adicionar projeto**. O título e o resumo aparecem no portfólio; o conteúdo do editor aparece no estudo de caso. Preencha **Dados do projeto no portfólio** com tecnologias separadas por vírgulas, links opcionais e até três métricas com valor e descrição. Sem URL de estudo de caso, o tema usa a página do próprio projeto. Em **Atributos do projeto → Ordem**, valores menores aparecem primeiro.

Enquanto nenhum projeto estiver publicado, aparecem os três exemplos do design original. O primeiro projeto publicado substitui todos os exemplos. Desmarque **Exibir projetos de demonstração** no Personalizador para ocultá-los antes disso. Os números dos exemplos foram copiados do HTML: substitua-os por resultados reais e verificáveis.

Informe o currículo em **Portfólio Sabuwu → Contatos e links**: envie um PDF à Biblioteca de mídia e cole a URL. Links vazios ficam ocultos. Links internos abrem na mesma aba; externos usam outra aba com `noopener noreferrer`.

Após atualizar um tema `sabuwu` já ativo, acesse o painel como administrador para atualizar as rotas. Se um projeto retornar 404, salve **Configurações → Links permanentes** uma vez.

Textos personalizados e projetos são armazenados no banco do WordPress; este ZIP contém o código do tema. No repositório original, o ambiente local fica em <http://localhost:8088/> e `./scripts/db-export.sh` inclui essas edições no snapshot editorial. Preserve também as mídias ao transferir o conteúdo entre instalações.
