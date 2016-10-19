<?php

return [
    "api_key" => env("CHARGIFY_API_KEY"),

    "password" => env("CHARGIFY_PASSWORD", "x"),

    "api_url" => env("CHARGIFY_API_URL", "https://gmail-sandbox.chargify.com/"),

    "share_key" => env("CHARGIFY_SHARE_KEY"),
];