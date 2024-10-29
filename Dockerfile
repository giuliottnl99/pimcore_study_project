# Use the Pimcore base image with PHP 8.3 and supervisord
FROM pimcore/pimcore:php8.3-supervisord-latest

# Switch to root user to install packages
USER root

# Install cron
RUN apt-get update && \
    apt-get install -y cron vim && \
    rm -rf /var/lib/apt/lists/*

# Copy your script into the container
COPY my_script.sh /usr/local/bin/my_script.sh
RUN chmod +x /usr/local/bin/my_script.sh

# Copy the crontab file to the appropriate location
COPY crontab.txt /etc/cron.d/my-cron

# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/my-cron

# Apply the cron job
RUN crontab /etc/cron.d/my-cron

# Create a log file for cron
RUN touch /var/log/cron.log

# Update supervisord configuration to include cron
COPY supervisord-cron.conf /etc/supervisor/conf.d/cron.conf

# Expose necessary ports (if applicable)
EXPOSE 9000

# Default CMD for supervisord, which will run both PHP and cron
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
