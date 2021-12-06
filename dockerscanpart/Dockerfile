# syntax=docker/dockerfile:1
FROM ubuntu
LABEL biogeco.version-date="2021-11-25"
RUN apt-get update && apt-get install -y cron && rm -rf /var/lib/apt/lists/* && mkdir /pgd
COPY pgd.py db-insert.py Master_PGD.json /pgd/
COPY pgd-cron /etc/cron.d/pgd-cron
# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/pgd-cron && touch /var/log/cron.log && crontab /etc/cron.d/pgd-cron
# Run the command on container startup
ENTRYPOINT ["cron", "-f"]
