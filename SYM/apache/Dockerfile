FROM httpd:latest

RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get install -y nano vim curl git

RUN set -x && \
    curl -O https://files.magerun.net/n98-magerun2.phar && \
    chmod +x ./n98-magerun2.phar && \
    mv ./n98-magerun2.phar /usr/local/bin/n98-magerun2
    
RUN echo "" >> /usr/local/apache2/conf/httpd.conf && \
  echo "Include /usr/local/apache2/conf/overwrite/**" >> /usr/local/apache2/conf/httpd.conf