# openSenseMap-proxy
A simple proxy with caching for the openSenseMap-API.

* No database required
* No need to setup a CRON-job
* `allow_url_fopen` has to be enabled
* You probably have to set the permission of the `api/cache`-directory (`chmod 777 api/cache/`)

## Why using it?
* Simpler privacy-management (It's on your own server.)
* Size of transfered data is smaller
* Not exposing the location

## Configuration
All configruation is done in `/api/config.php`:
```php
<?php
$config = array(
  'interval' => 5,            // 5 minutes update interval
  'include_name' => false,    // not including the name in output-json
  'cors' => true,             // will enable CORS
  'senseboxes' => array(
    'weatherstation' => '56957f3ab3de1fe0052532da',
    'dustsensor' => '5ad4cf6d223bd8001939172d'
  )
);
```

## API
`http(s)://host/api/?q=weatherstation` - Will return a slimmer version of the official API-route for the senseBox with the id `56957f3ab3de1fe0052532da`.
(Take a look at the configuration.)

### Response
```json
{
  "time": 1560608077,
  "sensors": [
    {
      "title": "PM10",
      "unit": "µg/m³",
      "sensortype": "SDS 011",
      "value": "13.87",
      "time": "2019-06-15T14:12:57.407Z"
    },
    {
      "title": "PM2.5",
      "unit": "µg/m³",
      "sensortype": "SDS 011",
      "value": "7.30",
      "time": "2019-06-15T14:12:57.407Z"
    },
    {
      "title": "Temperatur",
      "unit": "°C",
      "sensortype": "DHT22",
      "value": "13.90",
      "time": "2019-06-15T14:12:57.407Z"
    },
    {
      "title": "rel. Luftfeuchte",
      "unit": "%",
      "sensortype": "DHT22",
      "value": "99.90",
      "time": "2019-06-15T14:12:57.407Z"
    }
  ]
}

```
