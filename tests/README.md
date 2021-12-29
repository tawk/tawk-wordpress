# Tests

## Running Tests on Github Actions

### Secrets to store

| Secret | Description |
|---|---|
| TAWK_PROPERTY_ID | Property Id |
| TAWK_WIDGET_ID | Widget Id |
| TAWK_USERNAME | tawk.to account username |
| TAWK_PASSWORD | tawk.to account password |
| BROWSERSTACK_USERNAME | Browserstack username |
| BROWSERSTACK_ACCESS_KEY | Browserstack access key |

## Running Tests on local Selenium

### Environment Variables

These are the environment variables needed to run the selenium tests locally.

| Environment Variable | Description | Required |
|---|---|---|
| TAWK_PROPERTY_ID | Property Id | Yes |
| TAWK_WIDGET_ID | Widget Id | Yes |
| TAWK_USERNAME | tawk.to account username | Yes |
| TAWK_PASSWORD | tawk.to account password | Yes |
| WEB_HOST | Wordpress web hostname | Yes |
| WEB_PORT | Wordpress web port | No |
| SELENIOUM_BROWSER | Browser type (chrome, firefox, safari) | Yes |
| SELENIUM_HOST | Selenium host | Yes |
| SELENIUM_PORT | Selenium port | No |

### Command Sample
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

## Running Tests on Browserstack

### Environment Variables

These are the environment variables needed to run the selenium tests on browserstack.

| Environment Variable | Description | Required |
|---|---|---|
| TAWK_PROPERTY_ID | Property Id | Yes |
| TAWK_WIDGET_ID | Widget Id | Yes |
| TAWK_USERNAME | tawk.to account username | Yes |
| TAWK_PASSWORD | tawk.to account password | Yes |
| WEB_HOST | Wordpress web hostname | Yes |
| WEB_PORT | Wordpress web port | No |
| SELENIOUM_BROWSER | Browser type (chrome, firefox, safari) | Yes |
| SELENIUM_HOST | Selenium host | Yes |
| SELENIUM_PORT | Selenium port | No |
| SELENIUM_HTTPS_FLAG | HTTPS flag for the browserstack url. `true` for browserstack testing |Yes |
| SELENIUM_HUB_FLAG | Flag for using selenium hub. `true` for browserstack testing |Yes |
| BROWSERSTACK_USERNAME | Browserstack username | Yes |
| BROWSERSTACK_ACCESS_KEY | Browserstack access key | Yes |
| BROWSERSTACK_LOCAL_IDENTIFIER | Local unique identifier for the test instance | Yes |
| BROWSERSTACK_PROJECT_NAME | Project name for the test instance | Yes |
| BROWSERSTACK_BUILD_NAME | Build name for the test instance | Yes |

### Command Sample
```
TAWK_PROPERTY_ID=<TAWK_PROPERTY_ID> \
TAWK_WIDGET_ID=<TAWK_WIDGET_ID> \
TAWK_USERNAME=<TAWK_USERNAME> \
TAWK_PASSWORD=<TAWK_PASSWORD> \
WEB_HOST=wordpress \
SELENIUM_BROWSER=safari \
SELENIUM_HOST=<BROWSERSTACK_USERNAME>:<BROWSERSTACK_ACCESS_KEY>@hub-cloud.browserstack.com \
SELENIUM_HTTPS_FLAG='true' \
SELENIUM_HUB_FLAG='true' \
BROWSERSTACK_USERNAME=<BROWSERSTACK_USERNAME> \
BROWSERSTACK_ACCESS_KEY=<BROWSERSTACK_ACCESS_KEY> \
BROWSERSTACK_LOCAL_IDENTIFIER=docker-test \
BROWSERSTACK_PROJECT_NAME=test-project \
BROWSERSTACK_BUILD_NAME=test-build \
composer run test
```
