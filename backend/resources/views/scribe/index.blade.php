<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Gerador de Relatorios API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://localhost:8000";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.11.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.11.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-autenticacao" class="tocify-header">
                <li class="tocify-item level-1" data-unique="autenticacao">
                    <a href="#autenticacao">Autenticação</a>
                </li>
                                    <ul id="tocify-subheader-autenticacao" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="autenticacao-POSTapi-login">
                                <a href="#autenticacao-POSTapi-login">Login</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="autenticacao-POSTapi-logout">
                                <a href="#autenticacao-POSTapi-logout">Logout</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="autenticacao-GETapi-me">
                                <a href="#autenticacao-GETapi-me">Usuário autenticado</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-clientes" class="tocify-header">
                <li class="tocify-item level-1" data-unique="clientes">
                    <a href="#clientes">Clientes</a>
                </li>
                                    <ul id="tocify-subheader-clientes" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="clientes-GETapi-customers">
                                <a href="#clientes-GETapi-customers">Listar clientes</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="clientes-POSTapi-customers">
                                <a href="#clientes-POSTapi-customers">Cadastrar cliente</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="clientes-GETapi-customers--id-">
                                <a href="#clientes-GETapi-customers--id-">Visualizar cliente</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="clientes-PUTapi-customers--id-">
                                <a href="#clientes-PUTapi-customers--id-">Editar cliente</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-cobrancas" class="tocify-header">
                <li class="tocify-item level-1" data-unique="cobrancas">
                    <a href="#cobrancas">Cobranças</a>
                </li>
                                    <ul id="tocify-subheader-cobrancas" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="cobrancas-GETapi-billings">
                                <a href="#cobrancas-GETapi-billings">Listar cobranças</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="cobrancas-POSTapi-billings">
                                <a href="#cobrancas-POSTapi-billings">Cadastrar cobrança</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="cobrancas-GETapi-billings--id-">
                                <a href="#cobrancas-GETapi-billings--id-">Visualizar cobrança</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="cobrancas-PUTapi-billings--id-">
                                <a href="#cobrancas-PUTapi-billings--id-">Editar cobrança</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="cobrancas-POSTapi-billings--billing_id--pay">
                                <a href="#cobrancas-POSTapi-billings--billing_id--pay">Registrar pagamento</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-relatorios" class="tocify-header">
                <li class="tocify-item level-1" data-unique="relatorios">
                    <a href="#relatorios">Relatórios</a>
                </li>
                                    <ul id="tocify-subheader-relatorios" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="relatorios-GETapi-reports-billing">
                                <a href="#relatorios-GETapi-reports-billing">Relatório de faturamento</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="relatorios-GETapi-reports-billing-export-csv">
                                <a href="#relatorios-GETapi-reports-billing-export-csv">Exportar relatório em CSV</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="relatorios-GETapi-reports-billing-export-pdf">
                                <a href="#relatorios-GETapi-reports-billing-export-pdf">Exportar relatório em PDF</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: September 18, 2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<p>API de faturamento: clientes, cobranças (com cálculo de juros em tempo real) e relatório de faturamento com exportação em CSV/PDF.</p>
<aside>
    <strong>Base URL</strong>: <code>http://localhost:8000</code>
</aside>
<pre><code>Todas as rotas, exceto `POST /login`, exigem um Bearer token no header
`Authorization` (obtido no login). Clique em **Authorization** no topo desta
página para colar um token e usar o "Try It Out" direto contra a API rodando.

&lt;aside&gt;As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>To authenticate requests, include an <strong><code>Authorization</code></strong> header with the value <strong><code>"Bearer {SEU_TOKEN}"</code></strong>.</p>
<p>All authenticated endpoints are marked with a <code>requires authentication</code> badge in the documentation below.</p>
<p>Obtenha um token fazendo login em <code>POST /login</code> com as credenciais do usuário de teste (ver README) — o token retornado vai no header <code>Authorization: Bearer {token}</code>.</p>

        <h1 id="autenticacao">Autenticação</h1>

    <p>Login, logout e dados do usuário autenticado. Todas as rotas deste grupo exigem Bearer token, exceto login.</p>

                                <h2 id="autenticacao-POSTapi-login">Login</h2>

<p>
</p>

<p>Autentica com email e senha e retorna um token de acesso (Bearer) a
ser enviado no header <code>Authorization</code> das demais rotas da API.</p>

<span id="example-requests-POSTapi-login">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/login" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"email\": \"gbailey@example.net\",
    \"password\": \"|]|{+-\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/login"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "email": "gbailey@example.net",
    "password": "|]|{+-"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-login">
</span>
<span id="execution-results-POSTapi-login" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-login"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-login"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-login" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-login">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-login" data-method="POST"
      data-path="api/login"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-login', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-login"
                    onclick="tryItOut('POSTapi-login');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-login"
                    onclick="cancelTryOut('POSTapi-login');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-login"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/login</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-login"
               value="gbailey@example.net"
               data-component="body">
    <br>
<p>Must be a valid email address. Example: <code>gbailey@example.net</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-login"
               value="|]|{+-"
               data-component="body">
    <br>
