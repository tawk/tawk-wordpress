# tawk.to WordPress Docker

Docker containers for tawk.to WordPress plugin.

## Information
- WordPress Version: Latest
- MySQL Version: 5.7
- WordPress CLI
- Selenium Standalone Servers: Latest (chrome, firefox, edge)

## Pre-Requisites
- install docker-compose [http://docs.docker.com/compose/install/](http://docs.docker.com/compose/install/)


## Environment Variables

Environment variables used in the `docker-compose.yml` file can be found in `.env` file.

| Environment Variable | Description | Default Value |
|---|---|---|
| WORDPRESS_DB_HOST | MySQL Service DB Host | db:3306 |
| WORDPRESS_DB_NAME | MySQL Service DB Name | wordpress |
| WORDPRESS_DB_USER | MySQL Service DB User | wordpress |
| WORDPRESS_DB_PASSWORD | MySQL Service DB Password | wordpress |
| WORDPRESS_DB_ROOT_PASSWORD | MySQL Service DB Root Password | somewordpress |
| WEB_HOST | WordPress Web Host | wordpress |
| WORDPRESS_DEBUG | WordPress Debug Mode | 1 |
| WORDPRESS_ADMIN_USER | WordPress Admin User | admin |
| WORDPRESS_ADMIN_PASSWORD | WordPress Admin Password | admin |
| WORDPRESS_ADMIN_EMAIL | WordPress Admin Email | admin@example.com |
| SELENIUM_BROWSER | Selenium Server Browser Type | chrome |
| SELENIUM_PORT | Selenium Server Port | 4444 |

## Usage

Start the WordPress container:
- ```docker-compose up -d db wordpress```

Start the WordPress setup container:
- ```docker-compose up wordpress-cli```

Start the Selenium Standalone Server container:
- ```docker-compose up -d selenium```

Stop the containers:
- ```docker-compose stop```

Destroy the container and start from scratch:
- ```docker-compose down```
- ```docker volume rm docker_db_data docker_wp_data```

## Plugin setup
You can follow the instruction in the [Wordpress KB Article](https://help.tawk.to/article/wordpress)
