#!/bin/sh

envsubst '\$NGINX_DOCROOT' < /config/default.conf.template > /etc/nginx/conf.d/default.conf

exec "$@"
