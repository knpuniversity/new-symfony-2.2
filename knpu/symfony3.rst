Upgrade to Symfony 3.0?
=======================

Now that we are upgraded to Symfony 2.2 it's time to start preparing 
your application to work with 3.0!

On each release of Symfony, some functionality is deprecated and scheduled
to be removed entirely later. For the first time, a few things
have been deprecated and scheduled to be removed in Symfony 3.0.

To see them, check out the `Symfony 3.0 CHANGELOG`_ - through any of the
2.x releases, you can check out this file and find out how to be ahead of
the game when it comes to future-compatability.

Routing Method and Scheme Changes
---------------------------------

There's not a lot in here yet, but there are a few really important things
about routing! First, let's update our route to use the ``_method`` and
``_scheme`` requirements:

.. code-block:: yaml

    fragments:
        pattern: /fragments
        defaults:
            _controller: EventBundle:NewFeatures:testFragmentsMobile
        host:    foo.%base_host%
        requirements:
            _method: GET|POST
            _scheme: https

All of this works before Symfony 2.2, and it says that this route should
only match if the HTTP method is GET or POST and should force the user to
use HTTPs.

As of 2.2, this syntax is deprecated, but won't be removed until 3.0. Let's 
update our routes to be Symfony3-compatible:

.. code-block:: yaml

    fragments:
        pattern: /fragments
        defaults:
            _controller: EventBundle:NewFeatures:testFragmentsMobile
        host:    foo.%base_host%
        methods: [GET, POST]
        schemes: https

.. _symfony-routing-pattern-path:

Pattern to Path Routing Change
------------------------------

This will work today, tomorrow, and even for any Symfony3 version. Actually,
there's one more change that's *much* more important than this: ``pattern``
has changed to ``path``:

.. code-block:: yaml

    fragments:
        path: /fragments
        defaults:
            _controller: EventBundle:NewFeatures:testFragmentsMobile
        host:    foo.%base_host%
        methods: [GET, POST]
        schemes: https

This is all just a syntax change, and if you get in the habit of using the
new way, you might just save yourself some heartache later when Symfony 3.0
is the `greatest thing since sliced bread`_.

Time to go run and tell your friends that you've upgraded to Symfony 3.0!
Ok, you haven't actually of course, but if your friends aren't programmers
they won't know what you're talking about anyways.

.. _`Symfony 3.0 CHANGELOG`: https://github.com/symfony/symfony/blob/master/UPGRADE-3.0.md
.. _`greatest thing since sliced bread`: http://en.wiktionary.org/wiki/greatest_thing_since_sliced_bread