#!/usr/bin/env node
/**
 * Aplica correcoes de acentuacao pt-BR em conteudo visivel.
 * - Mascara atributos HTML cuja semantica e URL/identificador.
 * - Mascara valores de chaves JS tipo href: "...", path: "..." (config files).
 * - Preserva caixa: "Conteudo" -> "Conteúdo", "CONTEUDO" -> "CONTEÚDO".
 *
 * Uso:
 *   node scripts/fix-pt-accents.js --dry-run
 *   node scripts/fix-pt-accents.js
 */

const fs = require("node:fs");
const path = require("node:path");
const { execSync } = require("node:child_process");

const ROOT = path.resolve(__dirname, "..", "..", "..");

const URL_LIKE_ATTRS = [
  "href", "src", "srcset", "action", "formaction",
  "data-blog-slug", "data-blog-path", "data-blog-image",
  "class", "id", "name", "rel", "target",
  "type", "loading", "decoding", "sizes",
  "width", "height", "charset", "http-equiv",
  "crossorigin", "integrity", "referrerpolicy", "hreflang", "lang",
  "property", "media", "value", "role"
];

// JS object keys que costumam carregar caminho/url.
const JS_URL_KEYS = ["href", "path", "url", "src", "ctaPath"];

const REPLACEMENTS = {
  "automacao": "automação", "automacoes": "automações",
  "integracao": "integração", "integracoes": "integrações",
  "operacao": "operação", "operacoes": "operações",
  "producao": "produção", "execucao": "execução", "execucoes": "execuções",
  "evolucao": "evolução", "transformacao": "transformação", "transformacoes": "transformações",
  "modernizacao": "modernização",
  "implementacao": "implementação", "implementacoes": "implementações",
  "configuracao": "configuração", "configuracoes": "configurações",
  "documentacao": "documentação",
  "comunicacao": "comunicação", "comunicacoes": "comunicações",
  "manutencao": "manutenção",
  "decisao": "decisão", "decisoes": "decisões",
  "previsao": "previsão", "previsoes": "previsões",
  "transicao": "transição", "transicoes": "transições",
  "comissao": "comissão", "comissoes": "comissões",
  "atencao": "atenção", "intencao": "intenção",
  "aplicacao": "aplicação", "aplicacoes": "aplicações",
  "variacao": "variação", "variacoes": "variações",
  "relacao": "relação", "relacoes": "relações",
  "selecao": "seleção", "selecoes": "seleções",
  "correcao": "correção", "correcoes": "correções",
  "deteccao": "detecção", "protecao": "proteção",
  "conexao": "conexão", "conexoes": "conexões",
  "adocao": "adoção", "reducao": "redução", "conducao": "condução",
  "avaliacao": "avaliação", "avaliacoes": "avaliações",
  "migracao": "migração", "migracoes": "migrações",
  "expansao": "expansão", "versao": "versão", "versoes": "versões",
  "razao": "razão", "razoes": "razões", "pressao": "pressão",
  "compreensao": "compreensão", "expressao": "expressão",
  "regiao": "região", "regioes": "regiões",
  "coordenacao": "coordenação", "combinacao": "combinação", "combinacoes": "combinações",
  "definicao": "definição", "definicoes": "definições",
  "composicao": "composição", "exposicao": "exposição",
  "supervisao": "supervisão", "divisao": "divisão", "divisoes": "divisões",
  "discussao": "discussão", "discussoes": "discussões",
  "criacao": "criação", "criacoes": "criações",
  "consideracao": "consideração", "consideracoes": "considerações",
  "instalacao": "instalação", "instalacoes": "instalações",
  "investigacao": "investigação",
  "informacao": "informação", "informacoes": "informações",
  "preparacao": "preparação", "validacao": "validação", "verificacao": "verificação",
  "personalizacao": "personalização", "otimizacao": "otimização", "padronizacao": "padronização",
  "atualizacao": "atualização", "atualizacoes": "atualizações",
  "centralizacao": "centralização", "automatizacao": "automatização",
  "concentracao": "concentração", "negociacao": "negociação", "negociacoes": "negociações",
  "demonstracao": "demonstração", "explicacao": "explicação",
  "publicacao": "publicação", "publicacoes": "publicações",
  "indicacao": "indicação", "indicacoes": "indicações",
  "apresentacao": "apresentação",
  "solucao": "solução", "solucoes": "soluções",
  "introducao": "introdução",
  "interacao": "interação", "interacoes": "interações",
  "transacao": "transação", "transacoes": "transações",
  "extensao": "extensão", "ampliacao": "ampliação", "consolidacao": "consolidação",
  "implantacao": "implantação", "habilitacao": "habilitação", "qualificacao": "qualificação",
  "extracao": "extração", "satisfacao": "satisfação", "frustracao": "frustração",
  "antecipacao": "antecipação", "participacao": "participação",
  "exfiltracao": "exfiltração", "alocacao": "alocação", "aprovacao": "aprovação",
  "autorizacao": "autorização", "fundamentacao": "fundamentação", "alimentacao": "alimentação",
  "regulacao": "regulação", "resolucao": "resolução",
  "mitigacao": "mitigação", "mitigacoes": "mitigações",
  "comparacao": "comparação", "comparacoes": "comparações",
  "modificacao": "modificação", "modificacoes": "modificações",
  "duplicacao": "duplicação",

  // -encia / -anca
  "presenca": "presença", "ausencia": "ausência", "essencia": "essência",
  "diligencia": "diligência", "negligencia": "negligência",
  "consequencia": "consequência", "consequencias": "consequências",
  "tendencia": "tendência", "tendencias": "tendências",
  "convergencia": "convergência", "divergencia": "divergência",
  "violencia": "violência", "potencia": "potência",
  "relevancia": "relevância", "importancia": "importância",
  "ciencia": "ciência", "ciencias": "ciências",
  "experiencia": "experiência", "experiencias": "experiências",
  "consciencia": "consciência", "audiencia": "audiência",
  "agencia": "agência", "agencias": "agências",
  "frequencia": "frequência", "inteligencia": "inteligência",
  "urgencia": "urgência", "transparencia": "transparência",
  "permanencia": "permanência", "concorrencia": "concorrência",
  "ocorrencia": "ocorrência", "ocorrencias": "ocorrências",
  "referencia": "referência", "referencias": "referências",
  "diferenca": "diferença", "diferencas": "diferenças",
  "gerencia": "gerência",
  "latencia": "latência",
  "dependencia": "dependência", "dependencias": "dependências",
  "evidencia": "evidência", "evidencias": "evidências",
  "eficiencia": "eficiência", "ineficiencia": "ineficiência",
  "governanca": "governança",

  // adjetivos -ico/-ica
  "tecnica": "técnica", "tecnicas": "técnicas", "tecnico": "técnico", "tecnicos": "técnicos",
  "pratica": "prática", "praticas": "práticas", "pratico": "prático", "praticos": "práticos",
  "pragmatica": "pragmática", "pragmatico": "pragmático",
  "tecnologica": "tecnológica", "tecnologico": "tecnológico",
  "tecnologicas": "tecnológicas", "tecnologicos": "tecnológicos",
  "estrategica": "estratégica", "estrategico": "estratégico",
  "estrategicas": "estratégicas", "estrategicos": "estratégicos",
  "logica": "lógica", "logicas": "lógicas", "logico": "lógico", "logicos": "lógicos",
  "publica": "pública", "publico": "público", "publicas": "públicas", "publicos": "públicos",
  "automatica": "automática", "automatico": "automático",
  "automaticas": "automáticas", "automaticos": "automáticos",
  "magica": "mágica", "magico": "mágico",
  "agentica": "agêntica", "agenticas": "agênticas",
  "agentico": "agêntico", "agenticos": "agênticos",
  "estatica": "estática", "estatico": "estático",
  "estaticas": "estáticas", "estaticos": "estáticos",
  "critica": "crítica", "criticas": "críticas",
  "critico": "crítico", "criticos": "críticos",
  "politica": "política", "politicas": "políticas",
  "politico": "político", "politicos": "políticos",
  "metrica": "métrica", "metricas": "métricas",
  "academica": "acadêmica", "academico": "acadêmico",

  // -ido/-ida
  "rapida": "rápida", "rapido": "rápido", "rapidas": "rápidas", "rapidos": "rápidos",
  "valido": "válido", "valida": "válida", "validos": "válidos", "validas": "válidas",
  "hibrido": "híbrido", "hibrida": "híbrida",
  "rigido": "rígido", "rigida": "rígida",
  "solido": "sólido", "solida": "sólida", "solidos": "sólidos", "solidas": "sólidas",
  "liquido": "líquido",
  "sensivel": "sensível", "sensiveis": "sensíveis",

  // -avel/-ivel
  "facil": "fácil", "dificil": "difícil",
  "possivel": "possível", "impossivel": "impossível",
  "previsivel": "previsível", "imprevisivel": "imprevisível",
  "visivel": "visível", "invisivel": "invisível",
  "estavel": "estável", "instavel": "instável",
  "rentavel": "rentável",
  "agil": "ágil", "ageis": "ágeis", "fragil": "frágil", "frageis": "frágeis",
  "incrivel": "incrível",

  // outros
  "conteudo": "conteúdo", "conteudos": "conteúdos",
  "trafego": "tráfego",
  "estagio": "estágio", "estagios": "estágios",
  "negocio": "negócio", "negocios": "negócios",
  "usuario": "usuário", "usuarios": "usuários",
  "cenario": "cenário", "cenarios": "cenários",
  "criterio": "critério", "criterios": "critérios",
  "credito": "crédito", "creditos": "créditos",
  "debito": "débito", "debitos": "débitos",
  "modulo": "módulo", "modulos": "módulos",
  "relatorio": "relatório", "relatorios": "relatórios",
  "diario": "diário", "diarios": "diários",
  "escritorio": "escritório", "escritorios": "escritórios",
  "salario": "salário", "salarios": "salários",
  "estrategia": "estratégia", "estrategias": "estratégias",
  "analise": "análise", "analises": "análises",
  "principio": "princípio", "principios": "princípios",
  "exercicio": "exercício", "exercicios": "exercícios",
  "beneficio": "benefício", "beneficios": "benefícios",
  "metodo": "método", "metodos": "métodos",
  "duvida": "dúvida", "duvidas": "dúvidas",
  "industria": "indústria", "industrias": "indústrias",
  "memoria": "memória", "memorias": "memórias",
  "historia": "história", "historias": "histórias",
  "vitoria": "vitória", "trajetoria": "trajetória",
  "orcamento": "orçamento", "orcamentos": "orçamentos",
  "servicos": "serviços", "servico": "serviço",
  "fabrica": "fábrica", "fabricas": "fábricas",
  "area": "área", "areas": "áreas",
  "ate": "até", "ja": "já",
  "voce": "você", "voces": "vocês",
  "tambem": "também", "porem": "porém",
  "alem": "além", "apos": "após", "atraves": "através",
  "exito": "êxito", "ambito": "âmbito", "ambitos": "âmbitos",
  "proximo": "próximo", "proxima": "próxima", "proximos": "próximos", "proximas": "próximas",
  "minimo": "mínimo", "minima": "mínima", "minimos": "mínimos", "minimas": "mínimas",
  "maximo": "máximo", "maxima": "máxima", "maximos": "máximos", "maximas": "máximas",
  "otimo": "ótimo", "otima": "ótima", "otimos": "ótimos", "otimas": "ótimas",
  "ultimo": "último", "ultima": "última", "ultimos": "últimos", "ultimas": "últimas",
  "proprio": "próprio", "propria": "própria",
  "proprios": "próprios", "proprias": "próprias",
  "multiplo": "múltiplo", "multipla": "múltipla",
  "multiplos": "múltiplos", "multiplas": "múltiplas",
  "padrao": "padrão", "padroes": "padrões",
  "questao": "questão", "questoes": "questões",
  "estao": "estão", "entao": "então",
  "sao": "são", "nao": "não",
  "mao": "mão", "maos": "mãos",
  "campeao": "campeão", "campeoes": "campeões",
  "sera": "será", "tera": "terá", "fara": "fará", "dara": "dará",
  "estara": "estará", "havera": "haverá", "ira": "irá",
  "podera": "poderá", "devera": "deverá", "ficara": "ficará"
};

