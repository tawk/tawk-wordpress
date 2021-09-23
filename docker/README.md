Wordpress
============
Docker container for Wordpress.

## Information
- Wordpress Version: Latest
- MySQL Version: 5.7

## Pre-Requisites
- install docker-compose [http://docs.docker.com/compose/install/](http://docs.docker.com/compose/install/)

## Usage
Start the container:
- ```docker-compose up```

Stop the container:
- ```docker-compose stop```

Destroy the container and start from scratch:
- ```docker-compose down```
- ```docker volume rm wordpress_db_data wordpress_web_data```

## Plugin setup
You can follow the instruction in the [Wordpress KB Article](https://help.tawk.to/article/wordpress)
