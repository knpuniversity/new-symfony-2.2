Upgrading to 2.2
================

To find details about this release, the `actual blog post`_ about it is a great 
spot. Upgrading a Symfony2 project actually means upgrading the libraries 
in your `vendor/` directory. The code in your project is, well,
*your* code. Symfony2 and all its friends just sit there in ``vendor/`` and
wait for you to use them.

Updating composer.json
----------------------

The blog post contains a diff of how your ``composer.json`` needs to change.
An easier way to see this for any release is to go to the `Symfony Standard Edition Repository`_
and find the exact tag you want. Right now, 2.2.0 is the latest, so I'll select
it by clicking on the hash. From here, we can
`browse how the code looked at the moment of this release`_. And so when
we open `composer.json`, we're seeing exactly how it should look for this version:

.. code-block:: json

    {
        [ " ... parts left out ..." ],
        "require": {
            "php": ">=5.3.3",
            "symfony/symfony": "2.2.*",
            "doctrine/orm": "~2.2,>=2.2.3",
            "doctrine/doctrine-bundle": "1.2.*",
            "twig/extensions": "1.0.*",
            "symfony/assetic-bundle": "2.1.*",
            "symfony/swiftmailer-bundle": "2.2.*",
            "symfony/monolog-bundle": "2.2.*",
            "sensio/distribution-bundle": "2.2.*",
            "sensio/framework-extra-bundle": "2.2.*",
            "sensio/generator-bundle": "2.2.*",
            "jms/security-extra-bundle": "1.4.*",
            "jms/di-extra-bundle": "1.3.*"
        },
    
        [ " ... parts left out ..." ],
    
        "minimum-stability": "alpha",
        "extra": {
            "symfony-app-dir": "app",
            "symfony-web-dir": "web",
            "branch-alias": {
                "dev-master": "2.2-dev"
            }
        }
    }

Copy the contents of the ``require`` key and paste them into your ``composer.json``
file, being sure to only replace the core Symfony libraries, and not any
custom lines you may have added. In this case, the ``doctrine-fixtures-bundle``
is custom, so I'll leave it alone:

.. code-block:: json

    "require": {
        "php": ">=5.3.3",
        "symfony/symfony": "2.2.*",
        "doctrine/orm": "~2.2,>=2.2.3",
        "doctrine/doctrine-bundle": "1.2.*",
        "twig/extensions": "1.0.*",
        "symfony/assetic-bundle": "2.1.*",
        "symfony/swiftmailer-bundle": "2.2.*",
        "symfony/monolog-bundle": "2.2.*",
        "sensio/distribution-bundle": "2.2.*",
        "sensio/framework-extra-bundle": "2.2.*",
        "sensio/generator-bundle": "2.2.*",
        "jms/security-extra-bundle": "1.4.*",
        "jms/di-extra-bundle": "1.3.*",.

        "doctrine/doctrine-fixtures-bundle": "dev-master"
    },

Also, be sure you have the ``minimum-stability`` set to ``alpha``. As Stof
points out in the blog, this is because at least one of the libraries here
is still technically at an alpha state. This allows that library to be included.
Finally, add the `branch-alias`_ that maps ``dev-master`` to ``2.2-dev``.

Updating with Composer
----------------------

All we need to do now is tell Composer to re-read the ``composer.json`` file
and update everything. But wait! As you undoubtedly remember from watching
our `wildly entertaining and informative tutorial on Composer`_, we need to
run ``composer.phar update`` to do this, which *can* be a dangerous command.
Let's run ``update`` now, and then talk about what horrible things this might
be doing:

.. code-block:: bash

    php composer.phar update

Remember that running ``update`` will update *all* of our vendor libraries
to the latest versions specified in ``composer.json``. Since the ``doctrine-fixtures-bundle``
is tagged at ``dev-master``, it means that it is updating this bundle to the
latest commits on the ``master`` branch.

Instead of running a naked ``update``, you could try to specify only the
libraries you want to update:

.. code-block:: bash

    php composer.phar update symfony doctrine/orm doctrine/doctrine-bundle twig sensio jms

But since so many libraries depend on the version
of Symfony, you'll quite likely get dependency errors if you try this. Give
it a shot, but your best option is to tag as many packages to specific versions
as possible before running ``update``. If a library you use isn't tagged,
well, it's time to give the maintainer a loving poke to tag.

Upgrading your Project
----------------------

Ok! We're now on Symfony 2.2! All we need to do now is see if any of our
code needs to be updated. In fact, when I refresh the page, Symfony 2.2 kills
my project!

  Cannot import resource "@FrameworkBundle/Resources/config/routing/internal.xml"
  from "/Users/weaverryan/Sites/knp/casts/new-2.2/app/config/routing.yml". Make
  sure the "FrameworkBundle" bundle is correctly registered and loaded in the
  application kernel class.

Ok, don't panic. Head back to the blog post and find 2 UPGRADE
links. The `first UPGRADE file`_ is a big list of all the backwards-compatibility
breaks in Symfony, which may or may not affect you depending on which features
you use.

The `second UPGRADE file`_ talks about changes that you'll need to make to
the files in the Symfony Standard Distribution, which was the starting point
of your project. It mentions a change to the ``_internal`` route used for
ESI caching, which sounds just like the error we're seeing.

.. _`actual blog post`: http://symfony.com/blog/symfony-2-2-0
.. _`Symfony Standard Edition Repository`: https://github.com/symfony/symfony-standard
.. _`browse how the code looked at the moment of this release`: https://github.com/symfony/symfony-standard/tree/v2.2.0
.. _`wildly entertaining and informative tutorial on Composer`: http://knpuniversity.com/screencast/composer
.. _`branch-alias`: http://getcomposer.org/doc/articles/aliases.md
.. _`first UPGRADE file`: https://github.com/symfony/symfony/blob/2.2/UPGRADE-2.2.md
.. _`second UPGRADE file`: https://github.com/symfony/symfony-standard/blob/2.2/UPGRADE-2.2.md