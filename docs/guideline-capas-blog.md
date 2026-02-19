# Guideline de Capas do Blog SVD

## Objetivo
Padronizar a criacao de capas para posts do blog SVD, com visual hitech, identidade azul da marca, leitura forte em redes sociais e execucao reproduzivel.

## Escopo
Esta guideline cobre:
- direcao visual
- copy da capa
- contrato tecnico do arquivo
- fluxo de geracao (OpenAI Image API e fallback local)
- checklist de validacao
- rotina semanal de execucao

## Contrato do Asset
- Dimensao: `1200x630`
- Formato: `JPG progressivo`
- Qualidade: `90`
- Safe area: `60px` em todo o perimetro
- Layout: texto a esquerda, imagem flutuante a direita
- Caminho de saida padrao: `/Users/acidcode/data/svd_site/index_svd_files/posts/{slug}.jpg`

## Direcao Visual (Hitech SVD)
### Paleta
- Base: `#004AAD`
- Sombra: `#003F91`
- Apoio: `#215BA8`
- Tipografia: branco/off-white

### Composicao
- Fundo com gradiente diagonal azul + textura sutil de circuito
- Painel escuro semitransparente para bloco de texto
- Elemento flutuante a direita (foto/arte tech) com sombra e glow azul
- Camadas geometricas discretas para profundidade (sem poluicao visual)

### Tipografia
- Titulo: peso alto, caixa alta, contraste forte
- Subtitulo: peso medio, alta legibilidade
- Selo superior curto: contexto editorial (ex.: `BLOG SVD - DESENVOLVIMENTO PHP`)

## Regras de Copy
- Titulo direto (maximo impacto, 2-4 palavras quando possivel)
- Subtitulo orientado a valor de negocio/operacao
- Texto sempre em pt-BR
- Evitar claims tecnicos nao confirmados
- Evitar excesso de texto na area da capa

### Exemplo aprovado (V1 oficial)
- Titulo: `PHP 8.5 EM FOCO`
- Subtitulo: `Menos custo operacional com backend moderno`
- Selo: `BLOG SVD - DESENVOLVIMENTO PHP`
- Arquivo: `/Users/acidcode/data/svd_site/index_svd_files/posts/php-8-5-em-foco.jpg`

## Fluxo de Geracao
### Opcao A (preferencial): OpenAI Image API via skill imagegen
Pre-requisitos:
- `OPENAI_API_KEY` definido no ambiente
- CLI da skill: `/Users/acidcode/.codex/skills/imagegen/scripts/image_gen.py`

Comando base (ajustar prompt e output):
```bash
python3 /Users/acidcode/.codex/skills/imagegen/scripts/image_gen.py generate \
  --prompt "Hitech blog cover in SVD blue palette, title on left, floating tech image on right" \
  --size 1536x1024 \
  --quality high \
  --output-format png \
  --out /Users/acidcode/data/svd_site/tmp/imagegen/raw-cover.png
```

Depois:
- recortar/redimensionar para `1200x630`
- aplicar copy final
- exportar JPG progressivo em `index_svd_files/posts/`

### Opcao B (fallback local): composicao com Pillow
Usar quando nao houver `OPENAI_API_KEY`:
- gerar base com gradiente + textura
- inserir foto tech livre (licenca valida)
- aplicar painel de texto, selo e copy
- exportar `1200x630` JPG progressivo

## Licenca e Compliance
- Usar imagens com licenca aberta/comercial
- Nao usar logos de terceiros visiveis
- Nao usar marcas de produto sem permissao
- Garantir rastreabilidade da origem da imagem base

## Checklist de Validacao
- Arquivo existe no caminho esperado
- Dimensao exatamente `1200x630`
- Texto legivel em miniatura (largura 320px)
- Contraste minimo AA entre texto e fundo
- Peso otimizado (target recomendado: ate ~250KB)
- Sem artefatos visuais fortes
- Sem logos de terceiros

## Integracao com Post
Ao publicar post novo, atualizar:
- `/Users/acidcode/data/svd_site/index.php` (3 posts mais recentes)
- `/Users/acidcode/data/svd_site/blog/index.php` (novo card no topo)
- `/Users/acidcode/data/svd_site/sitemap.xml` (nova URL e `lastmod`)

## Rotina Semanal de Execucao
Janela padrao:
- quarta-feira, 09:00 (America/Sao_Paulo)

Passos:
1. Definir tema da semana (IA, PHP ou Tecnologia)
2. Fechar copy da capa (titulo, subtitulo, selo)
3. Gerar capa no padrao desta guideline
4. Validar checklist completo
5. Seguir atualizacao do post/site conforme integracao acima

## Controle de Versao do Design
Versao oficial atual:
- `/Users/acidcode/data/svd_site/index_svd_files/posts/php-8-5-em-foco.jpg`

Regras:
- qualquer variacao de teste nao deve substituir o padrao oficial sem aprovacao explicita
- ao aprovar nova variacao, atualizar esta guideline para apontar o novo padrao oficial
