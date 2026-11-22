job "skywhale" {
  type = "service"

  group "skywhale" {
    network {
      port "http" { }
    }

    service {
      name     = "skywhale"
      port     = "http"
      provider = "nomad"
      tags = [
        "traefik.enable=true",
        "traefik.http.routers.skywhale.rule=Host(`val.datasektionen.se`)",
        "traefik.http.routers.skywhale.tls.certresolver=default",
      ]
    }

    task "skywhale" {
      driver = "docker"

      config {
        image = var.image_tag
        ports = ["http"]

        volumes = [
          "local/nginx.conf:/etc/nginx/http.d/http_proxy.conf"
        ]
      }

      template {
        data = <<EOF
server {
    listen {{ env "NOMAD_PORT_http" }};
    server_name _;
    root /var/www/html/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME /var/www/html/public$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF
        destination = "local/nginx.conf"
      }

      template {
        data        = <<ENV
{{ with nomadVar "nomad/jobs/skywhale" }}
APP_KEY={{ .app_key }}
OIDC_SECRET={{ .oidc_secret }}
HIVE_API_KEY={{ .hive_api_key }}
SPAM_API_KEY={{ .spam_api_key }}
RFINGER_API_KEY={{ .rfinger_api_key }}
DB_PASSWORD={{ .database_password }}
{{ end }}
PORT={{ env "NOMAD_PORT_http" }}
LOG_CHANNEL=stderr
LOG_LEVEL=info
SSO_API_URL=http://sso.nomad.dsekt.internal
OIDC_PROVIDER=http://sso.nomad.dsekt.internal/op
OIDC_ID=skywhale
REDIRECT_URL=https://val.datasektionen.se/login-complete
HIVE_API_URL=http://hive.nomad.dsekt.internal/api/v1
RFINGER_API_URL=https://rfinger.datasektionen.se/api
SPAM_API_URL=https://spam.datasektionen.se/api/legacy/sendmail
DB_CONNECTION=pgsql
DB_USERNAME=skywhale
DB_HOST=postgres.dsekt.internal
DB_DATABASE=skywhale
APP_URL=https://val.datasektionen.se
APP_ENV=production
APP_DEBUG=false
SESSION_DRIVER=cookie
ENV
        destination = "local/.env"
        env         = true
      }

      resources {
        memory = 120
      }
    }
  }
}

variable "image_tag" {
  type = string
  default = "ghcr.io/datasektionen/skywhale:latest"
}
