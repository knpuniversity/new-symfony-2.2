Hostname Routing
----------------

A lot of people wanted it, so brand new in Symfony 2.2 is the ability to
match route names based on the host parameter. To see it in action, let's
duplicate the ``fragments`` route and make the first one point to a non-existent
controller:

.. code-block:: yaml

    fragments:
        path: /fragments
        defaults:
            _controller: EventBundle:NewFeatures:testFragmentsFake

    fragments2:
        path: /fragments
        defaults:
            _controller: EventBundle:NewFeatures:testFragments

Perfect! Since both routes have the same pattern, the first always wins and
our application breaks.

Pretend now that this route should only respond to ``foo.sf22.l``. To make
this happen, add a ``host`` key under the route:

.. code-block:: yaml

    fragments:
        path: /fragments
        defaults:
            _controller: EventBundle:NewFeatures:testFragmentsFake
        host:    foo.sf22.l

When we refresh, the application works, which means that the first route
is no longer matching since we're not at the foo subdomain.
The second route has no ``host`` key, it matches on any host. We can also
switch to the subdomain and see that the first route indeed matches.

.. _symfony-routing-parameters:

Using Parameters in your Routes
-------------------------------

The only problem is that we've hardcoded the domain name, and it's likely
that this domain will be different locally than beta and production.

To fix this, let's leverage a feature that was actually added in Symfony 2.1:
the ability to use parameters in routing files. Start by adding a new entry
in ``parameters.yml`` called ``base_host``:

.. code-block:: yaml

    # app/config/parameters.yml
    parameters:
        # ...
        base_host:         localhost

.. tip::

    Remember that there's nothing special about this file - you can have
    a ``parameters`` key in ``config.yml`` or any other of your configuration
    files. 

Next, update the route to use the parameter under the ``host`` key.

.. code-block:: yaml

    fragments:
        path: /fragments
        defaults:
            _controller: EventBundle:NewFeatures:testFragmentsFake
        host:    foo.%base_host%

When we try it, both subdomains behave exactly as before. Easy!