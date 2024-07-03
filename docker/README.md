# Develop locally

This docker compose makes use of the `bitnami/wordpress` image and symlinks the `tawkto` directory into wordpress `plugins` folder. The `query-monitor` plugin is auto installed for ease of debugging.

## Setup
`docker compose up -d`

## Wordpress
- http://localhost:8080
- Username: admin
- Password: admin

After setup, please go to "Plugins" tab and activate "Tawk.to Live Chat"