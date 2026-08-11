#!/usr/bin/env python3
"""
[Modulo Ads SVD — renovacao do refresh token do Google Ads]
@Author: Andre Gomes ( @acidcode )
@since 2026-08-11

POR QUE ISSO EXISTE: enquanto o app OAuth estiver com status "Teste" na tela de
permissao do Google Cloud, todo refresh token **expira em 7 dias** — sem aviso, e
o cron do painel comeca a devolver invalid_grant. A correcao definitiva e publicar
o app ("Publicar app" -> Em producao); ai o token passa a ser permanente. Enquanto
isso nao for feito, rode este script quando o painel acusar token expirado.

Uso:
  python3 renovar-token.py            # imprime a URL, pede o codigo, grava no .env
  python3 renovar-token.py --testar   # so verifica se o token atual ainda vale
"""
import json
import re
import sys
import urllib.parse
import urllib.request

ENV_PATH = "/data/coderush-sites/.env"
ESCOPO = "https://www.googleapis.com/auth/adwords"
REDIRECT = "urn:ietf:wg:oauth:2.0:oob"
TOKEN_URL = "https://oauth2.googleapis.com/token"


def ler_env():
    env, ordem = {}, []
    for line in open(ENV_PATH, encoding="utf-8"):
        ordem.append(line)
        t = line.strip()
        if t and not t.startswith("#") and "=" in t:
            k, v = t.split("=", 1)
            env[k.strip()] = v.strip().strip("\"'")
    return env, ordem


def gravar_refresh_token(novo):
    _, linhas = ler_env()
    achou = False
    for i, line in enumerate(linhas):
        if line.strip().startswith("GOOGLE_ADS_REFRESH_TOKEN="):
            linhas[i] = f"GOOGLE_ADS_REFRESH_TOKEN={novo}\n"
            achou = True
    if not achou:
        linhas.append(f"GOOGLE_ADS_REFRESH_TOKEN={novo}\n")
    with open(ENV_PATH, "w", encoding="utf-8") as f:
        f.writelines(linhas)
    print(f"  .env atualizado ({ENV_PATH})")

    # o yaml usado por alguns scripts carrega a mesma credencial
    yaml_path = "/root/.config/svd-ads/google-ads.yaml"
    try:
        conteudo = open(yaml_path, encoding="utf-8").read()
        conteudo = re.sub(r"refresh_token: .*", f"refresh_token: {novo}", conteudo)
        open(yaml_path, "w", encoding="utf-8").write(conteudo)
        print(f"  {yaml_path} atualizado")
    except FileNotFoundError:
        pass


def token_vale(env):
    dados = urllib.parse.urlencode({
        "client_id": env["GOOGLE_ADS_CLIENT_ID"],
        "client_secret": env["GOOGLE_ADS_CLIENT_SECRET"],
        "refresh_token": env["GOOGLE_ADS_REFRESH_TOKEN"],
        "grant_type": "refresh_token"}).encode()
    try:
        urllib.request.urlopen(urllib.request.Request(TOKEN_URL, data=dados))
        return True, "válido"
    except urllib.error.HTTPError as e:
        return False, json.loads(e.read()).get("error_description", "erro")


def main():
    env, _ = ler_env()
    for k in ("GOOGLE_ADS_CLIENT_ID", "GOOGLE_ADS_CLIENT_SECRET"):
        if not env.get(k):
            sys.exit(f"{k} ausente no .env")

    if "--testar" in sys.argv:
        ok, msg = token_vale(env)
        print(("TOKEN OK — " if ok else "TOKEN INVALIDO — ") + msg)
        sys.exit(0 if ok else 1)

    url = "https://accounts.google.com/o/oauth2/v2/auth?" + urllib.parse.urlencode({
        "client_id": env["GOOGLE_ADS_CLIENT_ID"],
        "redirect_uri": REDIRECT,
        "response_type": "code",
        "scope": ESCOPO,
        "access_type": "offline",
        "prompt": "consent"})  # prompt=consent forca vir refresh_token novo

    print("\n1) Abra esta URL logado como andre.kernelpanic@gmail.com:\n")
    print(url)
    print("\n2) Autorize e copie o codigo que aparecer na tela.\n")
    codigo = input("Cole o codigo aqui: ").strip()
    if codigo.startswith("http"):  # aceita a URL inteira colada
        q = urllib.parse.parse_qs(urllib.parse.urlparse(codigo).query)
        codigo = (q.get("code") or [""])[0]
    if not codigo:
        sys.exit("codigo vazio")

    dados = urllib.parse.urlencode({
        "code": codigo,
        "client_id": env["GOOGLE_ADS_CLIENT_ID"],
        "client_secret": env["GOOGLE_ADS_CLIENT_SECRET"],
        "redirect_uri": REDIRECT,
        "grant_type": "authorization_code"}).encode()
    try:
        resp = json.loads(urllib.request.urlopen(
            urllib.request.Request(TOKEN_URL, data=dados)).read())
    except urllib.error.HTTPError as e:
        sys.exit("falhou: " + e.read().decode()[:300])

    novo = resp.get("refresh_token")
    if not novo:
        sys.exit("o Google nao devolveu refresh_token — refaca com prompt=consent")
    print("\n  refresh token novo obtido")
    gravar_refresh_token(novo)

    env["GOOGLE_ADS_REFRESH_TOKEN"] = novo
    ok, msg = token_vale(env)
    print(("  validado: " + msg) if ok else ("  ATENCAO: " + msg))
    print("\nAgora rode: python3 automation/leads/ga-stats.py")


if __name__ == "__main__":
    main()
