/**
 * [Modulo Blog Bot — telas do produto dentro do post]
 * @Author: Andre Gomes ( @acidcode )
 * @since 2026-11-26
 *
 * POR QUE ISSO EXISTE: o usuario pediu em 26/11 imagens no meio do texto de
 * todos os posts — "pra nao ficar so coisa de leitura". A imagem que mais vale
 * pra quem le um post do SVD nao e ilustracao: e a tela do sistema fazendo o que
 * o texto acabou de explicar. E prova (o leitor ve que existe), e convite (a
 * legenda leva pra demonstracao) e nao custa nada — a IA de imagem custava,
 * falhava e vazava texto alucinado (ver capa-local.py).
 *
 * As telas sao fotos do AMBIENTE DE DEMONSTRACAO (painel.sistemavendadireta.com.br,
 * dados ficticios), tiradas em 26/11/2026 so com telas que tinham conteudo e
 * nenhum dado pessoal. Ficam em sistemavendadireta/imagens/posts/telas/.
 * Trocar uma tela = trocar o .webp com o mesmo nome; nenhum post precisa mudar.
 *
 * A escolha e por palavra-chave contra slug + titulo + subtitulos do post. Empate
 * desempata pelo hash do slug, pra nao sair a mesma dupla em todo post.
 */
const DEMO_URL = "https://painel.sistemavendadireta.com.br/primeiros-passos";

const TELAS = [
  {
    id: "admin-painel",
    alt: "Painel da administração do Sistema Venda Direta com gráfico de receitas e despesas por mês",
    legenda: "Painel da administração: receitas e despesas mês a mês, sem montar planilha.",
    chaves: ["receita", "previsibilidade", "financeiro", "gestao", "custo", "inadimplencia", "fluxo", "governanca", "escalar", "crescer", "quanto custa", "faturamento", "notas", "fiscais", "tributaria"]
  },
  {
    id: "admin-plano-comercial",
    alt: "Tela de plano comercial com níveis, metas anuais, metas mensais e desconto de cada nível",
    legenda: "Plano comercial configurado na tela: nível, meta e desconto — mudar a regra não vira projeto de programação.",
    chaves: ["comissao", "comissoes", "comissionamento", "plano", "carreira", "cargo", "pontos", "bonus", "binario", "unilevel", "compensacao", "payout", "qualificacao"]
  },
  {
    id: "admin-estoque",
    alt: "Tela de gerenciamento de estoque com produtos, quantidades e código do ERP",
    legenda: "Estoque com o código do ERP amarrado a cada produto — a base da integração fiscal e logística.",
    chaves: ["erp", "fiscal", "nota", "nf", "estoque", "logistica", "integracao", "integrar", "bling", "totvs", "pagamento", "entrega"]
  },
  {
    id: "admin-relatorios",
    alt: "Lista de relatórios da administração: escalada, ranking de parceiros, usuários por estado e saldo liberado",
    legenda: "Relatórios prontos: ranking, mapa de consultores, aniversariantes e saldo liberado.",
    chaves: ["relatorio", "dados", "indicador", "crm", "governanca", "controle", "auditoria", "fraude", "decisao", "acompanhar", "regulamentacao"]
  },
  {
    id: "consultor-inicio",
    alt: "Tela inicial do escritório virtual do consultor com nível atual, total recebido e total da rede",
    legenda: "Escritório virtual: o consultor abre e já vê nível, ganhos e tamanho da rede.",
    chaves: ["escritorio", "consultor", "distribuidor", "suporte", "atendimento", "ia", "onboarding", "ativacao", "experiencia", "engajamento"]
  },
  {
    id: "consultor-escalada",
    alt: "Tela Minha Escalada mostrando nível atual, desconto vigente, próximo nível e régua de níveis",
    legenda: "Minha Escalada: quanto falta para o próximo nível e o ritmo do mês, à vista do consultor.",
    chaves: ["carreira", "retencao", "motivacao", "meta", "metas", "nivel", "niveis", "reconhecimento", "recrutamento", "engajamento", "plano", "progresso"]
  },
  {
    id: "consultor-rede",
    alt: "Tela da rede do consultor com resumo de ativos e inativos e distribuição por nível",
    legenda: "A rede do consultor: ativos, inativos e quantas pessoas em cada nível.",
    // sem "mmn"/"multinivel"/"rede" soltos: estao em quase todo titulo e essa
    // tela ia em 14 de 20 posts
    chaves: ["marketing de rede", "marketing rede", "indicacao", "downline", "unilevel", "diferenca", "equipe", "lideranca", "empresas", "tecnologia"]
  },
  {
    id: "consultor-novo-cadastro",
    alt: "Formulário de novo cadastro de indicado em três etapas: dados pessoais, endereço e revisão",
    legenda: "Cadastro de indicado em três etapas, feito pelo próprio consultor — sem passar pelo escritório.",
    // "cadastro" solto casava com "cadastro de produtos" em post fiscal (26/11/2026)
    chaves: ["cadastrar", "cadastro de consultor", "cadastro de consultores", "consultores", "recrutamento", "onboarding", "novo consultor", "ativacao", "kyc"]
  },
  {
    id: "consultor-recompra",
    alt: "Catálogo de recompra no escritório virtual com produtos, preços e botão comprar",
    legenda: "Recompra pelo escritório: o consultor pede com o desconto do nível dele já aplicado.",
    chaves: ["recompra", "pedido", "pedidos", "produto", "produtos", "catalogo", "ativacao", "preco", "precificar", "moeda"]
  },
  {
    id: "loja-virtual",
    alt: "Loja virtual do consultor com vitrine, menu de categorias e carrinho",
    legenda: "Loja virtual de cada consultor: o cliente final compra e a venda cai na conta de quem indicou.",
    chaves: ["loja", "ecommerce", "e-commerce", "consumidor final", "online", "vender", "digital", "vitrine", "afiliado", "pais", "internacional"]
  }
];

