FROM ctf-php-nginx-maria

# root for install
USER root

#
# Copy www_site files
#
COPY --chown=nobody www_site/ /var/www/html/
RUN chmod -R a+w /var/www/html/

# Back to nobody
USER nobody
