# Tests

## Running Tests on Github Actions

### Secrets to store

| Secret | Description |
|---|---|
| TAWK_PROPERTY_ID | Property Id |
| TAWK_WIDGET_ID | Widget Id |
| TAWK_USERNAME | tawk.to account username |
| TAWK_PASSWORD | tawk.to account password |

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
| SELENIUM_BROWSER | Browser type (chrome, firefox, safari) | Yes |
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
