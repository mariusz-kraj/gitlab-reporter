FROM codibly/php

# INSTALL UTILS
RUN apt-get update && apt-get install -y \
  htop \
  nano \
  ant

# CLEAN APT AND TMP
RUN apt-get clean && apt-get autoremove && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
