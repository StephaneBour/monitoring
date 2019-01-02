# Monitoring

[![Build Status](https://travis-ci.org/StephaneBour/monitoring.svg?branch=master)](https://travis-ci.org/StephaneBour/monitoring)

### Prerequisites
- PHP >= 7.2
- Elasticsearch >= 6.X

### Installation

```shell
copy .env.example .env
nano .env
composer install
php artisan key:generate
php artisan templates:create
```

### Connections
- Elasticsearch (query)
- HTTP (status)
- HTTP (json response)
- MySQL

### Alerts
- Mails
- Slack

### Conditions
* Count
  * Greater than
  * Greater than or equal
  * Lower than
  * Lower than or equal
  * Equal
  * Range (min, max)

* Parse
  * Exist
  * Not exists