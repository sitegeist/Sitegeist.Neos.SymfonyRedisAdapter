# Sitegeist.Neos.SymfonyRedisAdapter

Use symfony redis cache in Neos ... for reasons
https://symfony.com/doc/current/components/cache/adapters/redis_adapter.html

## ⚠⚠⚠ Still under development ⚠⚠⚠

This package is still under development. This message will disappear once the package is ready for testing.

## Configuration   

The cache adapter is configured via the options `dsn` and `options` from the symfony adapter.
```yaml
Neos_Fusion_Content:
  backend: \Sitegeist\Neos\SymfonyRedisAdapter\Cache\SymfonyRedisAdapterBackend
  backendOptions:
    defaultLifetime: 86400
    
    # see: https://symfony.com/doc/current/components/cache/adapters/redis_adapter.html#configure-the-connection 
    dsn: 'redis://redis:6379'
    
    # see: https://symfony.com/doc/current/components/cache/adapters/redis_adapter.html#configure-the-options
    options:
      lazy: false
      persistent: 0
      persistent_id: null
      tcp_keepalive: 0
      timeout: 30
      read_timeout: 0
      retry_interval: 0
```      
