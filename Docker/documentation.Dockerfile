ARG NODE_VERSION=latest

FROM node:$NODE_VERSION

RUN apt update && apt install -y mysql-client php-cli php-mysql less
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && chmod +x wp-cli.phar && mv wp-cli.phar /usr/local/bin/wp
RUN echo "<?php error_reporting(0); @ini_set('display_errors', 0);" > /_pre.php
RUN echo "alias wp='wp --require=/_pre.php --path=\"/wordpress\" --allow-root'" >> /root/.bashrc
