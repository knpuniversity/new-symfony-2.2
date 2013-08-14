Fragments, ESI and Caching
==========================

Symfony 2.2 comes with a brand new `fragments sub-framework`_, which allows
you to render small parts of your page - like our inner box - independently.
Actually, this has existed since Symfony 2.0, but was called "sub-requests".
In 2.2, the feature has been overhauled for flexibility, speed and security.

Understanding Http Caching, ESI and Fragments
---------------------------------------------

One of the best features of Symfony is its use of `Edge Side Includes`_ or
ESI. This is where different parts of your page are rendered as small ``esi`` tags. 
Before your user sees these, a middle layer parses them out, makes another
request back to your app for just that fragment, and then combines it all together.

This is the "fragments" framework at work: the page is broken down into
small pieces and each has a special URL to render it all by itself.
This lets you cache the different fragments of your page independently. 
Since the middle cache layer puts all the fragments together, your user 
has no idea you're doing all this voodoo behind the scenes.

Fragments in Symfony2
~~~~~~~~~~~~~~~~~~~~~

The easiest way to split your page into these fragments is with the ``render``
tag we're using, which gets its content by rendering another controller:

.. code-block:: jinja

    {# This is the 2.1 syntax for fragments (or sub-requests) %}
    {% render 'EventBundle:NewFeatures:inner' with {
        'color': 'lightblue'
    }, {
        'standalone': true
    } %}

There's a lot more to know about this, so check out Symfony's `Http Caching`_
chapter. The important point is that your page can be broken down into fragments,
and even though we don't have a route that points to a fragment like ``innerAction``,
there's a special URL that let's us render it independently.

Welcome fragments and ProxyListener, goodbye internal.xml
---------------------------------------------------------

Before Symfony 2.2, this special URL came from importing the ``internal.xml`` routing
file, which exposed a regular Symfony route that was capable of rendering
any controller.

.. code-block:: yaml

    # app/config/routing.yml
    _internal:
        resource: "@FrameworkBundle/Resources/config/routing/internal.xml"
        prefix:   /_internal

If you used this route, you were supposed to somehow make sure that the URL
was protected so that only your caching layer could use it. If a normal user
had access to this, they could render any controller with any arguments in
your system, which would be a bummer...

In Symfony 2.2, the ``internal.xml`` routing file is gone. Let's remove it
and replace it with a ``fragments`` key in ``config.yml``. Instead of a route,
this activates a listener that watches for any requests that start with
``/_proxy``, which is the URL that the ESI tags now render as. This alone
doesn't help security, except that the listener uses a few tricks internally,
which we'll talk more about in a moment.

.. code-block:: yaml

    # app/config/config.yml
    framework:
        esi:             ~
        fragments:       { path: /_proxy }
        # ...

The new Twig render Syntax
--------------------------

For now, let's get our application working! Aside from this configuration
change, the ``render`` tag now looks different. First, it isn't a
tag at all anymore, it's now a function that's rendered using the double-curly
brace syntax:

.. tip::

    The ``{% render %}`` tag will still be supported until Symfony 3.0.

Second, when you reference the controller, you must wrap it in a call to
a new Twig ``controller`` function. For now, I'll remove the ``standalone``
key that activated ESI:

.. code-block:: jinja

    {{ render(controller('EventBundle:NewFeatures:inner', {
        'color': 'lightblue'
    })) }}

When we refresh the page, things finally work! Looking at the timeline,
we see information about the main request and the ``inner`` fragment. This
is how things look when we're not using ESI - the full page and the ``inner``
fragment render all at once. This is called the ``default`` rendering strategy
and it's pretty straightforward.

Activating ESI
--------------

Since that's boring, let's activate ESI! To do this, just change the function
to ``render_esi``:

.. code-block:: jinja

    {{ render_esi(controller('EventBundle:NewFeatures:inner', {
        'color': 'lightblue'
    })) }}

Refresh again! It renders exactly the same, how exciting! But actually, a lot
just changed behind the scenes. The main page now renders everything
except the inner area. Instead, it prints out an ESI tag. Our caching layer
parses it, makes another request into Symfony for that piece and then combines
it all together. This is called the ESI rendering strategy, because the first
main request returns an ESI tag in place of the inner area.

Debugging ESI with X-Symfony-Cache
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

We're using `Symfony's reverse proxy in PHP`_, so all of this happens on the
server and is completely invisible to us. But if you view the network details,
you'll see an ``X-Symfony-Cache`` header, which describes what's happening
at our caching layer. You can now see two entries - one for the main page request 
and another when the caching layer requests just the inner portion.

  X-Symfony-Cache: GET /new/fragments: miss;
  GET /_proxy?_path=color%3Dlightblue%26_format=%3Dhtml%26_controller%3DEventBundle%253ANewFeatures%253Ainner: miss

Of course, we're  not actually caching either part, but you can see how each
operates independently.

Using HInclude Tags
~~~~~~~~~~~~~~~~~~~

To push things further, change the function to ``render_hinclude``.

.. code-block:: jinja

    {{ render_hinclude(controller('EventBundle:NewFeatures:inner', {
        'color': 'lightblue'
    })) }}

Refresh your page to see that the inner section has vanished! When you view
the source you'll find an HTML tag with a URL. This is called an ``hinclude`` tag, and
it works a lot like an ESI tag. In both cases, an extra request is made back
to the server to fetch the content, which allows that small piece to be cached
independently. The difference is that this tag is processed by your client
using a JavaScript library called `HInclude`_ whereas ESI is processed in
a layer somewhere inside your server architecture, invisible to the user.

Fragment URL Security
---------------------

Let's look a little bit more at the URL in the HInclude tag. If we open 
this URL directly we can see the content that will be rendered. In fact, 
regardless of whether you use the ESI or HInclude strategy, this URL is 
used to allow an outside layer to request the individual fragments.
This was activated by adding the ``fragments`` key to ``config.yml``.

So what prevents an evil user from exploiting this URL to render any controller
in our system with any parameters? Nothing! Just kidding, there are two built 
in protections: :ref:`trusted proxies<new-2.2-fragments-trusted-proxies>` and
:ref:`signed URLs<new-2.2-fragments-signed-urls>`.

Trusted Proxies
~~~~~~~~~~~~~~~

The class that handles all this magic is called ``FragmentListener``. Before
it starts serving anything from your application, it first checks to see
if the person requesting is "trusted".

.. _`new-2.2-fragments-trusted-proxies`:

If you're using a reverse proxy like Varnish, then you'll want to add its
IP address or - `CIDR`_ IP address range for the super-geeks - to your ``config.yml``
file:

.. code-block:: yaml

    framework
        trusted_proxies:
            - 192.168.12.0

.. note::

    Internally, this sets the ``Request::setTrustedProxies`` method. Currently
    it appears that IP ranges (e.g. ``192.168.12.0/23``) are respected in
    ``FragmentListener``, but aren't accepted under the ``trusted_proxies``
    key. This was fixed in `Symfony 2.3`_.

If the request comes from this IP or range, it allows it. And, if it comes from
a local address, it also allows it. In other words, if it's someone you trust,
then it's ok.

.. _`new-2.2-fragments-signed-urls`:

Signed URLs
~~~~~~~~~~~

If it's not, then it falls back to use URL signing. Notice the ``_hash``
query parameter at the end of the URL. That's generated using an application
secret and the URI. It means that if we weren't trusted, we could still access
this exact URL. But if we changed any part of it, it wouldn't match
the hash and Symfony would deny access. It's a pretty clever way to expose
parts of your application that you want, without exposing everything.

Phew! Let's change back to use the ESI strategy and keep going with some
of the other great new features in Symfony 2.2.

.. _`fragments sub-framework`: http://symfony.com/blog/new-in-symfony-2-2-the-new-fragment-sub-framework
.. _`Edge Side Includes`: http://symfony.com/doc/current/book/http_cache.html#using-edge-side-includes
.. _`Http Caching`: http://symfony.com/doc/current/book/http_cache.html
.. _`Symfony's reverse proxy in PHP`: http://symfony.com/doc/current/book/http_cache.html#symfony2-reverse-proxy
.. _`HInclude`: http://mnot.github.com/hinclude/
.. _`CIDR`: http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing
.. _`Symfony 2.3`: https://github.com/symfony/symfony/pull/7735/files