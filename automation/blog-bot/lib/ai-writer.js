async function generateWithOpenAI({ apiKey, model, prompt }) {
  const response = await fetch("https://api.openai.com/v1/chat/completions", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${apiKey}`
    },
    body: JSON.stringify({
      model,
      temperature: 0.7,
      messages: [
        {
          role: "system",
          content:
            "Voce escreve artigos tecnicos em pt-BR para blogs corporativos. Responda em JSON valido."
        },
        {
          role: "user",
          content: prompt
        }
      ],
      response_format: { type: "json_object" }
    })
  });

  if (!response.ok) {
    const text = await response.text();
    throw new Error(`OpenAI API error (${response.status}): ${text}`);
  }

  const data = await response.json();
  const content = data.choices?.[0]?.message?.content;
  if (!content) {
    throw new Error("Resposta da OpenAI sem conteudo.");
  }

  return JSON.parse(content);
}

function generateFallbackContent({ siteName, theme, focus }) {
  return {
    headline: `${siteName}: como usar ${focus.toUpperCase()} de forma pratica`,
    summary: `Guia objetivo sobre ${theme} com foco em resultado operacional.`,
    sections: [
      {
        title: "Contexto de mercado",
        body: `Empresas estao acelerando a adocao de ${focus} para reduzir retrabalho, ganhar previsibilidade e melhorar a experiencia do cliente.`
      },
      {
        title: "Aplicacao tecnica",
        body: `A abordagem recomendada e iniciar com um fluxo critico, medir impacto e evoluir com governanca de dados e seguranca desde o inicio.`
      },
      {
        title: "Software sob medida com IA",
        body: "Solucoes personalizadas com IA permitem integrar sistemas legados, padronizar operacoes e manter controle sobre regras de negocio."
      },
      {
        title: "Plano de execucao",
        body: "Comece com piloto de 30 dias, defina KPIs, valide com usuarios reais e escale somente o que trouxer ganho comprovado."
      }
    ]
  };
}

module.exports = {
  generateWithOpenAI,
  generateFallbackContent
};
