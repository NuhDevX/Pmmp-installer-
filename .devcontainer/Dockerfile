FROM ubuntu:22.04

# Install dependencies
RUN apt update && \
    apt install -y php php-cli php-curl php-mbstring php-yaml php-xml unzip git curl && \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

WORKDIR /workspace
