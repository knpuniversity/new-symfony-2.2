Upgrade!
========

So, we heard you like Symfony, so we're showing you how to put more Symfony
in your Symfony! Now that 2.2 has been released, we wanted to take some time to show
you how to upgrade and highlight a few of our favorite features. We'll be
upgrading our 2.1 events project from part 1 of our `Getting Starting in Symfony2`_
series and experimenting with a few new features, like the new fragments
framework, caching, routing changes, console command goodies and more. We'll
be using this test page which has an inner box that uses Symfony's ``render``
and gets its content by executing another controller.

The "Big Picture" of Symfony 2.2
--------------------------------

Symfony 2.2 is special for a few reasons. First, it breaks very minimal backwards
compatibility. This means that you should be able to upgrade your project
without throwing your laptop out the window. Second, it was the first release
as part of `Symfony's new process`_: 1 release every six months, with
time for features to mature, documentation to be updated, and bugs to be
fixed. If you love when upgrades cause your application to blow up on production,
then this will be a boring release for you. If you want some great new features,
then stay tuned.

.. _`Getting Starting in Symfony2`: http://knpuniversity.com/screencast/getting-started-in-symfony2-2-1
.. _`Symfony's new process`: http://symfony.com/doc/current/contributing/community/releases.html