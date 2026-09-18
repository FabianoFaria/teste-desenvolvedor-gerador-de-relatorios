<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    // Content-Disposition precisa ser explicitamente exposto: por padrão,
    // fetch() em requisição cross-origin (frontend em outra porta/origem)
    // não consegue ler esse header via response.headers.get(), mesmo com a
    // requisição autorizada — o browser só expõe um safelist pequeno de
    // headers "simples" por padrão. Sem isso, o frontend não consegue
    // extrair o nome de arquivo sugerido pelo backend na exportação do
    // relatório e cai sempre no nome de fallback.
    'exposed_headers' => ['Content-Disposition'],

    'max_age' => 0,

    'supports_credentials' => false,

];
