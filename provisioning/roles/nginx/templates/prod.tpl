server {
    listen 80;

    error_log /var/log/nginx/kickfoo_error.log;
    access_log /var/log/nginx/kickfoo_access.log;

    server_name {{ servername }};
    client_max_body_size 50M;

    root {{ doc_root }};

    # serve static files directly
    location ~* ^.+.(jpg|jpeg|gif|css|png|js|ico|html|xml|txt)$ {
        access_log        off;
        expires           max;
    }

    location / {
        try_files $uri @app;
    }

    location @app {
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        fastcgi_pass unix:/run/php/php7.0-fpm.sock;
        fastcgi_read_timeout 120;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME {{ script_name }};
        fastcgi_param SCRIPT_NAME {{ script_name }};
        fastcgi_param SYMFONY__ENV {{ env }};
        fastcgi_param SYMFONY__DEBUG {{ debug }};
    }
}
