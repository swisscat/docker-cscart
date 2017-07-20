# docker-cscart
This package allows you to spin up a docker-based environment for cs-cart.

## Installation
Include the package on your dev dependencies:
```
composer require swisscat/docker-cscart --dev --ignore-platform-reqs
```

Copy the dependencies:
```
cp -r vendor/swisscat/docker-cscart/var/* var/
cp -r vendor/swisscat/docker-cscart/docker-compose.yml docker-compose.yml
```

Run the environment (docker-compose)
```
var/toolbox env:up   
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