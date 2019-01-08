Logging
=======

Chameleon uses the standard logging of Symfony combined with Monolog: https://symfony.com/doc/current/logging.html

All logging should thus be done with the PSR4 interface `LoggerInterface` with appropriate channels if needed.


Configuration
-------------

Configuration of logging generally is done in these locations:

- the `config.yml` of a specific project
- `vendor/chameleon-system/chameleon-base/src/CoreBundle/Resources/config/project-config.yml`
- the Extension class of any bundle if it implements `PrependExtensionInterface`

Any configuration of specific project needs should be done in the project's config.yml (or config_<env>.yml respectively).
For example if everything should be logged to standard output a handler for stream `"php://stdout"` would be configured there.

project-config.yml contains the default logger configuration "main".
It logs any warning or above to the following file: `%kernel.logs_dir%/%kernel.environment%.log"`
Furthermore it contains some channel names for legacy classes that cannot use dependency injection.

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

Adding additional data to any log message is done with a Processor. For example you can use WebProcessor to add the REQUEST_URI.
Adding a service with the tag `monolog.processor` is sufficient for this.
Restricting it to a certain handler is done with a another argument to that tag:

.. configuration-block::
    .. code-block:: xml

        <service id="chameleon_system_cms_core_log.processor.web" class="Monolog\Processor\WebProcessor" public="false">
            <tag name="monolog.processor" handler="main"/>
        </service>

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

The logger `database_for_fingers_crossed` writes only if, during a request, a message of level warning or above is logged. In this configuration, this logger only logs channel "standard".

Also note that a fingers_crossed handler (and also a group handler) will reset the channel list of the wrapped logger(s):
If `database_for_fingers_crossed` had channels defined it will not have them afterwards. Only the ones on `standard` remain.
