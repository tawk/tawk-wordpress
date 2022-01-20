#! /bin/sh

TIMEOUT=30;

wait_for() {
  for i in `seq $TIMEOUT` ; do
    HOST=$(printf "%s\n" "$1"| cut -d : -f 1);
    PORT=$(printf "%s\n" "$1"| cut -d : -f 2);

    if [ -z $PORT ] || [ "$PORT" == "$HOST" ]; then
      PORT="80";
    fi;

    nc -z "$HOST" "$PORT" > /dev/null 2>&1;

    result=$?;
    if [ $result -eq 0 ] ; then
		echo "$1 is ready."
      exit 0;
    fi
	echo "$1 is not yet ready."
    sleep 1;
  done
  echo "Operation timed out" >&2;
  exit 1;
}

FAILED=0;

wait_for ${WEB_HOST} & WEB_PID=$!;
wait_for ${WORDPRESS_DB_HOST} & DB_PID=$!;
wait $WEB_PID || FAILED=$((FAILED+1));
wait $DB_PID || FAILED=$((FAILED+1));

if [ $FAILED -gt 0 ]; then
  exit 1;
fi

# Run wp cli setup commands
wp core install --path="/var/www/html" --url=http://${WEB_HOST} --title="Local Wordpress By Docker" --admin_user=${WORDPRESS_ADMIN_USER} --admin_password=${WORDPRESS_ADMIN_PASSWORD} --admin_email=${WORDPRESS_ADMIN_EMAIL};
wp rewrite structure /%postname%/;
wp term create category Category-A --description="Category A";
wp term create category Category-B --description="Category B";
wp term create category Category-C --description="Category C";
wp post term add 1 post_tag tag-a tag-b tag-c;
wp plugin install woocommerce --activate;
wp theme install storefront --activate;
wp plugin install wordpress-importer --activate;
wp import wp-content/plugins/woocommerce/sample-data/sample_products.xml --authors=skip;
wp wc product_tag create --name=product-tag-a --user=admin;
wp option set woocommerce_store_address '123 Main Street';
wp option set woocommerce_store_address_2 '';
wp option set woocommerce_store_city 'Toronto';
wp option set woocommerce_default_country 'CA:ON';
wp option set woocommerce_store_postalcode 'A1B2C3';
wp option set woocommerce_currency 'CAD';
wp option set woocommerce_product_type 'physical';
wp option set woocommerce_allow_tracking 'no';
wp wc --user=admin tool run install_pages;
