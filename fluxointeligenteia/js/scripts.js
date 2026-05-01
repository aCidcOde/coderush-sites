// Extracted from fluxointeligenteia/index.html block 1.
(function(){var b=document.getElementById("back-to-top");if(!b)return;var t=120;function s(){var u=window.scrollY<t;b.classList.toggle("is-at-top",u);b.setAttribute("aria-disabled",u?"true":"false");if(u)b.setAttribute("tabindex","-1");else b.removeAttribute("tabindex");}b.addEventListener("click",function(){if(b.classList.contains("is-at-top"))return;if(window.matchMedia("(prefers-reduced-motion: reduce)").matches)window.scrollTo(0,0);else window.scrollTo({top:0,behavior:"smooth"});});window.addEventListener("scroll",s,{passive:true});s();})();

// Extracted from fluxointeligenteia/index.html block 2.
(function(){var h=document.querySelector("[data-site-header]");if(!h)return;function o(){h.classList.toggle("is-scrolled",window.scrollY>16);}o();window.addEventListener("scroll",o,{passive:true});})();

// Extracted from fluxointeligenteia/index.html block 3.
(function(){var b=document.getElementById("mobile-menu-btn"),m=document.getElementById("mobile-menu"),io=document.getElementById("mobile-menu-icon-open"),ic=document.getElementById("mobile-menu-icon-close");if(!b||!m)return;function setOpen(o){m.classList.toggle("hidden",!o);b.setAttribute("aria-expanded",o?"true":"false");b.setAttribute("aria-label",o?"Fechar menu de navegação":"Abrir menu de navegação");document.body.classList.toggle("overflow-hidden",o);if(io&&ic){io.classList.toggle("hidden",o);ic.classList.toggle("hidden",!o);}}b.addEventListener("click",function(e){e.stopPropagation();setOpen(m.classList.contains("hidden"));});m.querySelectorAll('a[href^="#"]').forEach(function(a){a.addEventListener("click",function(){setOpen(false);});});document.addEventListener("click",function(e){if(m.classList.contains("hidden"))return;if(!m.contains(e.target)&&!b.contains(e.target))setOpen(false);});document.addEventListener("keydown",function(e){if(e.key==="Escape")setOpen(false);});window.addEventListener("resize",function(){if(window.matchMedia("(min-width: 768px)").matches)setOpen(false);});})();

// Extracted from fluxointeligenteia/index.html block 4.
(function () {
      function observeSection(selector, className, options) {
        var el = document.querySelector(selector);
        if (!el) return;
        function rev() { el.classList.add(className); }
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { rev(); return; }
        if (typeof IntersectionObserver === 'undefined') { rev(); return; }
        var obs = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) { rev(); obs.disconnect(); }
          });
        }, options);
        obs.observe(el);
      }
      observeSection('#cases', 'cases-visible', { threshold: 0.1, rootMargin: '0px 0px -6% 0px' });
      observeSection('#depoimentos', 'testimonials-visible', { threshold: 0.1, rootMargin: '0px 0px -7% 0px' });
      observeSection('#faq', 'faq-visible', { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
      observeSection('#contato', 'contact-visible', { threshold: 0.08, rootMargin: '0px 0px -5% 0px' });
      observeSection('#sobre', 'sobre-visible', { threshold: 0.1, rootMargin: '0px 0px -6% 0px' });
      observeSection('#site-footer', 'footer-visible', { threshold: 0.08, rootMargin: '0px 0px -4% 0px' });
    })();