<p>Example: <code>|]|{+-</code></p>
        </div>
        </form>

                    <h2 id="autenticacao-POSTapi-logout">Logout</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Revoga o token de acesso usado na requisição.</p>

<span id="example-requests-POSTapi-logout">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/logout" \
    --header "Authorization: Bearer {SEU_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/logout"
);

const headers = {
    "Authorization": "Bearer {SEU_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-logout">
</span>
<span id="execution-results-POSTapi-logout" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-logout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-logout"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-logout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-logout">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-logout" data-method="POST"
      data-path="api/logout"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-logout', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-logout"
                    onclick="tryItOut('POSTapi-logout');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-logout"
                    onclick="cancelTryOut('POSTapi-logout');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-logout"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/logout</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-logout"
               value="Bearer {SEU_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {SEU_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="autenticacao-GETapi-me">Usuário autenticado</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna os dados básicos (id, nome, e-mail) do usuário dono do token
usado na requisição.</p>

<span id="example-requests-GETapi-me">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/me" \
    --header "Authorization: Bearer {SEU_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/me"
);

const headers = {
    "Authorization": "Bearer {SEU_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-me">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
access-control-expose-headers: Content-Disposition
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 1,
    &quot;name&quot;: &quot;Admin Teste&quot;,
    &quot;email&quot;: &quot;admin@teste.com&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-me" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-me"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-me"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-me" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-me">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-me" data-method="GET"
      data-path="api/me"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-me', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-me"
                    onclick="tryItOut('GETapi-me');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-me"
                    onclick="cancelTryOut('GETapi-me');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-me"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/me</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-me"
               value="Bearer {SEU_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {SEU_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-me"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-me"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="clientes">Clientes</h1>

    <p>Cadastro, edição, listagem e visualização de clientes. Todas as rotas exigem Bearer token.</p>

                                <h2 id="clientes-GETapi-customers">Listar clientes</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Listagem paginada no backend, com filtro por status e busca textual
(nome, documento ou e-mail).</p>

<span id="example-requests-GETapi-customers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/customers?status=active&amp;search=Maria&amp;sort_by=name&amp;sort_direction=asc&amp;page=1&amp;per_page=15" \
    --header "Authorization: Bearer {SEU_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/customers"
);

const params = {
    "status": "active",
    "search": "Maria",
    "sort_by": "name",
    "sort_direction": "asc",
    "page": "1",
    "per_page": "15",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {SEU_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-customers">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
access-control-expose-headers: Content-Disposition
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 880,
            &quot;name&quot;: &quot;Dr. Mariana Gil Rivera&quot;,
            &quot;document&quot;: &quot;689.166.559-00&quot;,
            &quot;email&quot;: &quot;matos.rayane@example.com&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;created_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;
        },
        {
            &quot;id&quot;: 720,
            &quot;name&quot;: &quot;Edilson Molina Filho&quot;,
            &quot;document&quot;: &quot;27.196.767/0001-94&quot;,
            &quot;email&quot;: &quot;maria06@example.com&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;created_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;
        },
        {
            &quot;id&quot;: 885,
            &quot;name&quot;: &quot;J&eacute;ssica Mariana Carmona Jr.&quot;,
            &quot;document&quot;: &quot;65.609.382/0001-09&quot;,
            &quot;email&quot;: &quot;sergio.camacho@example.org&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;created_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;
        },
        {
            &quot;id&quot;: 580,
            &quot;name&quot;: &quot;Kauan Gustavo Martines Jr.&quot;,
            &quot;document&quot;: &quot;504.340.289-08&quot;,
            &quot;email&quot;: &quot;mariah41@example.org&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;created_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;
        },
        {
            &quot;id&quot;: 646,
            &quot;name&quot;: &quot;Malena Maria Medina&quot;,
            &quot;document&quot;: &quot;737.578.738-74&quot;,
            &quot;email&quot;: &quot;marcio33@example.net&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;created_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;
        },
        {
            &quot;id&quot;: 787,
            &quot;name&quot;: &quot;Maria Viviane Molina&quot;,
            &quot;document&quot;: &quot;46.690.964/0001-00&quot;,
            &quot;email&quot;: &quot;luna.galhardo@example.net&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;created_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;
        },
        {
            &quot;id&quot;: 42,
            &quot;name&quot;: &quot;Mariana Faro Pedrosa&quot;,
            &quot;document&quot;: &quot;38.042.228/0001-17&quot;,
            &quot;email&quot;: &quot;nmarques@example.net&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;created_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;
        },
        {
            &quot;id&quot;: 159,
            &quot;name&quot;: &quot;Santiago Santiago Espinoza&quot;,
            &quot;document&quot;: &quot;47.570.782/0001-68&quot;,
            &quot;email&quot;: &quot;maria52@example.net&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;created_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;
        },
        {
            &quot;id&quot;: 613,
            &quot;name&quot;: &quot;Sra. Talita Giovanna Rosa&quot;,
            &quot;document&quot;: &quot;84.264.757/0001-82&quot;,
            &quot;email&quot;: &quot;serna.mariana@example.com&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;created_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;
        },
        {
            &quot;id&quot;: 910,
            &quot;name&quot;: &quot;Srta. Emily Barros Solano&quot;,
            &quot;document&quot;: &quot;37.646.221/0001-41&quot;,
            &quot;email&quot;: &quot;mendes.mariah@example.net&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;created_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;
        },
        {
            &quot;id&quot;: 236,
            &quot;name&quot;: &quot;Srta. Mariah Fabiana Matias Filho&quot;,
            &quot;document&quot;: &quot;139.364.731-61&quot;,
            &quot;email&quot;: &quot;agustina.valencia@example.com&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;created_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;
        },
        {
            &quot;id&quot;: 211,
            &quot;name&quot;: &quot;Wesley Dante Lozano&quot;,
            &quot;document&quot;: &quot;908.325.708-85&quot;,
            &quot;email&quot;: &quot;mariana45@example.com&quot;,
            &quot;status&quot;: &quot;active&quot;,
            &quot;created_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;http://localhost:8000/api/customers?status=active&amp;search=Maria&amp;sort_by=name&amp;sort_direction=asc&amp;per_page=15&amp;page=1&quot;,
        &quot;last&quot;: &quot;http://localhost:8000/api/customers?status=active&amp;search=Maria&amp;sort_by=name&amp;sort_direction=asc&amp;per_page=15&amp;page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;http://localhost:8000/api/customers?status=active&amp;search=Maria&amp;sort_by=name&amp;sort_direction=asc&amp;per_page=15&amp;page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;page&quot;: 1,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;http://localhost:8000/api/customers&quot;,
        &quot;per_page&quot;: 15,
        &quot;to&quot;: 12,
        &quot;total&quot;: 12
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-customers" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-customers"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-customers"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-customers" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-customers">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-customers" data-method="GET"
      data-path="api/customers"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-customers', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-customers"
                    onclick="tryItOut('GETapi-customers');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-customers"
                    onclick="cancelTryOut('GETapi-customers');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-customers"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/customers</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-customers"
               value="Bearer {SEU_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {SEU_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-customers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-customers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="GETapi-customers"
               value="active"
               data-component="query">
    <br>
<p>Filtra por status. Example: <code>active</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>active</code></li> <li><code>inactive</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>search</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="search"                data-endpoint="GETapi-customers"
               value="Maria"
               data-component="query">
    <br>
<p>Busca por nome, documento ou e-mail (contém o termo). Example: <code>Maria</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sort_by</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="sort_by"                data-endpoint="GETapi-customers"
               value="name"
               data-component="query">
    <br>
<p>Coluna de ordenação. Example: <code>name</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>id</code></li> <li><code>name</code></li> <li><code>status</code></li> <li><code>created_at</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sort_direction</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="sort_direction"                data-endpoint="GETapi-customers"
               value="asc"
               data-component="query">
    <br>
<p>Example: <code>asc</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>asc</code></li> <li><code>desc</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-customers"
               value="1"
               data-component="query">
    <br>
<p>Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-customers"
               value="15"
               data-component="query">
    <br>
<p>Itens por página (máximo 100). Example: <code>15</code></p>
            </div>
                </form>

                    <h2 id="clientes-POSTapi-customers">Cadastrar cliente</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-customers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/customers" \
    --header "Authorization: Bearer {SEU_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"b\",
    \"document\": \"123.456.789-00\",
    \"email\": \"ashly64@example.com\",
    \"status\": \"inactive\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/customers"
);

const headers = {
    "Authorization": "Bearer {SEU_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "b",
    "document": "123.456.789-00",
    "email": "ashly64@example.com",
    "status": "inactive"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-customers">
</span>
<span id="execution-results-POSTapi-customers" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-customers"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-customers"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-customers" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-customers">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-customers" data-method="POST"
      data-path="api/customers"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-customers', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-customers"
                    onclick="tryItOut('POSTapi-customers');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-customers"
                    onclick="cancelTryOut('POSTapi-customers');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-customers"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/customers</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-customers"
               value="Bearer {SEU_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {SEU_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-customers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-customers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-customers"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>document</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="document"                data-endpoint="POSTapi-customers"
               value="123.456.789-00"
               data-component="body">
    <br>
<p>CPF ou CNPJ do cliente. O valor é salvo exatamente como enviado (a API não normaliza formato nem valida dígito verificador) — os clientes já cadastrados usam pontuação, então o mesmo formato é recomendado para manter consistência. Example: <code>123.456.789-00</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-customers"
               value="ashly64@example.com"
               data-component="body">
    <br>
<p>Must be a valid email address. Must not be greater than 255 characters. Example: <code>ashly64@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="POSTapi-customers"
               value="inactive"
               data-component="body">
    <br>
<p>Example: <code>inactive</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>active</code></li> <li><code>inactive</code></li></ul>
        </div>
        </form>

                    <h2 id="clientes-GETapi-customers--id-">Visualizar cliente</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-customers--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/customers/1" \
    --header "Authorization: Bearer {SEU_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/customers/1"
);

const headers = {
    "Authorization": "Bearer {SEU_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-customers--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
access-control-expose-headers: Content-Disposition
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;Dr. Alonso Hernani Serrano&quot;,
        &quot;document&quot;: &quot;84.695.889/0001-69&quot;,
        &quot;email&quot;: &quot;ddearruda@example.org&quot;,
        &quot;status&quot;: &quot;active&quot;,
        &quot;created_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-09-18T07:23:06.000000Z&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-customers--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-customers--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-customers--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-customers--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-customers--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-customers--id-" data-method="GET"
      data-path="api/customers/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-customers--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-customers--id-"
                    onclick="tryItOut('GETapi-customers--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-customers--id-"
                    onclick="cancelTryOut('GETapi-customers--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-customers--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/customers/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-customers--id-"
               value="Bearer {SEU_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {SEU_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-customers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-customers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-customers--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the customer. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="clientes-PUTapi-customers--id-">Editar cliente</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-PUTapi-customers--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost:8000/api/customers/1" \
    --header "Authorization: Bearer {SEU_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"b\",
    \"document\": \"123.456.789-00\",
    \"email\": \"ashly64@example.com\",
    \"status\": \"active\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/customers/1"
);

const headers = {
    "Authorization": "Bearer {SEU_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "b",
    "document": "123.456.789-00",
    "email": "ashly64@example.com",
    "status": "active"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-customers--id-">
</span>
<span id="execution-results-PUTapi-customers--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-customers--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-customers--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-customers--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-customers--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-customers--id-" data-method="PUT"
      data-path="api/customers/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-customers--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-customers--id-"
                    onclick="tryItOut('PUTapi-customers--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-customers--id-"
                    onclick="cancelTryOut('PUTapi-customers--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-customers--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/customers/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/customers/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-customers--id-"
               value="Bearer {SEU_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {SEU_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-customers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-customers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PUTapi-customers--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the customer. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="PUTapi-customers--id-"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>document</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="document"                data-endpoint="PUTapi-customers--id-"
               value="123.456.789-00"
               data-component="body">
    <br>
<p>CPF ou CNPJ do cliente. O valor é salvo exatamente como enviado (a API não normaliza formato nem valida dígito verificador) — os clientes já cadastrados usam pontuação, então o mesmo formato é recomendado para manter consistência. Example: <code>123.456.789-00</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="PUTapi-customers--id-"
               value="ashly64@example.com"
               data-component="body">
    <br>
<p>Must be a valid email address. Must not be greater than 255 characters. Example: <code>ashly64@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="PUTapi-customers--id-"
               value="active"
               data-component="body">
    <br>
<p>Example: <code>active</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>active</code></li> <li><code>inactive</code></li></ul>
        </div>
        </form>

                <h1 id="cobrancas">Cobranças</h1>

    <p>Cadastro, edição, listagem, visualização e registro de pagamento de cobranças. Todas as rotas exigem Bearer token.</p>

                                <h2 id="cobrancas-GETapi-billings">Listar cobranças</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Listagem paginada no backend, com filtro por cliente e status. Cada
item já inclui os campos calculados de juros/valor atualizado (ver
grupo Relatórios para a mesma lógica aplicada ao relatório completo).</p>

<span id="example-requests-GETapi-billings">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/billings?customer_id=1&amp;status=overdue&amp;sort_by=due_date&amp;sort_direction=asc&amp;page=1&amp;per_page=15" \
    --header "Authorization: Bearer {SEU_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/billings"
);

const params = {
    "customer_id": "1",
    "status": "overdue",
    "sort_by": "due_date",
    "sort_direction": "asc",
    "page": "1",
    "per_page": "15",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {SEU_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-billings">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
access-control-expose-headers: Content-Disposition
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;http://localhost:8000/api/billings?customer_id=1&amp;status=overdue&amp;sort_by=due_date&amp;sort_direction=asc&amp;per_page=15&amp;page=1&quot;,
        &quot;last&quot;: &quot;http://localhost:8000/api/billings?customer_id=1&amp;status=overdue&amp;sort_by=due_date&amp;sort_direction=asc&amp;per_page=15&amp;page=1&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: null
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: null,
        &quot;last_page&quot;: 1,
        &quot;links&quot;: [
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            },
            {
                &quot;url&quot;: &quot;http://localhost:8000/api/billings?customer_id=1&amp;status=overdue&amp;sort_by=due_date&amp;sort_direction=asc&amp;per_page=15&amp;page=1&quot;,
                &quot;label&quot;: &quot;1&quot;,
                &quot;page&quot;: 1,
                &quot;active&quot;: true
            },
            {
                &quot;url&quot;: null,
                &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
                &quot;page&quot;: null,
                &quot;active&quot;: false
            }
        ],
        &quot;path&quot;: &quot;http://localhost:8000/api/billings&quot;,
        &quot;per_page&quot;: 15,
        &quot;to&quot;: null,
        &quot;total&quot;: 0
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-billings" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-billings"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-billings"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-billings" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-billings">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-billings" data-method="GET"
      data-path="api/billings"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-billings', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-billings"
                    onclick="tryItOut('GETapi-billings');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-billings"
                    onclick="cancelTryOut('GETapi-billings');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-billings"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/billings</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-billings"
               value="Bearer {SEU_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {SEU_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-billings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-billings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>customer_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="customer_id"                data-endpoint="GETapi-billings"
               value="1"
               data-component="query">
    <br>
<p>Filtra pelas cobranças de um cliente. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="GETapi-billings"
               value="overdue"
               data-component="query">
    <br>
<p>Example: <code>overdue</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>pending</code></li> <li><code>overdue</code></li> <li><code>paid</code></li> <li><code>cancelled</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sort_by</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="sort_by"                data-endpoint="GETapi-billings"
               value="due_date"
               data-component="query">
    <br>
<p>Example: <code>due_date</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>due_date</code></li> <li><code>issue_date</code></li> <li><code>status</code></li> <li><code>created_at</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sort_direction</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="sort_direction"                data-endpoint="GETapi-billings"
               value="asc"
               data-component="query">
    <br>
<p>Example: <code>asc</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>asc</code></li> <li><code>desc</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-billings"
               value="1"
               data-component="query">
    <br>
<p>Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-billings"
               value="15"
               data-component="query">
    <br>
<p>Itens por página (máximo 100). Example: <code>15</code></p>
            </div>
                </form>

                    <h2 id="cobrancas-POSTapi-billings">Cadastrar cobrança</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Toda cobrança nova começa com status <code>pending</code> — não é possível criar
já como <code>paid</code>/<code>overdue</code>/<code>cancelled</code> via este endpoint.</p>

<span id="example-requests-POSTapi-billings">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/billings" \
    --header "Authorization: Bearer {SEU_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"customer_id\": 16,
    \"description\": \"Et animi quos velit et fugiat.\",
    \"original_amount\": 42,
    \"issue_date\": \"2026-09-18T08:23:18\",
    \"due_date\": \"2052-10-11\",
    \"monthly_interest_rate\": 2.5
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/billings"
);

const headers = {
    "Authorization": "Bearer {SEU_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "customer_id": 16,
    "description": "Et animi quos velit et fugiat.",
    "original_amount": 42,
    "issue_date": "2026-09-18T08:23:18",
    "due_date": "2052-10-11",
    "monthly_interest_rate": 2.5
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-billings">
</span>
<span id="execution-results-POSTapi-billings" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-billings"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-billings"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-billings" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-billings">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-billings" data-method="POST"
      data-path="api/billings"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-billings', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-billings"
                    onclick="tryItOut('POSTapi-billings');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-billings"
                    onclick="cancelTryOut('POSTapi-billings');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-billings"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/billings</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-billings"
               value="Bearer {SEU_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {SEU_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-billings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-billings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>customer_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="customer_id"                data-endpoint="POSTapi-billings"
               value="16"
               data-component="body">
    <br>
<p>Must match an existing stored value. Example: <code>16</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="description"                data-endpoint="POSTapi-billings"
               value="Et animi quos velit et fugiat."
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>Et animi quos velit et fugiat.</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>original_amount</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="original_amount"                data-endpoint="POSTapi-billings"
               value="42"
               data-component="body">
    <br>
<p>Must be at least 0.01. Example: <code>42</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>issue_date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="issue_date"                data-endpoint="POSTapi-billings"
               value="2026-09-18T08:23:18"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-09-18T08:23:18</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>due_date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="due_date"                data-endpoint="POSTapi-billings"
               value="2052-10-11"
               data-component="body">
    <br>
<p>Must be a valid date. Must be a date after or equal to <code>issue_date</code>. Example: <code>2052-10-11</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>monthly_interest_rate</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="monthly_interest_rate"                data-endpoint="POSTapi-billings"
               value="2.5"
               data-component="body">
    <br>
<p>Taxa de juros mensal em percentual — 2.5 representa 2,5% ao mês, não a fração decimal 0.025. Example: <code>2.5</code></p>
        </div>
        </form>

                    <h2 id="cobrancas-GETapi-billings--id-">Visualizar cobrança</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Inclui os campos calculados de juros/valor atualizado em tempo real
(nunca lidos de coluna persistida).</p>

<span id="example-requests-GETapi-billings--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/billings/1" \
    --header "Authorization: Bearer {SEU_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/billings/1"
);

const headers = {
    "Authorization": "Bearer {SEU_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-billings--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
access-control-expose-headers: Content-Disposition
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;customer_id&quot;: 373,
        &quot;customer&quot;: {
            &quot;id&quot;: 373,
            &quot;name&quot;: &quot;Miranda Isabella &Aacute;vila&quot;
        },
        &quot;description&quot;: &quot;Fatura de consultoria&quot;,
        &quot;original_amount&quot;: 4363.34,
        &quot;issue_date&quot;: &quot;2026-08-22&quot;,
        &quot;due_date&quot;: &quot;2026-09-24&quot;,
        &quot;payment_date&quot;: null,
        &quot;monthly_interest_rate&quot;: 2.48,
        &quot;status&quot;: &quot;pending&quot;,
        &quot;paid_amount&quot;: null,
        &quot;interest_amount_at_payment&quot;: null,
        &quot;interest_amount&quot;: 0,
        &quot;updated_amount&quot;: 4363.34,
        &quot;days_overdue&quot;: 0,
        &quot;created_at&quot;: &quot;2026-09-18T00:00:00.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-09-18T00:00:00.000000Z&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-billings--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-billings--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-billings--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-billings--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-billings--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-billings--id-" data-method="GET"
      data-path="api/billings/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-billings--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-billings--id-"
                    onclick="tryItOut('GETapi-billings--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-billings--id-"
                    onclick="cancelTryOut('GETapi-billings--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-billings--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/billings/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-billings--id-"
               value="Bearer {SEU_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {SEU_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-billings--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-billings--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-billings--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the billing. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="cobrancas-PUTapi-billings--id-">Editar cobrança</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retorna 422 se a cobrança já estiver paga — uma cobrança paga não
pode ser editada.</p>

<span id="example-requests-PUTapi-billings--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost:8000/api/billings/1" \
    --header "Authorization: Bearer {SEU_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"customer_id\": 16,
    \"description\": \"Et animi quos velit et fugiat.\",
    \"original_amount\": 42,
    \"issue_date\": \"2026-09-18T08:23:18\",
    \"due_date\": \"2052-10-11\",
    \"monthly_interest_rate\": 2.5
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/billings/1"
);

const headers = {
    "Authorization": "Bearer {SEU_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "customer_id": 16,
    "description": "Et animi quos velit et fugiat.",
    "original_amount": 42,
    "issue_date": "2026-09-18T08:23:18",
    "due_date": "2052-10-11",
    "monthly_interest_rate": 2.5
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-billings--id-">
</span>
<span id="execution-results-PUTapi-billings--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-billings--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-billings--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-billings--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-billings--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-billings--id-" data-method="PUT"
      data-path="api/billings/{id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-billings--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-billings--id-"
                    onclick="tryItOut('PUTapi-billings--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-billings--id-"
                    onclick="cancelTryOut('PUTapi-billings--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-billings--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/billings/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/billings/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-billings--id-"
               value="Bearer {SEU_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {SEU_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-billings--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-billings--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="PUTapi-billings--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the billing. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>customer_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="customer_id"                data-endpoint="PUTapi-billings--id-"
               value="16"
               data-component="body">
    <br>
<p>Must match an existing stored value. Example: <code>16</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="description"                data-endpoint="PUTapi-billings--id-"
               value="Et animi quos velit et fugiat."
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>Et animi quos velit et fugiat.</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>original_amount</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="original_amount"                data-endpoint="PUTapi-billings--id-"
               value="42"
               data-component="body">
    <br>
<p>Must be at least 0.01. Example: <code>42</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>issue_date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="issue_date"                data-endpoint="PUTapi-billings--id-"
               value="2026-09-18T08:23:18"
               data-component="body">
    <br>
<p>Must be a valid date. Example: <code>2026-09-18T08:23:18</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>due_date</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="due_date"                data-endpoint="PUTapi-billings--id-"
               value="2052-10-11"
               data-component="body">
    <br>
<p>Must be a valid date. Must be a date after or equal to <code>issue_date</code>. Example: <code>2052-10-11</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>monthly_interest_rate</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="monthly_interest_rate"                data-endpoint="PUTapi-billings--id-"
               value="2.5"
               data-component="body">
    <br>
<p>Taxa de juros mensal em percentual — 2.5 representa 2,5% ao mês, não a fração decimal 0.025. Example: <code>2.5</code></p>
        </div>
        </form>

                    <h2 id="cobrancas-POSTapi-billings--billing_id--pay">Registrar pagamento</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Não aceita nenhum campo no corpo da requisição: a data de pagamento é
sempre <code>now()</code> e o valor pago é sempre o valor atualizado calculado no
momento do registro — nunca dados enviados pelo client. Retorna 422 se
a cobrança já estiver paga ou cancelada.</p>

<span id="example-requests-POSTapi-billings--billing_id--pay">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost:8000/api/billings/1/pay" \
    --header "Authorization: Bearer {SEU_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/billings/1/pay"
);

const headers = {
    "Authorization": "Bearer {SEU_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-billings--billing_id--pay">
</span>
<span id="execution-results-POSTapi-billings--billing_id--pay" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-billings--billing_id--pay"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-billings--billing_id--pay"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-billings--billing_id--pay" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-billings--billing_id--pay">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-billings--billing_id--pay" data-method="POST"
      data-path="api/billings/{billing_id}/pay"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-billings--billing_id--pay', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-billings--billing_id--pay"
                    onclick="tryItOut('POSTapi-billings--billing_id--pay');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-billings--billing_id--pay"
                    onclick="cancelTryOut('POSTapi-billings--billing_id--pay');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-billings--billing_id--pay"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/billings/{billing_id}/pay</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-billings--billing_id--pay"
               value="Bearer {SEU_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {SEU_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-billings--billing_id--pay"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-billings--billing_id--pay"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>billing_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="billing_id"                data-endpoint="POSTapi-billings--billing_id--pay"
               value="1"
               data-component="url">
    <br>
<p>The ID of the billing. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="relatorios">Relatórios</h1>

    <p>Relatório de faturamento por período, com totalizadores e exportação em CSV/PDF. Todas as rotas exigem Bearer token.</p>

                                <h2 id="relatorios-GETapi-reports-billing">Relatório de faturamento</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Listagem paginada de cobranças com totalizadores sobre o conjunto
completo filtrado (não só a página atual). <code>date_from</code>/<code>date_to</code> só
são aplicados quando os dois vierem juntos e forem datas válidas —
um deles sozinho, ou inválido, é tratado como "sem filtro de período"
(não gera erro 422).</p>

<span id="example-requests-GETapi-reports-billing">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/reports/billing?date_from=2026-01-01&amp;date_to=2026-06-30&amp;date_field=due_date&amp;customer_id=1&amp;status=overdue&amp;sort_by=due_date&amp;sort_direction=asc&amp;page=1&amp;per_page=15" \
    --header "Authorization: Bearer {SEU_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/reports/billing"
);

const params = {
    "date_from": "2026-01-01",
    "date_to": "2026-06-30",
    "date_field": "due_date",
    "customer_id": "1",
    "status": "overdue",
    "sort_by": "due_date",
    "sort_direction": "asc",
    "page": "1",
    "per_page": "15",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {SEU_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-reports-billing">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: *
access-control-expose-headers: Content-Disposition
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [],
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: null,
        &quot;last_page&quot;: 1,
        &quot;path&quot;: &quot;http://localhost:8000/api/reports/billing&quot;,
        &quot;per_page&quot;: 15,
        &quot;to&quot;: null,
        &quot;total&quot;: 0
    },
    &quot;totals&quot;: {
        &quot;count&quot;: 0,
        &quot;original_amount_total&quot;: 0,
        &quot;interest_total&quot;: 0,
        &quot;updated_amount_total&quot;: 0,
        &quot;paid_total&quot;: 0,
        &quot;pending_total&quot;: 0
    },
    &quot;filters_applied&quot;: {
        &quot;date_from&quot;: &quot;2026-01-01&quot;,
        &quot;date_to&quot;: &quot;2026-06-30&quot;,
        &quot;date_field&quot;: &quot;due_date&quot;,
        &quot;customer_id&quot;: 1,
        &quot;status&quot;: &quot;overdue&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-reports-billing" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-reports-billing"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-reports-billing"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-reports-billing" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-reports-billing">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-reports-billing" data-method="GET"
      data-path="api/reports/billing"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-reports-billing', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-reports-billing"
                    onclick="tryItOut('GETapi-reports-billing');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-reports-billing"
                    onclick="cancelTryOut('GETapi-reports-billing');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-reports-billing"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/reports/billing</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-reports-billing"
               value="Bearer {SEU_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {SEU_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-reports-billing"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-reports-billing"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>date_from</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date_from"                data-endpoint="GETapi-reports-billing"
               value="2026-01-01"
               data-component="query">
    <br>
<p>Início do período (formato YYYY-MM-DD). Precisa vir junto com date_to. Example: <code>2026-01-01</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>date_to</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date_to"                data-endpoint="GETapi-reports-billing"
               value="2026-06-30"
               data-component="query">
    <br>
<p>Fim do período (formato YYYY-MM-DD). Precisa vir junto com date_from. Example: <code>2026-06-30</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>date_field</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date_field"                data-endpoint="GETapi-reports-billing"
               value="due_date"
               data-component="query">
    <br>
<p>Qual data usar para o filtro de período acima. <code>issue_date</code> = data de emissão da cobrança; <code>due_date</code> = data de vencimento (padrão); <code>payment_date</code> = data em que o pagamento foi registrado (só existe para cobranças pagas — filtrar por payment_date exclui automaticamente as demais). Example: <code>due_date</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>issue_date</code></li> <li><code>due_date</code></li> <li><code>payment_date</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>customer_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="customer_id"                data-endpoint="GETapi-reports-billing"
               value="1"
               data-component="query">
    <br>
<p>Filtra pelas cobranças de um cliente. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="GETapi-reports-billing"
               value="overdue"
               data-component="query">
    <br>
<p>Example: <code>overdue</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>pending</code></li> <li><code>overdue</code></li> <li><code>paid</code></li> <li><code>cancelled</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sort_by</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="sort_by"                data-endpoint="GETapi-reports-billing"
               value="due_date"
               data-component="query">
    <br>
<p>Ordenação da listagem paginada (não afeta os totalizadores, que somam sempre o conjunto completo). Example: <code>due_date</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>due_date</code></li> <li><code>issue_date</code></li> <li><code>status</code></li> <li><code>created_at</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sort_direction</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="sort_direction"                data-endpoint="GETapi-reports-billing"
               value="asc"
               data-component="query">
    <br>
<p>Example: <code>asc</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>asc</code></li> <li><code>desc</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-reports-billing"
               value="1"
               data-component="query">
    <br>
<p>Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-reports-billing"
               value="15"
               data-component="query">
    <br>
<p>Itens por página (máximo 100). Example: <code>15</code></p>
            </div>
                </form>

                    <h2 id="relatorios-GETapi-reports-billing-export-csv">Exportar relatório em CSV</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Mesmos filtros do endpoint de listagem (<code>date_from</code>, <code>date_to</code>,
<code>date_field</code>, <code>customer_id</code>, <code>status</code>) aplicados ao conjunto completo,
não só a uma página — <code>sort_by</code>/<code>sort_direction</code>/<code>page</code> não se aplicam
aqui e são ignorados se enviados. Streaming: retorna o arquivo direto,
não JSON (<code>Content-Disposition: attachment</code>).</p>

<span id="example-requests-GETapi-reports-billing-export-csv">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/reports/billing/export/csv?date_from=2026-01-01&amp;date_to=2026-06-30&amp;date_field=due_date&amp;customer_id=1&amp;status=overdue" \
    --header "Authorization: Bearer {SEU_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/reports/billing/export/csv"
);

const params = {
    "date_from": "2026-01-01",
    "date_to": "2026-06-30",
    "date_field": "due_date",
    "customer_id": "1",
    "status": "overdue",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {SEU_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-reports-billing-export-csv">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">content-type: text/csv; charset=UTF-8
cache-control: no-cache, private
content-disposition: attachment; filename=relatorio-faturamento_2026-01-01_a_2026-06-30.csv
access-control-allow-origin: *
access-control-expose-headers: Content-Disposition
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">﻿&quot;Relat&oacute;rio de Faturamento&quot;
&quot;Per&iacute;odo (Vencimento): 2026-01-01 a 2026-06-30&quot;
&quot;Cliente: Dr. Alonso Hernani Serrano&quot;
&quot;Status: Vencida&quot;
&quot;Gerado em: 18/09/2026 08:23&quot;

Cliente,Descri&ccedil;&atilde;o,Emiss&atilde;o,Vencimento,Pagamento,Status,&quot;Valor Original&quot;,Juros,&quot;Valor Atualizado&quot;,&quot;Valor Pago&quot;

Totalizadores
&quot;Quantidade de cobran&ccedil;as&quot;,0
&quot;Valor original total&quot;,&quot;0,00&quot;
&quot;Total de juros&quot;,&quot;0,00&quot;
&quot;Valor atualizado total&quot;,&quot;0,00&quot;
&quot;Valor total recebido&quot;,&quot;0,00&quot;
&quot;Valor total pendente&quot;,&quot;0,00&quot;
</code>
 </pre>
    </span>
<span id="execution-results-GETapi-reports-billing-export-csv" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-reports-billing-export-csv"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-reports-billing-export-csv"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-reports-billing-export-csv" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-reports-billing-export-csv">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-reports-billing-export-csv" data-method="GET"
      data-path="api/reports/billing/export/csv"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-reports-billing-export-csv', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-reports-billing-export-csv"
                    onclick="tryItOut('GETapi-reports-billing-export-csv');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-reports-billing-export-csv"
                    onclick="cancelTryOut('GETapi-reports-billing-export-csv');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-reports-billing-export-csv"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/reports/billing/export/csv</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-reports-billing-export-csv"
               value="Bearer {SEU_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {SEU_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-reports-billing-export-csv"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-reports-billing-export-csv"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>date_from</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date_from"                data-endpoint="GETapi-reports-billing-export-csv"
               value="2026-01-01"
               data-component="query">
    <br>
<p>Precisa vir junto com date_to. Example: <code>2026-01-01</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>date_to</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date_to"                data-endpoint="GETapi-reports-billing-export-csv"
               value="2026-06-30"
               data-component="query">
    <br>
<p>Precisa vir junto com date_from. Example: <code>2026-06-30</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>date_field</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date_field"                data-endpoint="GETapi-reports-billing-export-csv"
               value="due_date"
               data-component="query">
    <br>
<p>Example: <code>due_date</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>issue_date</code></li> <li><code>due_date</code></li> <li><code>payment_date</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>customer_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="customer_id"                data-endpoint="GETapi-reports-billing-export-csv"
               value="1"
               data-component="query">
    <br>
<p>Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="GETapi-reports-billing-export-csv"
               value="overdue"
               data-component="query">
    <br>
<p>Example: <code>overdue</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>pending</code></li> <li><code>overdue</code></li> <li><code>paid</code></li> <li><code>cancelled</code></li></ul>
            </div>
                </form>

                    <h2 id="relatorios-GETapi-reports-billing-export-pdf">Exportar relatório em PDF</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Mesmos filtros da exportação em CSV. Antes de gerar, conta quantos
registros o filtro retorna (sem carregá-los); se exceder o limite
configurado (<code>config('reports.pdf_row_limit')</code>, default 500 — ver
README para a justificativa medida), retorna 422 sugerindo usar CSV.</p>

<span id="example-requests-GETapi-reports-billing-export-pdf">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost:8000/api/reports/billing/export/pdf?date_from=2026-01-01&amp;date_to=2026-06-30&amp;date_field=due_date&amp;customer_id=1&amp;status=overdue" \
    --header "Authorization: Bearer {SEU_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost:8000/api/reports/billing/export/pdf"
);

const params = {
    "date_from": "2026-01-01",
    "date_to": "2026-06-30",
    "date_field": "due_date",
    "customer_id": "1",
    "status": "overdue",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer {SEU_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-reports-billing-export-pdf">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">content-type: application/pdf
content-disposition: attachment; filename=relatorio-faturamento_2026-01-01_a_2026-06-30.pdf
cache-control: no-cache, private
access-control-allow-origin: *
access-control-expose-headers: Content-Disposition
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">%PDF-1.7
1 0 obj
&lt;&lt; /Type /Catalog
/Outlines 2 0 R
/Pages 3 0 R &gt;&gt;
endobj
2 0 obj
&lt;&lt; /Type /Outlines /Count 0 &gt;&gt;
endobj
3 0 obj
&lt;&lt; /Type /Pages
/Kids [6 0 R
]
/Count 1
/Resources &lt;&lt;
/ProcSet 4 0 R
/Font &lt;&lt; 
/F1 8 0 R
/F2 9 0 R
&gt;&gt;
&gt;&gt;
/MediaBox [0.000 0.000 841.890 595.280]
 &gt;&gt;
endobj
4 0 obj
[/PDF /Text ]
endobj
5 0 obj
&lt;&lt;
/Producer (�� d o m p d f   3 . 1 . 6   +   C P D F)
/CreationDate (D:20260918082320+00&#039;00&#039;)
/ModDate (D:20260918082320+00&#039;00&#039;)
/Title (�� R e l a t � r i o   d e   F a t u r a m e n t o)
&gt;&gt;
endobj
6 0 obj
&lt;&lt; /Type /Page
/MediaBox [0.000 0.000 841.890 595.280]
/Parent 3 0 R
/Contents 7 0 R
&gt;&gt;
endobj
7 0 obj
&lt;&lt; /Filter /FlateDecode
/Length 1432 &gt;&gt;
stream
x��X�nG��+�hq��E7&#039;^�&quot;r�}�cej�R�������&amp;{��&Aacute;��
�k�{�]]�+F9�W?��T�qC��T3IVkr��.(#�/�|x���4���n 떼ivOc����&#039;��%�WW�
c	��3C�Q�̂8*b�w����g���}�O��L���i��d�y(5��+v���M���j���f�������];�M?�J�4����ہ����\7&#039;�*���&#039;D�m�4��7��k毃 �܍��@�8��` $�C�n�zm	7�YIƖ|�e�1�?߿����/��-�@�&#039;���J���)�H��
�������B�
��FS��svy�:��򴅎�1�7q�z��J�J���
�dp�Ѯ�i�wHG
,���@ᜎW�~�3�+ɴ��幨�pc��CZ�4Tr_i	���HL�,P&iacute;�&lt;�8&#039;�c��^�yE13�b��T��H!�ӢP^)$&amp;G�
vx�qNLi!�U��� �*=�\�(f�����R���&quot;K��pT��k���	9�Yj@b9�\%�pꝩ���XN�,��ྐrF&Rho;�^ВhE-��5����DZ�������ḙ��-��M(�Z�F���=t}��ה�EM�S�|�)@��1���M�9�O�,����iѷO�p�&lt;�U�&sup1;�\�^�8�b�g\WZJ�/��%Gh1pra/�����t��;/+L�&oacute;�
Y���H��ΪJU@F��A��J�=B���V{
��%��&lt;��t��&gt;�8��x��1�=\�f�y�eTM�!�4]����j��P��J܆K��J�B��+qD^MV&quot;�5]�{P�5]�P8�d%&gt;�(�d%B�\ӕ��\ӕ�rMW�_rMW&quot;�5]���5]�P��it=��2�*G��fP� n��&#039;�x7���xzl��^+�5����ߐ��ow�D��n貑_�d��R��gu�M�A��L��&gt;V�����!��&quot;Ql�,F;���oOM��w x��$z������&quot;��^�	C}LTcqߵe�#�?B�v5k\$��xf��Ŝ�P�46&gt;F���xx�Tn�K��@�u�!Nd7���]�����q�#�`�8�r�����0bʕ�?�e�K.g��g;&ntilde;�1����Ƈ���%�K/���
N��g5#e�3�lyE,\�&quot;�Z18�R�����^���m2��Z�&amp;�&quot;��T�b�UԩEMFZ�b&#039;��a�7y�;���d}M������&gt;�r�����Yo�%���#8n3)���%,և�n�%�x�&lt;�f�~���s�������ds�#�`�9�r�����Df�Lxvx_C���Cm��#�:�ʥ:��~Y/
�;ыpu������Z�N֐�Si��,&quot;�*�bHE� �b�#�k�����7��e
endstream
endobj
8 0 obj
&lt;&lt; /Type /Font
/Subtype /Type1
/Name /F1
/BaseFont /Helvetica-Bold
/Encoding /WinAnsiEncoding
&gt;&gt;
endobj
9 0 obj
&lt;&lt; /Type /Font
/Subtype /Type1
/Name /F2
/BaseFont /Helvetica
/Encoding /WinAnsiEncoding
&gt;&gt;
endobj
xref
0 10
0000000000 65535 f 
0000000009 00000 n 
0000000074 00000 n 
0000000120 00000 n 
0000000284 00000 n 
0000000313 00000 n 
0000000522 00000 n 
0000000625 00000 n 
0000002130 00000 n 
0000002242 00000 n 
trailer
&lt;&lt;
/Size 10
/Root 1 0 R
/Info 5 0 R
/ID[&lt;2f0e2fa91f9cc2c5a5b5a09200082d08&gt;&lt;2f0e2fa91f9cc2c5a5b5a09200082d08&gt;]
&gt;&gt;
startxref
2349
%%EOF
</code>
 </pre>
    </span>
<span id="execution-results-GETapi-reports-billing-export-pdf" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-reports-billing-export-pdf"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-reports-billing-export-pdf"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-reports-billing-export-pdf" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-reports-billing-export-pdf">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-reports-billing-export-pdf" data-method="GET"
      data-path="api/reports/billing/export/pdf"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-reports-billing-export-pdf', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-reports-billing-export-pdf"
                    onclick="tryItOut('GETapi-reports-billing-export-pdf');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-reports-billing-export-pdf"
                    onclick="cancelTryOut('GETapi-reports-billing-export-pdf');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-reports-billing-export-pdf"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/reports/billing/export/pdf</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-reports-billing-export-pdf"
               value="Bearer {SEU_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer {SEU_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-reports-billing-export-pdf"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-reports-billing-export-pdf"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>date_from</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date_from"                data-endpoint="GETapi-reports-billing-export-pdf"
               value="2026-01-01"
               data-component="query">
    <br>
<p>Precisa vir junto com date_to. Example: <code>2026-01-01</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>date_to</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date_to"                data-endpoint="GETapi-reports-billing-export-pdf"
               value="2026-06-30"
               data-component="query">
    <br>
<p>Precisa vir junto com date_from. Example: <code>2026-06-30</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>date_field</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="date_field"                data-endpoint="GETapi-reports-billing-export-pdf"
               value="due_date"
               data-component="query">
    <br>
<p>Example: <code>due_date</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>issue_date</code></li> <li><code>due_date</code></li> <li><code>payment_date</code></li></ul>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>customer_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="customer_id"                data-endpoint="GETapi-reports-billing-export-pdf"
               value="1"
               data-component="query">
    <br>
<p>Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="GETapi-reports-billing-export-pdf"
               value="overdue"
               data-component="query">
    <br>
<p>Example: <code>overdue</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>pending</code></li> <li><code>overdue</code></li> <li><code>paid</code></li> <li><code>cancelled</code></li></ul>
            </div>
                </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
