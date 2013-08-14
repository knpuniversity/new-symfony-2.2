New Dialog Goodies: Autocomplete, Progress
==========================================

Now let's turn to something completely different: custom console commands.
Creating commands in Symfony has always been easy and powerful and if you're
new to it, just check out the `cookbook article`_ we have on the topic::

    namespace Yoda\EventBundle\Command;

    use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;

    class PlayCommand extends ContainerAwareCommand
    {
        protected function configure()
        {
            $this
                ->setName('yo:dawg')
                ->setDescription('For playing')
            ;
        }

        protected function execute(InputInterface $input, OutputInterface $output)
        {
            /** @var $dialog \Symfony\Component\Console\Helper\DialogHelper */
            $dialog = $this->getHelper('dialog');

            $favoriteThing = $dialog->ask(
                $output,
                'What do you want more of? '
            );

            $output->writeln(sprintf(
                'I heard you liked <comment>%s</comment>, so I put more <comment>%s</comment> in your <comment>%s</comment>',
                $favoriteThing,
                $favoriteThing,
                $favoriteThing
            ));
        }
    }

Symfony 2.2 added a bunch of really fun new features. Right now, we have
a simple command, let's make it more awesome!

Choose Options with the new  DialogHelper::select
-------------------------------------------------

Suppose that we want to make sure that one of a few things is chosen. We're 
already using the `DialogHelper`_ to ask for a thing. Now, let's create an 
array of our favorite items and use the ``select`` function::

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $dialog \Symfony\Component\Console\Helper\DialogHelper */
        $dialog = $this->getHelper('dialog');

        $favoriteItems = array(
            'Symfony',
            'Ice Cream',
            'Documentation',
        );

        $index = $dialog->select(
            $output,
            'What do you want more of? ',
            $favoriteItems
        );
        $favoriteThing = $favoriteItems[$index];

        $output->writeln(sprintf(
            'I heard you liked <comment>%s</comment>, so I put more <comment>%s</comment> in your <comment>%s</comment>',
            $favoriteThing,
            $favoriteThing,
            $favoriteThing
        ));
    }

When we try it, we get more of your favorite thing!

.. _symfony-cli-autocomplete:

Command-Line Auto-completion
----------------------------

Let's keep going. Another function on the dialog helper is ``askAndValidate``,
which has actually always existed. First, create a simple validation function
that makes sure the value is one of our things. Next, use the ``askAndValidate``
function instead of ``select``::

    $validation = function($thing) use ($favoriteItems) {
        if (!in_array($thing, $favoriteItems)) {
            throw new \InvalidArgumentException(sprintf(
                '"%s" is not one of my favorite things!',
                $thing
            ));
        }

        return $thing;
    };

    $favoriteThing = $dialog->askAndValidate(
        $output,
        'What do you want more of?',
        $validation,
    );

When we try it, it's pretty simple - we type anything, and it keeps asking 
us until we enter a valid value. This is actually a bit worse than using 
``select``, but stay with me!

Change the ``askAndValidate`` method slightly - passing ``false`` and ``null``
to the default max tries and default value arguments and then finally the
array of ``$favoriteItems`` next::

    $favoriteThing = $dialog->askAndValidate(
        $output,
        'What do you want more of?',
        $validation,
        false,
        null,
        $favoriteItems
    );

Try this again. At first, it looks the same. But as soon as we type anything,
it starts auto-completing our answer. How cool is that!

Showing a Progress Bar
----------------------

Let's add just one more fancy thing. A lot of times, I write console tasks
to handle long-running processes. I normally wouldn't admit this but, I can 
be a bit impatient, I always want to know how far through the process I am. 
Normally I set some variables and print out a status message. Now there's 
a much better way.

First, setup a loop that pauses in the middle randomly - this will be our
"fake" long-running process::

    foreach (str_split($favoriteThing) as $char) {
        usleep(rand(100000, 1000000));
    }

Next, grab the `ProgressHelper`_ by using the ``getHelper`` function. This
is brand new to Symfony 2.2 and it works by showing details about how far
along the process is. Start it with, well the ``start`` function, which takes
the number of "steps" as its second argument. If you were looping through
1000 database records, you'll probably set this to 1000::

    /** @var $progressHelper \Symfony\Component\Console\Helper\ProgressHelper */
    $progressHelper = $this->getHelper('progress');
    $progressHelper->start($output, strlen($favoriteThing));
    foreach (str_split($favoriteThing) as $char) {
        // ...
    }

Now, on each loop, simply call ``advance`` to move the progress bar through
one step::

    foreach (str_split($favoriteThing) as $char) {
        usleep(rand(100000, 1000000));

        $progressHelper->advance();
    }

That's it, let's run this and see what happens! This time, we get
a really cool progress bar that shows us exactly where things are.

You can even control how this looks, using a number of different functions
on the `ProgressHelper`_ class. The easiest is ``setFormat``, which let's
you choose how "verbose" the progress bar should be. Let's choose ``FORMAT_VERBOSE``
to see the most details possible::

    $progressHelper->setFormat(
        \Symfony\Component\Console\Helper\ProgressHelper::FORMAT_VERBOSE
    );

If you're not used to building your own custom console commands, they're
easy and powerful! And even if you're not using Symfony, you can use *just*
the Console component to create single-file, standalone command-line applications.

.. _`cookbook article`: http://symfony.com/doc/current/cookbook/console/console_command.html
.. _`DialogHelper`: http://symfony.com/doc/current/components/console/helpers/dialoghelper.html
.. _`ProgressHelper`: http://symfony.com/doc/current/components/console/helpers/progresshelper.html