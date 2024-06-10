FROM anroe/nginx-headers-more

RUN mkdir -p /etc/nginx/cert

COPY docker/nginx/cert /etc/nginx/cert

RUN if [ ! -f "/etc/nginx/cert/msa-core.crt" ]; then \
    cp /etc/nginx/cert/msa-core.local.crt /etc/nginx/cert/msa-core.crt; \
    fi

RUN if [ ! -f "/etc/nginx/cert/msa-core.key" ]; then \
    cp /etc/nginx/cert/msa-core.local.key /etc/nginx/cert/msa-core.key; \
    fi

COPY docker/nginx/default.conf /etc/nginx/conf.d/

EXPOSE 443