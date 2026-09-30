<?php

return [

    /*
     * 跨域请求允许的路径。所有 renti 项目数据 API 都开放。
     */
    'paths' => ['api/*', 'api-admin/*', 'api-user/*', 'oauth/*'],

    /*
     * 允许的请求方法。
     */
    'allowed_methods' => ['*'],

    /*
     * 允许的来源。renti 项目运行在 www.a.com，故开放全部来源（GET 公开数据）。
     */
    'allowed_origins' => ['*'],

    /*
     * 允许的请求头。
     */
    'allowed_headers' => ['*'],

    /*
     * 响应头暴露给前端。
     */
    'exposed_headers' => [],

    /*
     * 预检请求缓存时间（秒）。
     */
    'max_age' => 0,

    /*
     * 是否允许带凭证。
     */
    'supports_credentials' => false,

];
