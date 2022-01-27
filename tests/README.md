# Tests

## Test setup and configuration

### Building package to test

Run `composer run build` to build both dev and prod dependencies.

Run `composer run package` to build the tawk.to plugin zip file that will be tested.

### Setting up docker environment

Docker-compose is used for test dependency setup and is required to run these tests

#### Configuring docker environment

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

#### Wordpress and WooCommerce setup

Docker environment is setup up to automatically populate test data
This is done by `wordpress-cli` service in compose file

For more details on setup, see <insert script file here> setup script

#### Running the test environment

To run docker environment for testing this repository, start docker compose file found in `/tests/docker/docker-compose.yml`

Example (assuming from root of repository)

```sh
docker-compose -f ./tests/docker/docker-compose.yml up -d
```

Environment is ready when `wordpress-cli` successfully exists.
To monitor its status, you can tail it's logs using `docker logs -f wordpress-cli`

### Configuring local test environment

These are the environment variables needed to run the selenium tests locally using composer script

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

#### Storing local environments in a file for easy reference

To simplify testing, you can place your environment configuration in a `.env.local` file.

Example contents:
```
export TAWK_PROPERTY_ID='<TAWK_PROPERTY_ID>'
export TAWK_WIDGET_ID='<TAWK_WIDGET_ID>'
export TAWK_USERNAME='<TAWK_USERNAME>'
export TAWK_PASSWORD='<TAWK_PASSWORD>'
export WEB_HOST='wordpress'
export SELENIUM_BROWSER='chrome'
export SELENIUM_HOST='localhost'
export SELENIUM_PORT='4444'
```

And simply run

`source .env.local && composer run test`

## Running Tests on Github Actions

This repository is set up to use Github Actions to perform automated testing.

To use actions in this repository, the following secrets need to be configured:

| Secret | Description |
|---|---|
| TAWK_PROPERTY_ID | Property Id |
| TAWK_WIDGET_ID | Widget Id |
| TAWK_USERNAME | tawk.to account username |
| TAWK_PASSWORD | tawk.to account password |
