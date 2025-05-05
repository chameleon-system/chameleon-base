# Request State

The active request contains some state data that is relevant for almost all content because of the way caching and db object serialization/unserialization is handled. An example would be the current language.

Chameleon provides a unique hash for every such unique state. This state hash can be retrieved via the service `chameleon_system_core.request_state_hash_provider`.

If you need to add additional parameters to this state hash, you can do so by implementing the interface `\ChameleonSystem\CoreBundle\RequestState\Interfaces\RequestStateElementProviderInterface`, registering the class as a service and tagging it with `chameleon_system_core.request_state_element_provider`.

## Caching

The request state hash will be included whenever you generate a hash for a cache entry via `\esono\pkgCmsCache\CacheInterface::getKey` unless you explicitly request a key without the request state hash.