function escapeRe(s) { return s.replace(/[.*+?^${}()|[\]\\]/g, "\\$&"); }

const URL_ATTR_RE = new RegExp(
  "(\\s)(" + URL_LIKE_ATTRS.map(escapeRe).join("|") + ")\\s*=\\s*\"([^\"]*)\"",
  "g"
);

// Mascara JS object key url-like: href: "...", path: "...", url: "...", ctaPath: "..."
const JS_URL_KEY_RE = new RegExp(
  "((?:[\\s,{(]|^))(" + JS_URL_KEYS.map(escapeRe).join("|") + ")\\s*:\\s*\"([^\"]*)\"",
  "g"
);

// Mascara comentarios JSDoc/linha (codigo, nao texto)
const JS_LINE_COMMENT = /\/\/[^\n]*/g;
const JS_BLOCK_COMMENT = /\/\*[\s\S]*?\*\//g;

const CODE_BLOCK_RE = /<(script|style|code|pre)\b[^>]*>[\s\S]*?<\/\1>/gi;
const HTML_COMMENT_RE = /<!--[\s\S]*?-->/g;
// Strings JS contendo marcador "BLOG-" (mantem markers intactos mesmo em codigo).
const JS_MARKER_STRING_RE = /(['"`])[^'"`]*BLOG-[^'"`]*\1/g;

function maskAll(content, isJs) {
  const masks = [];
  let masked = content;
  function push(m) { const ph = ` MASK${masks.length} `; masks.push(m); return ph; }
  masked = masked.replace(CODE_BLOCK_RE, push);
  masked = masked.replace(HTML_COMMENT_RE, push);
  masked = masked.replace(URL_ATTR_RE, push);
  if (isJs) {
    masked = masked.replace(JS_MARKER_STRING_RE, push);
    masked = masked.replace(JS_URL_KEY_RE, push);
    masked = masked.replace(JS_BLOCK_COMMENT, push);
    masked = masked.replace(JS_LINE_COMMENT, push);
  }
  return { masked, masks };
}

function unmask(content, masks) {
  return content.replace(/ MASK(\d+) /g, (_, i) => masks[+i]);
}

function preserveCase(src, repl) {
  if (src === src.toUpperCase()) return repl.toUpperCase();
  if (src[0] === src[0].toUpperCase() && src.slice(1) === src.slice(1).toLowerCase()) {
    return repl[0].toUpperCase() + repl.slice(1);
  }
  return repl;
}

const ENTRIES = Object.entries(REPLACEMENTS);
// Boundary strict: nem alfanumerico, nem hifen ou underline (preserva slugs e markers).
const COMBINED_RE = new RegExp(
  "(?<![A-Za-z0-9_-])(" + ENTRIES.map(([k]) => escapeRe(k)).join("|") + ")(?![A-Za-z0-9_-])",
  "gi"
);
const REPLACEMENT_MAP = new Map(ENTRIES);

function applyReplacements(text) {
  return text.replace(COMBINED_RE, (m) => {
    const key = m.toLowerCase();
    const out = REPLACEMENT_MAP.get(key);
    if (!out) return m;
    return preserveCase(m, out);
  });
}

function processFile(filePath, dryRun) {
  const orig = fs.readFileSync(filePath, "utf8");
  const isJs = filePath.endsWith(".js") || filePath.endsWith(".mjs") || filePath.endsWith(".cjs");
  const { masked, masks } = maskAll(orig, isJs);
  let next = applyReplacements(masked);
  // "São + nome de cidade conhecida": preservar grafia normal apos correcao
  // (sao -> são por padrao; tudo bem em texto pt-BR)
  next = unmask(next, masks);
  if (next === orig) return { path: filePath, changed: false };
  if (!dryRun) fs.writeFileSync(filePath, next, "utf8");
  return { path: filePath, changed: true };
}

function listTargets() {
  const out = [];
  const cmd = `find ${ROOT} -type f \\( -path '*/2026/*/*/*/index.php' -o -path '*/2026/*/*/*/index.html' -o -path '*/2023/*/*/*/index.php' \\)`;
  out.push(...execSync(cmd, { encoding: "utf8" }).trim().split("\n").filter(Boolean));
  out.push(path.join(ROOT, "automation/blog-bot/lib/publisher.js"));
  out.push(path.join(ROOT, "automation/blog-bot/lib/related-renderer.js"));
  return out;
}

function main() {
  const dryRun = process.argv.includes("--dry-run");
  const files = listTargets();
  const results = [];
  for (const f of files) {
    if (!fs.existsSync(f)) continue;
    results.push(processFile(f, dryRun));
  }
  const changed = results.filter((r) => r.changed);
  console.log(`${changed.length}/${results.length} arquivos alterados${dryRun ? " (dry-run)" : ""}.`);
  for (const r of changed) console.log(" ✓", path.relative(ROOT, r.path));
}

main();
