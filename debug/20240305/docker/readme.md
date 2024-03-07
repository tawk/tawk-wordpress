== Instructions ==
This is docker compose instruction to simulate Deprecated Wordpress Plugin Warning
Ref. (Clickup) Issue ID: [86entagpa](https://app.clickup.com/t/36791864/86entagpa)

== Changelog ==

= 0.7.3 =
* Bug_fixes: 86entagpa
* Author: Mior
* Tested Version: 
    PHP: 8.2.16
    Wordpress: 6.4.3
    Mysql: 5.7

* Local Branch Docker Compose Path: /home/user/branch/tawk/tawk-wordpress/debug/20240305/docker/


== To Start Bug Re-creation and fixes ==

1. cd to docker compose path 
    # cd ~/tawk-wordpress/debug/20240305/

2. make sure all test files available:
    # docker-compose.yml  readme.md  tawkto.php  wp-cli_setup_01.sh

3. clear docker environment (optional)
    # docker compose down --volumes

4. start docker compose
    # docker compose up -d

5. check container process
    # docker ps

6. Execute container interactive terminal
    # docker exec -it docker-wordpress-1 /bin/bash  

7. Make Sure Container Working dir : /var/www/html
    # pwd

8. Setup Wordpress Testing Environment using setup script
    # ./tmp/wp-cli_setup_01.sh 

9. Observe Wordpress Environment using local Browser (This will show the deprecated Warning)
    # http://localhost:8001/wp-admin

10. Applying fixes: rewrite fixed tawkto.php file
    # cp ./tmp/tawkto.php ./wp-content/plugins/tawkto-live-chat/tawkto.php

11. Observe Wordpress Environment using local Browser (No Deprecated Warning)
    # http://localhost:8001/wp-admin (Refresh)

12. clear docker environment (optional)
    # docker compose down --volumes

13. Test Completed