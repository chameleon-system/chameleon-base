Logging
=======

Logging is generally used to get different runtime data of a system to a desired location.
Originally this would be one or several log files. And currently maybe the standard output (for dockers) and consequently some log
tracking system like Kibana.

The main differentiating properties of different log messages (beside their content) are log level and channel name.

With channel names it is possible to configure a desired different output for different logging messages. For example all
messages during ordering could be logged additionally in another file or different database or to an AMQP message broker.
You would then use a channel "order" for these messages and configure a different handler for that channel.

All logging should be done with `Monolog` and the PSR4 interface `LoggerInterface`.


Configuration
-------------

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
Pre-defining a log channel there works like this:

.. code-block:: php
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('monolog', ['channels' => ['chameleon_order']]);
    }


The default logging channel name of any message if not otherwise specified is "app".
If you want to change this a logging channel is normally configured using the tag
`\<tag name="monolog.logger" channel="\<name\>"/\>` in the service definition that also has a dependency to a
a `LoggerInterface` class of Monolog (normally just "logger").


If you need further logging behavior you can either use the respective `Monolog` classes (ie MandrillHandler,
MemoryProcessor or HtmlFormatter) or implement them yourself using the Monolog interfaces.

Config examples
---------------

- Changing the log (line) format - here omitting the date:

.. configuration-block::
    .. code-block:: xml
        <service id="chameleon_system_cms_core_log.formatter_with_stacktraces" class="Monolog\Formatter\LineFormatter" public="false">
            <!-- See \Monolog\Formatter\LineFormatter::SIMPLE_FORMAT for the default format: -->
            <argument type="string">%%channel%%.%%level_name%%: %%message%% %%context%% %%extra%%\n</argument>
            <call method="includeStacktraces"/>
        </service>


- Logging to standard output - with the above line formatter:

.. configuration-block::
    .. code-block:: yaml
        monolog:
          handlers:
            main:
              type: stream
              path: "php://stdout"
              formatter: chameleon_system_cms_core_log.formatter_with_stacktraces

NOTE used inside a docker the docker also prepends every log message with a warning prefix and an additional date.

- Usage of a fingers crossed logger:

.. configuration-block::
    .. code-block:: yaml
        monolog:
          handlers:
             # Logs everything to the database
             database_for_fingers_crossed:
               type: service
               id: cmsPkgCore.logHandler.database

             # Takes/replaces the above handler and amends its behavior with "fingers crossed" (log everything once an error occurs)
             standard:
               type: fingers_crossed
               handler: database_for_fingers_crossed
               channels:
                 - "standard"

NOTE logging (with `database_for_fingers_crossed`) is then only done once warning or above occurs and only for channel "standard"