#!/bin/bash
date=$(date)
echo "$date   : Program wp-cli_setup_01.sh script executed."
#
apt update
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
php wp-cli.phar --info
chmod +x wp-cli.phar
mv wp-cli.phar /usr/local/bin/wp
wp-cli --info
date=$(date)
echo "$date   : Setting up Wordpress...."
#
wp core install --url=http://localhost:8001 --title=your_site_title_m --admin_user=wordpress --admin_email=test@tawk.to --admin_password=wordpress --allow-root
#
wp user list --allow-root
#
wp plugin install tawkto-live-chat --activate --allow-root
#
wp plugin install query-monitor --activate --allow-root
#
wp plugin list --allow-root
#
wp cache flush --allow-root
date=$(date)
echo "$date   : Program wp-cli_setup_01.sh script completed."