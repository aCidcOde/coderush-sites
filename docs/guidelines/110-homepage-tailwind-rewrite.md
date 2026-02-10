<!--
/*
[Modulo Documentacao Landing Page SVD]
@Author: André Gomes ( @acidcode )
@since 2026-02-06
Guideline para evolucao da home reescrita em Tailwind, preservando conteudo original e SEO.
*/
-->

# Guideline - Home SVD em Tailwind

## Objetivo
- Manter a home da SVD em HTML limpo + Tailwind, sem dependencias de WordPress.
- Preservar o conteudo e a ordem das secoes da pagina original.
- Evoluir design e codigo sem quebrar SEO, performance e navegacao por ancoras.

## Arquivo principal
- `index.html` (raiz do projeto)

## Regra de ouro
- Conteudo textual deve permanecer fiel ao original, inclusive nomes de secoes, CTA e FAQ.
- IDs de ancora devem ser preservados: `funcionalidades`, `porque`, `vantagens`, `desenvolvimento-ia`, `clientes`, `form`, `contato`.

## Estrutura obrigatoria de secoes
1. Header fixo com logo, menu e CTA.
2. Hero principal (titulo + texto + CTA + animacao).
3. Funcionalidades (6 cards).
4. Bloco de video.
5. Bloco "Por que escolher".
6. Bloco "Caracteristicas do Sistema".
7. Bloco "Vantagens".
8. Bloco "Desenvolvimento com IA" (sinergia com CodaFacil).
9. Bloco "Integracoes".
10. Bloco "Quem usa e recomenda" (depoimentos).
11. Bloco "Empresas que nos ja atendemos".
12. Bloco "Fale conosco" (texto + formulario).
13. Bloco FAQ (9 perguntas).
14. Bloco de posts (3 cards).
15. Bloco final de prova social (25 anos + redes).
16. Botao flutuante de WhatsApp.

## Analise de conteudo do CodaFacil
- Posicionamento: software sob medida orientado por IA.
- Oferta principal: desenvolvimento, consultoria, sustentacao e complementacao de equipe.
- Metodo de entrega: requisitos -> engenharia de processos -> desenvolvimento -> revisao -> testes -> deploy.
- Stack destacada: Laravel, Livewire, Tailwind e MCPs com governanca.
- Promessa de valor: reduzir custo, elevar qualidade e aumentar previsibilidade de entrega.

## Sinergia entre SVD e CodaFacil
- SVD deve apresentar o CodaFacil como subproduto para projetos customizados e expansao tecnologica.
- CodaFacil deve referenciar SVD como origem/produto principal de mercado consolidado.
- Cross-link obrigatorio:
  - SVD -> `codafacil/index.html` (pagina local) + opcional para `https://www.codafacil.dev/`.
  - CodaFacil -> `../index.html#desenvolvimento-ia`.
- Mensagem institucional:
  - SVD: produto validado para operacao de MMN/venda direta.
  - CodaFacil: celula de engenharia para customizacoes, integracoes e novos produtos.
- Sempre manter consistencia visual entre os dois links no header/rodape para navegacao previsivel.

## Assets locais
- Pasta: `index_svd_files/`
- Sempre preferir assets locais para estabilidade e velocidade.
- Lottie JSONs usados atualmente:
  - `index_svd_files/lottie-hero.json`
  - `index_svd_files/lottie-porque.json`
  - `index_svd_files/lottie-caracteristicas.json`

## SEO minimo obrigatorio
- `title` e `meta description` alinhados ao tema da pagina.
- `canonical` para dominio oficial.
- Open Graph completo (`og:title`, `og:description`, `og:image`, `og:url`).
- Schema JSON-LD:
  - `Organization`
  - `WebSite`
  - `FAQPage` (sincronizado com perguntas visiveis).
- Hierarquia semantica de headings com apenas 1 `h1`.
- `alt` descritivo em todas as imagens relevantes.

## Performance minima obrigatoria
- `loading="lazy"` para imagens fora do first paint.
- Manter dimensoes (`width`/`height`) nas imagens para evitar layout shift.
- Evitar scripts externos desnecessarios.
- Usar assets locais quando possivel.

## Acessibilidade minima obrigatoria
- Link "Pular para o conteudo".
- `aria-label` em links criticos e botoes icones.
- Contraste de texto em fundo azul deve ser legivel em mobile.
- `summary/details` no FAQ para navegacao simples.

## Checklist antes de publicar
- Menu ancora funcionando em desktop e mobile.
- Todos os IDs esperados presentes.
- Link de sinergia entre SVD e CodaFacil validado nos dois sentidos.
- FAQ visual e JSON-LD com os mesmos itens.
- Links externos abrindo com `target="_blank"` + `rel="noopener noreferrer"`.
- WhatsApp flutuante ativo.
- Sem scripts, CSS ou classes do WordPress/Elementor.

## Evolucao recomendada
- Etapa 1: Extrair componentes repetidos (card, testimonial, faq-item).
- Etapa 2: Migrar Tailwind CDN para build local (Tailwind v4 via Vite) quando o projeto Laravel estiver nesta pasta.
- Etapa 3: Integrar formulario com backend real e eventos de conversao.
