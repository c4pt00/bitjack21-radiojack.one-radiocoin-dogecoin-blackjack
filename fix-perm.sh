 adduser www-data
 chown -R www-data:www-data /var/www
 chmod -R g+rwX /var/www
 semanage fcontext -a -t httpd_sys_rw_content_t /var/www
 restorecon -Rv /var/www