function normalizar(texto) {
  return String(texto || "")
    .toLowerCase()
    .normalize("NFD")
    .replace(/[̀-ͯ]/g, "")
    .replace(/-/g, " ");
}

function hash(texto) {
  let h = 2166136261;
  for (const c of String(texto)) h = Math.imul(h ^ c.charCodeAt(0), 16777619) >>> 0;
  return h;
}

/** Duas telas diferentes, a mais aderente primeiro. */
function escolherTelas({ slug, titulo = "", subtitulos = [] }, quantas = 2, usados = {}) {
  // palavra inteira: "nivel" nao pode casar dentro de "multinivel", nem "ia"
  // dentro de "estrategia" — os dois faziam a mesma tela cair em metade dos posts
  const soPalavras = (t) => ` ${normalizar(t).replace(/<[^>]+>/g, " ").replace(/[^a-z0-9]+/g, " ").trim()} `;
  const alvo = soPalavras([slug, titulo, ...subtitulos].join(" "));
  const peso = (t) => t.chaves.reduce((n, k) => n + (alvo.includes(soPalavras(k)) ? 1 : 0), 0);
  const h = hash(slug);
  return TELAS
    .map((t, i) => ({ t, p: peso(t), u: usados[t.id] || 0, d: (h + i * 2654435761) % 1000 }))
    // empate de aderencia: a tela menos usada ate aqui (quando quem chama
    // conta — o script dos posts antigos conta), depois o hash do slug
    .sort((a, b) => b.p - a.p || a.u - b.u || a.d - b.d)
    .slice(0, quantas)
    .map((x) => x.t);
}

function esc(s) {
  return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
}

function figuraTela(tela, relativeRoot, slug) {
  const link = `${DEMO_URL}?utm_source=blog&amp;utm_medium=tela&amp;utm_campaign=demo&amp;utm_content=${encodeURIComponent(slug)}`;
  return `      <figure class="tela-produto mt-8 overflow-hidden rounded-2xl border border-white/15 bg-white/5">
        <img src="${relativeRoot}imagens/posts/telas/${tela.id}.webp" alt="${esc(tela.alt)}" class="block w-full" style="height:auto" width="1200" height="750" loading="lazy" decoding="async" />
        <figcaption class="px-4 py-3 text-xs leading-6 text-white/70 sm:text-sm">${esc(tela.legenda)} <a href="${link}" target="_blank" rel="noopener" class="font-semibold text-white underline decoration-white/40 underline-offset-4" onclick="window.gtag&amp;&amp;gtag('event','demo_click',{page:location.pathname,link_url:this.href.split('?')[0]})">Ver na demonstração →</a></figcaption>
      </figure>`;
}

/**
 * Insere as telas no corpo ja renderizado do post: a primeira depois do 1o
 * bloco com <h2>, a segunda depois do 3o (ou do ultimo antes do FAQ, em post
 * curto). Idempotente: corpo que ja tem .tela-produto volta igual.
 */
function inserirTelas(corpo, { slug, relativeRoot, usados = {} }) {
  if (!corpo || corpo.includes('class="tela-produto')) return corpo;
  const titulo = (corpo.match(/<h1[^>]*>([\s\S]*?)<\/h1>/) || [])[1] || "";
  const subtitulos = [...corpo.matchAll(/<h2[^>]*>([\s\S]*?)<\/h2>/g)].map((m) => m[1]);
  const telas = escolherTelas({ slug, titulo, subtitulos }, 2, usados);
  telas.forEach((t) => (usados[t.id] = (usados[t.id] || 0) + 1));

  // fim de cada <section> que tem <h2>, exceto o FAQ ("Perguntas frequentes")
  const fins = [];
  const re = /<section class="mt-8">\s*<h2[^>]*>([\s\S]*?)<\/h2>[\s\S]*?<\/section>\n/g;
  let m;
  while ((m = re.exec(corpo))) {
    if (/perguntas frequentes/i.test(m[1])) continue;
    fins.push(m.index + m[0].length);
  }
  if (!fins.length) return corpo;
  const pontos = [fins[0]];
  const segundo = fins[Math.min(2, fins.length - 1)];
  if (segundo !== fins[0]) pontos.push(segundo);

  let out = corpo;
  // de tras pra frente: inserir antes nao desloca os indices de depois
  pontos
    .map((p, i) => [p, telas[i]])
    .sort((a, b) => b[0] - a[0])
    .forEach(([p, tela]) => {
      out = out.slice(0, p) + figuraTela(tela, relativeRoot, slug) + "\n" + out.slice(p);
    });
  return out;
}

module.exports = { TELAS, escolherTelas, inserirTelas, figuraTela };
