# docker-cscart
This package allows you to spin up a docker-based environment for cs-cart.

## Installation
Include the package on your dev dependencies:
```
composer require swisscat/docker-cscart --dev --ignore-platform-reqs
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