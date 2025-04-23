# Logging

Chameleon uses the standard logging of Symfony combined with Monolog: [https://symfony.com/doc/current/logging.html](https://symfony.com/doc/current/logging.html)

All logging should thus be done with the PSR-3 interface `Psr\Log\LoggerInterface`, using different channels where appropriate.

## Configuration

Configuration of logging generally is done in these locations:

- the `config.yml` of a specific project
- `vendor/chameleon-system/chameleon-base/src/CoreBundle/Resources/config/project-config.yml`
- the Extension class of any bundle if it implements `PrependExtensionInterface`

Any configuration of specific project needs should be done in the project's config.yml (or config_<env>.yml respectively).
For example if everything should be logged to standard output a handler for stream `"php://stdout"` would be configured there.

project-config.yml contains the default logger configuration "main".
It logs any warning or above to the following file: `%kernel.logs_dir%/%kernel.environment%.log"`
Furthermore it contains some channel names for legacy classes that cannot use dependency injection.

An implementation of `PrependExtensionInterface` can be used in a bundle to define log channels for use in legacy classes
that cannot use dependency injection.
Pre-defining a log channel there works like this:

```php
public function prepend(ContainerBuilder $container)
{
    $container->prependExtensionConfig('monolog', ['channels' => ['chameleon_order']]);
}
```

## Additional Log Data

There are two additional logging processors which add a request ID and the session ID to the context of any log message:
`\ChameleonSystem\CmsCoreLogBundle\Bridge\Monolog\RequestIdProcessor`,
`\ChameleonSystem\CmsCoreLogBundle\Bridge\Monolog\SessionIdProcessor`.

## Config Examples

Changing the log line format - here omitting the date:

```xml
<service id="chameleon_system_cms_core_log.formatter_with_stacktraces" class="Monolog\Formatter\LineFormatter" public="false">
    <!-- See \Monolog\Formatter\LineFormatter::SIMPLE_FORMAT for the default format: -->
    <argument type="string">%%channel%%.%%level_name%%: %%message%% %%context%% %%extra%%\n</argument>
    <call method="includeStacktraces"/>
</service>
```

Logging to standard output with the above line formatter:

```yaml
monolog:
  handlers:
    main:
      type: stream
      path: "php://stdout"
      formatter: chameleon_system_cms_core_log.formatter_with_stacktraces
```

Usage of a fingers-crossed logger:

```yaml
monolog:
  handlers:
     # Logs everything to the database
     database_for_fingers_crossed:
       type: service
       id: cmsPkgCore.logHandler.database

     # Adds fingers-crossed behavior to the above handler (log everything once an error occurs)
     standard:
       type: fingers_crossed
       handler: database_for_fingers_crossed
       channels:
         - "standard"
```

The logger `database_for_fingers_crossed` writes only if, during a request, a message of level warning or above is logged. In this configuration, this logger only logs channel "standard".

Also note that a fingers-crossed handler (and also a group handler) will reset the channel list of wrapped loggers:
If `database_for_fingers_crossed` had channels defined it will not have them afterwards. Only the ones on `standard` remain.