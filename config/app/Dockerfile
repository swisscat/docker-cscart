FROM centos:latest

MAINTAINER Etienne Favre <etienne.favre@b-i.com>

ADD app-nginx.repo /etc/yum.repos.d/nginx.repo

# Create runtime user for application
RUN useradd -u 1000 www-data \
 	&& usermod -a -G www-data www-data \
	# Add repositories
	&& rpm -Uvh https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm \
 	&& rpm -Uvh https://centos7.iuscommunity.org/ius-release.rpm \
	# Install EPEL
	&& yum install -y epel-release \
	# Update RPM Packages
	&& yum -y update \
	# Install packages
	&& yum install -y \
	bzip2 \
	cronie \
	gcc \
	gcc-c++ \
	git \
	make \
	nginx \
	net-tools \
	npm \
	php56u \
	php56u-cli \
	php56u-common \
	php56u-fpm \
	php56u-gd \
	php56u-imap \
	php56u-intl \
	php56u-mbstring \
	php56u-mcrypt \
	php56u-mysqli \
	php56u-opcache \
	php56u-pgsql \
	php56u-soap \
	php56u-ssh2 \
	php56u-xml \
	python-pip \
	ruby-devel \
	rubygems \
	sqlite-devel \
	wget \
	# Install debug packages
	&& yum install -y \
	php56u-pecl-xdebug \
	# Install openSSH Server
	&& yum install -y openssh-server sudo \
    && mkdir -p /etc/nginx/ssl \
	# Install pip packages
	&& pip install meld3==1.0.0 \
	&& pip install supervisor && mkdir -p /var/log/supervisor \
	# Install grunt
	&& npm install -g grunt \
	# Install mailcatcher
	&& gem install --no-ri --no-rdoc json_pure mailcatcher \
	# Install Composer
	&& php -r "readfile('https://getcomposer.org/installer');" | php \
	&& mv composer.phar /usr/local/bin/composer \
	&& chmod a+x /usr/local/bin/composer \
	# Install Phpunit
	&& wget https://phar.phpunit.de/phpunit.phar \
	&& mv phpunit.phar /usr/local/bin/phpunit \
	&& chmod a+x /usr/local/bin/phpunit \
	# Remove useless packages and clean up
	&& yum remove -y wget && yum clean all \
	# Clean configuration
	&& rm /etc/nginx/conf.d/* \
	   /etc/php-fpm.d/*.conf \
	# Create data directories
	&& mkdir -p /tmp/app/sessions && chown -R www-data:www-data /tmp/app/sessions \
	&& mkdir -p /tmp/app/cache && chown -R www-data:www-data /tmp/app/cache \
	&& mkdir -p /tmp/profiler && chown -R www-data:www-data /tmp/profiler

USER www-data

RUN echo "* * * * * /var/apps-schedule-run" | crontab -

USER root

RUN touch /var/apps-schedule-run && chown www-data:www-data /var/apps-schedule-run

# Copy openssl configs 
ADD nginx/ssl /etc/nginx/ssl/

# Add configuration
ADD nginx.conf /etc/nginx/
ADD app-nginx.conf /etc/nginx/conf.d/app.conf
ADD app-php-fpm.conf /etc/php-fpm.d/app.conf
ADD app-php.ini /etc/php.d/app.ini
ADD app-xdebug.ini /etc/php.d/15-xdebug.ini
COPY php.d/* /etc/php.d/

ADD supervisord.conf /etc/supervisord.conf

VOLUME ["/tmp/profiler"]

EXPOSE 22 80 443 1080

CMD ["/usr/bin/supervisord", "--configuration=/etc/supervisord.conf"]