# Tests

## Running Tests on Github Actions

### Secrets to store

| Secret | Description |
|---|---|
| TAWK_PROPERTY_ID | Property Id |
| TAWK_WIDGET_ID | Widget Id |
| TAWK_USERNAME | tawk.to account username |
| TAWK_PASSWORD | tawk.to account password |

## Requirements for Running Locally

### Dependencies

Run `composer run build` to build both dev and prod dependencies.

### Selenium Server

#### Docker containers

You can pull the images from [Selenium Docker Hub](https://hub.docker.com/u/selenium).

#### Local Selenium Server

You can download the selenium jar file from their [Downloads page](https://www.selenium.dev/downloads/).

Do note you'll also need to install the webdrivers locally. Here are some examples on how to install the webdrivers on Ubuntu.

##### Installing Chromedriver
```bash
# install chrome
mkdir /tmp/chrome/ && \
cd /tmp/chrome/ && \
wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb && \
sudo apt install ./google-chrome-stable_current_amd64.deb

# install chromedriver
sudo apt-get install unzip;
a=$(uname -m);
rm -r /tmp/chromedriver/;
mkdir /tmp/chromedriver/;
wget -O /tmp/chromedriver/LATEST_RELEASE http://chromedriver.storage.googleapis.com/LATEST_RELEASE;
if [ $a == i686 ]; then b=32; elif [ $a == x86_64 ]; then b=64; fi;
latest=$(cat /tmp/chromedriver/LATEST_RELEASE);
wget -O /tmp/chromedriver/chromedriver.zip 'http://chromedriver.storage.googleapis.com/'$latest'/chromedriver_linux'$b'.zip';
sudo unzip /tmp/chromedriver/chromedriver.zip chromedriver -d /usr/local/bin/;
```

##### Installing Geckodriver
```bash
sudo update -y;
sudo apt install firefox-geckodriver -y;
```

##### Installing Edgedriver
```bash
# install edge
sudo apt update -y;
sudo apt-get install unzip;
wget -q https://packages.microsoft.com/keys/microsoft.asc -O- | sudo apt-key add -;
sudo add-apt-repository "deb [arch=amd64] https://packages.microsoft.com/repos/edge stable main";
sudo apt install microsoft-edge-stable;

# install edgedriver
MS_EDGE_VERSION=$(microsoft-edge --version);
MS_EDGE_DRIVER_VERSION=$(echo $MS_EDGE_VERSION | rev | cut -d ' ' -f '1' | rev);
mkdir /tmp/edgedriver;
wget -O /tmp/edgedriver/edgedriver.zip https://msedgedriver.azureedge.net/$MS_EDGE_DRIVER_VERSION/edgedriver_linux64.zip;
sudo unzip /tmp/edgedriver/edgedriver.zip -d /tmp/edgedriver/;
sudo cp /tmp/edgedriver/msedgedriver /usr/local/bin/;
```

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
WEB_HOST=http://localhost \
WEB_PORT=8000 \
SELENIUM_BROWSER=chrome \
SELENIUM_HOST=localhost \
SELENIUM_PORT=4444 \
composer run test
```
