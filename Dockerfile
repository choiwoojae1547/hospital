FROM php:8.1-apache

# 작업 디렉토리 설정
WORKDIR /var/www/html

# 필요한 확장 설치 (예: PDO, MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# AWS SDK 설치
COPY composer.json composer.lock /var/www/html/
RUN curl -sS https://getcomposer.org/installer | php \
    && php composer.phar install

# 애플리케이션 코드 복사
COPY . /var/www/html/

# 환경 변수 설정
ENV DB_HOST=your-db-host
ENV DB_NAME=hospital_db
ENV DB_USER=admin
ENV DB_PASSWORD=ehddnr99!
ENV SNS_TOPIC_ARN=your-sns-topic-arn
ENV DEPARTMENT="hospital"

# Apache 서비스 실행
CMD ["apache2-foreground"]