// Extracted from fluxointeligenteia/index.html block 5.
document.addEventListener("DOMContentLoaded",function(){
      var y=document.getElementById("year-copy");if(y)y.textContent=new Date().getFullYear();
      var leadForm=document.getElementById("contact-lead-form");
      var leadNameInput=document.getElementById("contact-nome");
      var leadWhatsappInput=document.getElementById("contact-whatsapp");
      var successBox=document.getElementById("contact-success-box");
      var errorBox=document.getElementById("contact-error-box");
      var successWhatsappLink=document.getElementById("contact-success-whatsapp-link");
      var feedbackEl=document.getElementById("contact-feedback");
      var leadStorageKey="fluxo-contact-lead";
      var urlParams=new URLSearchParams(window.location.search);
      var mailStatus=urlParams.get("mail");
      var defaultWaMsg="Ola, vim pelo site da FluxoInteligente IA e quero saber mais sobre agentes corporativos.";
      var phone="5511994566726";

      function getStoredLead(){try{var r=window.sessionStorage.getItem(leadStorageKey);return r?JSON.parse(r):null;}catch(e){return null;}}

      function buildLeadWhatsappUrl(lead){
        var s=lead||{};
        var tpl=leadForm?(leadForm.getAttribute("data-whatsapp-message-template")||defaultWaMsg):defaultWaMsg;
        var p=leadForm?(leadForm.getAttribute("data-whatsapp-phone")||phone):phone;
        if(!s.nome&&!s.whatsapp)return"https://wa.me/"+p+"?text="+encodeURIComponent(defaultWaMsg);
        var msg=tpl.replace("{nome}",(s.nome||"Nao informado").trim()).replace("{whatsapp}",(s.whatsapp||"Nao informado").trim());
        return"https://wa.me/"+p+"?text="+encodeURIComponent(msg);
      }

      if(mailStatus==="ok"&&successBox){
        successBox.classList.remove("hidden");
        if(feedbackEl){feedbackEl.classList.remove("hidden");feedbackEl.className="contact-feedback-alert is-success";feedbackEl.textContent="Recebemos seus dados. Vamos abrir o WhatsApp com uma mensagem pronta para agilizar seu atendimento.";}
        var storedLead=getStoredLead();
        if(successWhatsappLink)successWhatsappLink.href=buildLeadWhatsappUrl(storedLead);
        window.setTimeout(function(){var popup=window.open(buildLeadWhatsappUrl(storedLead),"_blank","noopener,noreferrer");if(popup)popup.opener=null;},250);
        try{window.sessionStorage.removeItem(leadStorageKey);}catch(e){}
        if(window.location.hash!=="#contato")window.location.hash="#contato";
        if(window.history&&window.history.replaceState)window.history.replaceState(null,"",window.location.pathname+"#contato");
      }else if(mailStatus==="erro"&&errorBox){
        errorBox.classList.remove("hidden");
        if(feedbackEl){feedbackEl.classList.remove("hidden");feedbackEl.className="contact-feedback-alert is-error";feedbackEl.textContent="Nao conseguimos enviar seus dados agora. Revise o WhatsApp informado ou use o atalho direto para falar com o comercial.";}
        try{window.sessionStorage.removeItem(leadStorageKey);}catch(e){}
        if(window.location.hash!=="#contato")window.location.hash="#contato";
        if(window.history&&window.history.replaceState)window.history.replaceState(null,"",window.location.pathname+"#contato");
      }

      if(leadForm){
        if(mailStatus!=="ok"){var prev=getStoredLead();if(prev){if(leadNameInput&&!leadNameInput.value)leadNameInput.value=prev.nome||"";if(leadWhatsappInput&&!leadWhatsappInput.value)leadWhatsappInput.value=prev.whatsapp||"";}}
        leadForm.addEventListener("submit",function(){
          var payload={nome:leadNameInput?leadNameInput.value.trim():"",whatsapp:leadWhatsappInput?leadWhatsappInput.value.trim():""};
          try{window.sessionStorage.setItem(leadStorageKey,JSON.stringify(payload));}catch(e){}
        });
      }
    });

