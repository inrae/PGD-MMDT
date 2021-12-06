# syntax=docker/dockerfile:1
FROM mongo:4.4
LABEL biogeco.version-date="2021-11-24"
ENV TZ=Europe/Paris
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN apt-get update && apt-get install -y python3 python3-pymongo && rm -rf /var/lib/apt/lists/*
RUN touch /var/log/pgd.log
COPY initialisation/setupdb.js /docker-entrypoint-initdb.d/setupdb.js
