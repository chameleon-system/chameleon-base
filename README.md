Bundle for https://github.com/chameleon-system/chameleon-system

# Logging

Logging is generally used to get different runtime data of a system to a desired location.
Originally this would be one or several log files. And currently maybe the standard output (for dockers) and consequently some log
tracking system like Kibana.

The main differentiating properties of different log messages (beside their content) are log level and channel name.

With channel names it is possible to configure a desired different output for different logging messages. For example all 
messages during ordering could be logged additionally in another file or different database or to an AMQP message broker.
You would then use a channel "order" for these messages and configure a different handler for that channel.

All logging should be done with `Monolog` and the PSR4 interface `LoggerInterface`.


## Configuration

Configuration of logging generally is done in these locations:
- the `config.yml` of a specific project
- `vendor/chameleon-system/chameleon-base/src/CoreBundle/Resources/config/project-config.yml`
- the Extension class of any bundle if it implements `PrependExtensionInterface`

Any configuration of specific project needs should be done in the project's config.yml (or config_prod.yml respectively).
For example if everything should be logged to standard output a handler for stream `"php://stdout"` would be configured there.

The project-config.yml contains the one default logger configuration "main".
It logs any warning or above to the following file: `%kernel.logs_dir%/%kernel.environment%.log"`
Furthermore it contains some channel names for legacy classes (without dependency injection)

The `PrependExtensionInterface` would normally also be used for legacy classes and
their respective logging channel definitions in bundles.


The default logging channel name of any message if not otherwise specified is "app".
If you want to change this a logging channel is normally configured using the tag
**\<tag name="monolog.logger" channel="\<name\>"/\>** in the service definition that also has a dependency to a
a `LoggerInterface` class of Monolog (normally just "logger").


If you need further logging behavior you can either use the respective `Monolog` classes (ie MandrillHandler, 
MemoryProcessor or HtmlFormatter) or implement them yourself using the Monolog interfaces.

## Best practises

