# Tests

## Running Tests on Github Actions

### Secrets to store

| Secret | Description |
|---|---|
| TAWK_PROPERTY_ID | Property Id |
| TAWK_WIDGET_ID | Widget Id |
| TAWK_USERNAME | tawk.to account username |
| TAWK_PASSWORD | tawk.to account password |


## Running Tests Locally
### Dependencies

Run `composer run build` to build both dev and prod dependencies.

### Starting the Docker Containers

Run `docker-compose -f ./.github/docker/docker-compose.yml up -d` to start the services.

Then run `docker logs -f wordpress-cli` to check if the WordPress setup is done.

#### Environment Variables

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

### Running the Tests

#### Environment Variables

These are the environment variables needed to run the selenium tests locally.

| Environment Variable | Description | Required |
|---|---|---|
| TAWK_PROPERTY_ID | Property Id | Yes |
| TAWK_WIDGET_ID | Widget Id | Yes |
| TAWK_USERNAME | tawk.to account username | Yes |
| TAWK_PASSWORD | tawk.to account password | Yes |
| WEB_HOST | Wordpress web hostname | Yes |
| WEB_PORT | Wordpress web port | No |
| SELENIUM_BROWSER | Browser type (chrome, firefox, edge) | Yes |
| SELENIUM_HOST | Selenium host | Yes |
| SELENIUM_PORT | Selenium port | No |

#### Command Sample
```
TAWK_PROPERTY_ID=<TAWK_PROPERTY_ID> \
TAWK_WIDGET_ID=<TAWK_WIDGET_ID> \
TAWK_USERNAME=<TAWK_USERNAME> \
TAWK_PASSWORD=<TAWK_PASSWORD> \
WEB_HOST=wordpress \
SELENIUM_BROWSER=chrome \
SELENIUM_HOST=localhost \
SELENIUM_PORT=4444 \
composer run test
```