// Extracted from fluxointeligenteia/index.html block 6.
document.addEventListener("DOMContentLoaded",function(){
      if(typeof lucide!=="undefined"&&lucide.createIcons)lucide.createIcons();
      (function(){
        var backdrop=document.getElementById("chat-backdrop"),panel=document.getElementById("chat-panel"),launcher=document.getElementById("chat-launcher"),closeBtn=document.getElementById("chat-close"),messagesEl=document.getElementById("chat-messages"),quickEl=document.getElementById("chat-quick"),form=document.getElementById("chat-form"),input=document.getElementById("chat-input"),iconMsg=document.getElementById("chat-launcher-icon-msg"),iconX=document.getElementById("chat-launcher-icon-x"),labelOpen=document.getElementById("chat-launcher-label-open"),labelClose=document.getElementById("chat-launcher-label-close");
        if(!backdrop||!panel||!launcher||!messagesEl||!quickEl||!form||!input)return;
        var open=false,initialized=false,rm=window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        function icons(){if(typeof lucide!=="undefined"&&lucide.createIcons)lucide.createIcons();}
        function scrollB(){messagesEl.scrollTop=messagesEl.scrollHeight;}
        function bot(t){var d=document.createElement("div");d.className="chat-msg chat-msg--bot";d.textContent=t;messagesEl.appendChild(d);scrollB();}
        function user(t){var d=document.createElement("div");d.className="chat-msg chat-msg--user";d.textContent=t;messagesEl.appendChild(d);scrollB();}
        function typing(then){var r=document.createElement("div");r.className="chat-msg chat-msg--bot chat-typing";r.setAttribute("aria-hidden","true");r.innerHTML="<span></span><span></span><span></span>";messagesEl.appendChild(r);scrollB();setTimeout(function(){if(r.parentNode)r.parentNode.removeChild(r);then();},rm?80:420);}        function ctaChip(){return{label:"Abrir formulário",onClick:function(){window.location.hash="#contato";setOpen(false);var el=document.getElementById("contato");if(el)el.scrollIntoView({behavior:rm?"auto":"smooth"});}};}
        function renderQuick(arr){quickEl.innerHTML="";arr.forEach(function(item){var x=document.createElement("button");x.type="button";x.className="chat-chip";x.textContent=item.label;x.addEventListener("click",item.onClick);quickEl.appendChild(x);});}
        function setOpen(v){
          open=v;backdrop.classList.toggle("is-open",v);panel.classList.toggle("is-open",v);backdrop.setAttribute("aria-hidden",v?"false":"true");panel.setAttribute("aria-hidden",v?"false":"true");launcher.setAttribute("aria-expanded",v?"true":"false");launcher.classList.toggle("is-open",v);document.body.classList.toggle("overflow-hidden",v);
          if(iconMsg&&iconX){iconMsg.classList.toggle("hidden",v);iconX.classList.toggle("hidden",!v);}
          if(labelOpen&&labelClose){labelOpen.classList.toggle("hidden",v);labelClose.classList.toggle("hidden",!v);}
          icons();
          if(v&&!initialized){initialized=true;bot("Olá! Posso orientar sobre diagnóstico, agente corporativo ou Link — ou te levar ao formulário.");
            renderQuick([
              {label:"Diagnóstico",onClick:function(){user("Quero diagnóstico");typing(function(){bot("O diagnóstico mapeia processos, gargalos e agentes com maior potencial de ROI. Use o formulário com essa opção.");renderQuick([ctaChip(),{label:"Menu",onClick:function(){renderQuick(menu());}}]);});}},
              {label:"Agente corporativo",onClick:function(){user("Quero um agente corporativo");typing(function(){bot("Desenhamos persona, permissões, base de conhecimento, tools, canais e integrações para produção.");renderQuick([ctaChip(),{label:"Menu",onClick:function(){renderQuick(menu());}}]);});}},
              {label:"Link",onClick:function(){user("Quero conhecer o Link");typing(function(){bot("O Link é nossa base para agentes corporativos com RAG, tools, permissões, auditoria e canais integrados.");renderQuick([ctaChip(),{label:"Menu",onClick:function(){renderQuick(menu());}}]);});}},
              {label:"Hub CodeRush",onClick:function(){user("Ver ecossistema");typing(function(){bot("Somos parte do CodeRush — veja todas as marcas no hub.");var d=document.createElement("div");d.className="chat-msg chat-msg--bot mt-2";var a=document.createElement("a");a.href="/";a.className="font-semibold text-emerald-400 underline decoration-emerald-400/40 underline-offset-2 hover:text-emerald-300";a.textContent="Abrir hub CodeRush";d.appendChild(a);messagesEl.appendChild(d);scrollB();renderQuick([ctaChip(),{label:"Menu",onClick:function(){renderQuick(menu());}}]);});}}
            ]);
          }
          if(v)setTimeout(function(){input.focus();},rm?0:200);
        }
        function menu(){return[
          {label:"Diagnóstico",onClick:function(){user("Diagnóstico");typing(function(){bot("Use o formulário para pedir um diagnóstico de automação com IA.");renderQuick([ctaChip()]);});}},
          {label:"Agente corporativo",onClick:function(){user("Agente corporativo");typing(function(){bot("Descreva sistemas, documentos e canais desejados no formulário para uma proposta.");renderQuick([ctaChip()]);});}},
          {label:"Link",onClick:function(){user("Link");typing(function(){bot("Podemos demonstrar o Link como plataforma de agentes com governança.");renderQuick([ctaChip()]);});}},
          {label:"Hub CodeRush",onClick:function(){user("CodeRush");typing(function(){bot("Acesse o hub para outras empresas do grupo.");var d=document.createElement("div");d.className="chat-msg chat-msg--bot mt-2";var a=document.createElement("a");a.href="/";a.className="font-semibold text-emerald-400 underline";a.textContent="coderush hub";d.appendChild(a);messagesEl.appendChild(d);scrollB();renderQuick([ctaChip()]);});}}
        ];}
        function freeText(raw){var t=raw.trim();if(t.length<2)return;user(t);input.value="";typing(function(){bot("Obrigado pelo contexto. Para retorno estruturado, use o formulário de contato — nossa equipe responde em nome da FluxoInteligente IA.");renderQuick([ctaChip(),{label:"Menu",onClick:function(){renderQuick(menu());}}]);});}
        backdrop.addEventListener("click",function(){setOpen(false);});
        closeBtn&&closeBtn.addEventListener("click",function(){setOpen(false);});
        launcher.addEventListener("click",function(){setOpen(!open);});
        form.addEventListener("submit",function(e){e.preventDefault();freeText(input.value);});
      })();
    });

