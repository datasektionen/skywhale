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
        "traefik-external.enable=true",
        "traefik-external.http.routers.skywhale.rule=Host(`val.datasektionen.se`)",
        "traefik-external.http.routers.skywhale.entrypoints=websecure",
        "traefik-external.http.routers.skywhale.tls.certresolver=default",

        "traefik-internal.enable=true",
        "traefik-internal.http.routers.skywhale.rule=Host(`skywhale.nomad.dsekt.internal`)",
      ]
    }

    task "skywhale" {
      driver = "docker"

      config {
        image = var.image_tag
        ports = ["http"]
      }

      template {
        data        = <<ENV
{{ with nomadVar "nomad/jobs/skywhale" }}
APP_KEY={{ .app_key }}
LOGIN_API_KEY={{ .login_api_key }}
SPAM_API_KEY={{ .spam_api_key }}
DB_PASSWORD={{ .database_password }}
{{ end }}
PORT={{ env "NOMAD_PORT_http" }}
LOGIN_API_URL=https://login.datasektionen.se
LOGIN_FRONTEND_URL=https://login.datasektionen.se
HODIS_API_URL=https://hodis.datasektionen.se
PLS_API_URL=https://pls.datasektionen.se/api
ZFINGER_API_URL=https://zfinger.datasektionen.se
SPAM_API_URL=https://spam.datasektionen.se/api/sendmail
DB_CONNECTION=pgsql
DB_USERNAME=skywhale
DB_HOST=postgres.dsekt.internal
DB_DATABASE=skywhale
APP_URL=https://val.datasektionen.se
APP_ENV=production
APP_DEBUG=false
ENV
        destination = "local/.env"
        env         = true
      }

      resources {
        memory = 80
      }
    }
  }
}

variable "image_tag" {
  type = string
  default = "ghcr.io/datasektionen/skywhale:latest"
}
