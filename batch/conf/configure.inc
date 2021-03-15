<?php
$config = [
    "db" => [
        "host"      => "localhost",
        "port"      => "8080",
        "login_id"  => "themeuser",
        "login_pwd" => "themepass",
        "schema"    => "theme",
    ],
    "login_url"  => "https://card.dartslive.com/entry/login/doLogin.jsp?id=%s&ps=%s",
    "theme_url"  => "https://card.dartslive.com/t/customize/theme_settings.jsp?tp=%d&dc=0&p=%d",
    "image_url"  => "https://card.dartslive.com%s",
    "image_path" => "/var/www/html/web/images/",
];
