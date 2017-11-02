# docker-cscart
An utility to create CS-Cart environments using docker

## Installation
Install the package globally:
```
composer require --global swisscat/docker-cscart
```

Ensure `$PATH` contains the `~/.composer/vendor/bin` directory

# Setup a new environment
Head to a cs-cart repository and type
```
docker-cscart new
docker-compose up -d
```

## Configuration
The following docker configurations are supported in `config/app.json` :

### Native docker integration
```
{
  "docker": {
    "driver": "native"
  }
}
```

### Docker-machine integration
```
{
  "docker": {
    "driver": "machine",
    "machine": "default"
  }
}
```