# Guideline Completa - Automacao Semanal de Post em Alta

## Status
- Plano aprovado
- Ativacao automatica: **agendada e ativa (quarta, 09:00, America/Sao_Paulo)**
- Branch de publicacao: `main`
- Idioma: `pt-BR`
- Fuso de referencia editorial: `America/Sao_Paulo`

## Resumo
Implementar uma rotina recorrente que, toda quarta-feira as 09:00, pesquisa um tema em alta com rodizio de foco (`IA -> PHP -> Tecnologia`), cria e publica 1 post novo completo no site SVD, atualiza listagens e sitemap, valida tudo e somente entao faz `commit + push` em `main`.

## Escopo Fechado
1. Frequencia: semanal, quarta-feira, 09:00.
2. Fluxo Git: commit + push automatico em `main`.
3. Estrategia de tema: rodizio semanal `IA`, `PHP`, `Tecnologia`.
4. Estrategia de imagem: banco de imagens com licenca aberta + layout padrao do blog.
5. Gate de qualidade: se qualquer validacao falhar, abortar publicacao (sem commit/push).

## Contrato Tecnico
### Contrato do post gerado
- `date`: data da execucao
- `slug`: kebab-case unico
- `title`: titulo SEO em pt-BR
- `description`: meta description de 140 a 160 chars
- `cover_image`: `/Users/acidcode/data/svd_site/index_svd_files/posts/{slug}.jpg`
- `path`: `/Users/acidcode/data/svd_site/YYYY/MM/DD/{slug}/index.php`
- `focus`: um de `ia|php|tecnologia`
- `sources`: minimo de 2 links externos confiaveis

### Estrutura obrigatoria do conteudo
1. Abertura contextual.
2. Secao tecnica principal do tema da semana.
3. Paragrafo no meio do texto sobre a importancia de software sob medida com IA.
4. Secao pratica para negocio/operacao.
5. Fontes oficiais no final com links.
6. CTA para paginas internas da SVD.

## Arquivos que devem ser atualizados em toda execucao
1. Novo post:
`/Users/acidcode/data/svd_site/YYYY/MM/DD/{slug}/index.php`
2. Nova capa:
`/Users/acidcode/data/svd_site/index_svd_files/posts/{slug}.jpg`
3. Home (bloco Blog SVD com apenas 3 mais recentes):
`/Users/acidcode/data/svd_site/index.php`
4. Listagem de blog (novo card no topo):
`/Users/acidcode/data/svd_site/blog/index.php`
5. Sitemap (nova URL + `lastmod` de `/` e `/blog/`):
`/Users/acidcode/data/svd_site/sitemap.xml`

## Fluxo de Implementacao
### 1) Preparacao
1. Executar:
```bash
git pull --rebase origin main
```
2. Se houver conflito de rebase, abortar execucao sem publicar.

### 2) Descoberta do tema em alta
1. Definir foco da semana pelo rodizio (`IA -> PHP -> Tecnologia`).
2. Pesquisar web com queries orientadas ao foco.
3. Coletar candidatos com:
- titulo
- data
- URL
- fonte
4. Rankear por:
- recencia
- relevancia para operacao/negocio
- autoridade da fonte
5. Selecionar 1 tema final e 2-4 fontes.

### 3) Geracao da capa
1. Baixar imagem compativel com o tema (licenca aberta/comercial).
2. Aplicar layout padrao (fundo + painel escuro + titulo/subtitulo + elemento flutuante).
3. Exportar em `1200x630` JPG otimizado.
4. Seguir as regras de:
`/Users/acidcode/data/svd_site/docs/guideline-capas-blog.md`

### 4) Geracao do post
1. Criar novo arquivo de post no padrao atual de template local.
2. Incluir:
- SEO tags
- OpenGraph
- JSON-LD (`BlogPosting`)
- footer padrao
- botao WhatsApp fixo
3. Adicionar secao `Leia Tambem` com 3 posts relevantes existentes.
4. Manter links internos para rotas reais do site.

### 5) Atualizacoes de navegacao e SEO
1. Home: atualizar cards do bloco Blog SVD mantendo apenas 3 mais recentes.
2. `/blog`: inserir novo card no topo da listagem.
3. `sitemap.xml`: incluir nova URL do post e atualizar `lastmod` de `/` e `/blog/`.

### 6) Validacao obrigatoria (antes de Git)
1. `php -l` em todos os arquivos PHP alterados.
2. Conferir existencia da capa e dimensao `1200x630`.
3. Verificar links internos dos arquivos alterados (sem quebrar locais).
4. Validar XML do sitemap.

Comandos de referencia:
```bash
# lint php (exemplo)
php -l /Users/acidcode/data/svd_site/index.php
php -l /Users/acidcode/data/svd_site/blog/index.php
php -l /Users/acidcode/data/svd_site/YYYY/MM/DD/{slug}/index.php

# dimensao da imagem
python3 - << 'PY'
from PIL import Image
im = Image.open('/Users/acidcode/data/svd_site/index_svd_files/posts/{slug}.jpg')
print(im.size)
assert im.size == (1200, 630)
PY

# validacao XML
xmllint --noout /Users/acidcode/data/svd_site/sitemap.xml
```

### 7) Publicacao Git
1. Adicionar somente arquivos esperados.
2. Commit:
```bash
git add <arquivos esperados>
git commit -m "Post semanal: {slug} ({YYYY-MM-DD})"
git push origin main
```
3. Se push falhar por remoto adiantado:
- tentar `git pull --rebase origin main` uma vez
- se conflitar, abortar e nao forcar push

## Casos de Teste e Cenarios
### Cenario feliz
- Tema encontrado
- Capa gerada
- Post criado
- Validacoes OK
- Commit e push OK

### Sem tema forte no foco da semana
- Usar segundo melhor tema do mesmo foco
- se nao houver, fallback para `Tecnologia` com fonte oficial

### Falha no banco de imagem
- Usar fallback visual local no mesmo layout
- so continuar se todas as validacoes passarem

### Falha de validacao
- Abortar publicacao, sem commit/push

### Conflito com remoto
- Abortar apos tentativa de rebase com conflito
- manter alteracoes locais para revisao manual

## Criterios de Aceite
1. Toda quarta as 09:00 a rotina gera 1 post novo completo.
2. Home exibe exatamente os 3 posts mais recentes.
3. `/blog` inclui o novo post no topo.
4. `sitemap.xml` contem a URL nova e `lastmod` atualizado.
5. Nao existe commit/push quando validacoes falham.

## Template minimo de conteudo (para preencher por execucao)
```text
Titulo SEO:
Meta description (140-160):
Slug:
Foco (ia|php|tecnologia):
Data:
Tema em alta escolhido:
Fontes:
- URL 1
- URL 2

Estrutura:
1) Abertura contextual
2) Secao tecnica principal
3) Paragrafo central sobre software sob medida com IA
4) Secao pratica para negocio/operacao
5) Fontes oficiais
6) CTA para paginas internas da SVD
```

## Nota operacional
Esta guideline define o **processo completo** (capa + post + SEO + publicacao). A automacao recorrente deve usar este documento como fonte principal de execucao.
