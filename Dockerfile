FROM php:7.4-cli

RUN apt-get update \
 && apt-get install -y --no-install-recommends \
    supervisor

VOLUME ["/app", "/etc/supervisor/conf.d"]

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.ini"]
