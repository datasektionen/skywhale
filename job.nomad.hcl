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
SSO_API_URL=http://sso.nomad.dsekt.internal
OIDC_PROVIDER=http://sso.nomad.dsekt.internal/op
OIDC_ID=skywhale
REDIRECT_URL=https://val.datasektionen.se/login-complete
HIVE_API_URL=http://hive.nomad.dsekt.internal/api/v1
RFINGER_API_URL=https://rfinger.datasektionen.se/api
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
        memory = 120
      }
    }
  }
}

variable "image_tag" {
  type = string
  default = "ghcr.io/datasektionen/skywhale:latest"
}
