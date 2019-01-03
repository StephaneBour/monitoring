# Monitoring

[![Build Status](https://travis-ci.org/StephaneBour/monitoring.svg?branch=master)](https://travis-ci.org/StephaneBour/monitoring)


Monitor your business processes based on a period or time, and be notified of errors by email, slack or webhooks.

The documentation is available on the [wiki](https://github.com/StephaneBour/monitoring/wiki).

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
* Elasticsearch (query)
* HTTP (status)
* HTTP (json response)
* MySQL
* FTP

### Alerts
* Mails
* Slack
* Webhook

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
  
* Aggregat
  * Compare bucket with others conditions
  
* Relative
  * Compare an other record with others conditions