(function(){
      function buildPageNeural(){
        var svg=document.getElementById("page-neural");
        if(!svg)return;
        var edgesG=document.getElementById("pn-edges");
        var nodesG=document.getElementById("pn-nodes");
        var pulsesG=document.getElementById("pn-pulses");
        if(!edgesG||!nodesG||!pulsesG)return;
        edgesG.textContent="";
        nodesG.textContent="";
        pulsesG.textContent="";
        var W=1920,H=2400,cols=7,rows=12,colStep=W/(cols-1),rowStep=H/(rows-1),pts=[],svgNs="http://www.w3.org/2000/svg";
        for(var c=0;c<cols;c++){
          pts[c]=[];
          for(var r=0;r<rows;r++){
            var jx=Math.sin(c*12.9+r*4.1)*0.5*(colStep*0.32);
            var jy=Math.cos(c*5.3+r*7.7)*0.5*(rowStep*0.30);
            pts[c][r]={x:c*colStep+jx,y:r*rowStep+jy};
          }
        }
        for(var c2=0;c2<cols-1;c2++){
          for(var r2=0;r2<rows;r2++){
            var p=pts[c2][r2];
            var targets=[pts[c2+1][r2]];
            if(r2>0&&(r2+c2)%3!==0)targets.push(pts[c2+1][r2-1]);
            if(r2<rows-1&&(r2+c2)%2===0)targets.push(pts[c2+1][r2+1]);
            targets.forEach(function(t){
              var line=document.createElementNS(svgNs,"line");
              line.setAttribute("x1",p.x.toFixed(1));
              line.setAttribute("y1",p.y.toFixed(1));
              line.setAttribute("x2",t.x.toFixed(1));
              line.setAttribute("y2",t.y.toFixed(1));
              line.setAttribute("opacity",(0.45+Math.random()*0.35).toFixed(2));
              edgesG.appendChild(line);
            });
          }
        }
        for(var c3=0;c3<cols;c3++){
          for(var r3=0;r3<rows;r3++){
            var p2=pts[c3][r3];
            var ci=document.createElementNS(svgNs,"circle");
            ci.setAttribute("cx",p2.x.toFixed(1));
            ci.setAttribute("cy",p2.y.toFixed(1));
            ci.setAttribute("r",(2+((c3+r3)%3===0?1.5:0)).toFixed(1));
            ci.setAttribute("opacity","0.65");
            ci.setAttribute("class","neural-node n"+((c3+r3)%6+1));
            nodesG.appendChild(ci);
          }
        }
        for(var i=0;i<6;i++){
          var row=Math.floor(Math.random()*rows);
          var path="M"+pts[0][row].x.toFixed(1)+","+pts[0][row].y.toFixed(1);
          for(var c4=1;c4<cols;c4++){
            var drift=Math.random()<0.5?-1:1;
            if(Math.random()<0.55)row=Math.max(0,Math.min(rows-1,row+drift));
            path+=" L"+pts[c4][row].x.toFixed(1)+","+pts[c4][row].y.toFixed(1);
          }
          var pulse=document.createElementNS(svgNs,"circle");
          pulse.setAttribute("r","3");
          pulse.setAttribute("fill",i%2?"url(#pn-pulse-alt)":"url(#pn-pulse)");
          var motion=document.createElementNS(svgNs,"animateMotion");
          motion.setAttribute("dur",(5+Math.random()*4).toFixed(1)+"s");
          motion.setAttribute("repeatCount","indefinite");
          motion.setAttribute("begin",(-Math.random()*5).toFixed(1)+"s");
          motion.setAttribute("path",path);
          pulse.appendChild(motion);
          pulsesG.appendChild(pulse);
        }
      }
      document.addEventListener("DOMContentLoaded",buildPageNeural);
    })();
