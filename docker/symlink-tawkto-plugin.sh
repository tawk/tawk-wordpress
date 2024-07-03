#!/bin/bash

timeout 5m bash -c 'until test -d /bitnami/wordpress/wp-content/plugins/; do echo Waiting for wordpress setup.... && sleep 3; done'

ln -sf /tawkto /bitnami/wordpress/wp-content/plugins/
echo Symlinked /tawkto to /bitnami/wordpress/wp-content/plugins/
