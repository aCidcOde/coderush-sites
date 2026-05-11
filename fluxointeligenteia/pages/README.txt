Páginas internas do site (portal de notícias e artigos).

Pasta blog/ (URL base pages/blog/):

- blog/blog.html — listagem / portal
- blog/blog-post.html — artigo (?post=slug)

Assets compartilhados: a partir de pages/blog/ use prefixo ../../ (CSS/JS/imagens
em /assets na raiz do projeto).

A landing principal permanece em /index.html na raiz do projeto.

Header e footer globais vêm de ../../assets/js/site-layout.js (montados nos divs
#flux-slot-header e #flux-slot-footer). body data-site="blog" nessas páginas.
