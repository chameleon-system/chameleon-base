# Google Maps

Since June 2016 Google Maps requires an API key to work properly. There is a free page impression volume, though.
Some old setups may work without a key, but it´s a matter of time till Google disables the API usage completely without
a key.

Get details about how to obtain an API key here: [https://developers.google.com/maps/documentation/javascript/get-api-key](https://developers.google.com/maps/documentation/javascript/get-api-key)

## Configuration

Add this configuration to config.yml

```yaml
chameleon_system_core:
    google_maps:
        api_key: "your API key"
```