Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

.. code-block:: bash

    $ composer require chameleon-system/sanitycheck-chameleon-bundle "~6.0"

This command requires you to have Composer installed globally, as explained
in the `installation chapter`_ of the Composer documentation.
Be sure to adjust the version information "~6.0" to the actual version you need.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding the following lines in the `app/AppKernel.php`
file of your project:

.. code-block:: php

    <?php
    // app/AppKernel.php

    // ...

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new \ChameleonSystem\SanityCheckBundle\ChameleonSystemSanityCheckBundle(),
            new \ChameleonSystem\SanityCheckBundle\ChameleonSystemSanityCheckChameleonBundle(),
        );
    }


Step 3: Run Updates
-------------------

Run updates in the Chameleon backend.


Step 4: Create Checks
---------------------

Create checks as described in the `usage chapter`_ in the documentation of the ChameleonSystemSanityCheckBundle.

.. _installation chapter: https://getcomposer.org/doc/00-intro.md
.. _usage chapter: https://docs.chameleon-system.de/bundles/chameleon___sanitycheckbundle/usage.html