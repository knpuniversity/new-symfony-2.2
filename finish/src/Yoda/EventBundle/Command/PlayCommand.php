<?php

namespace Yoda\EventBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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

        $favoriteItems = array(
            'Symfony',
            'Ice Cream',
            'Documentation'
        );

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
            'What do you want more of? ',
            $validation,
            false,
            null,
            $favoriteItems
        );

        /** @var $progressHelper \Symfony\Component\Console\Helper\ProgressHelper */
        $progressHelper = $this->getHelper('progress');
        $progressHelper->setFormat(\Symfony\Component\Console\Helper\ProgressHelper::FORMAT_VERBOSE);
        $progressHelper->start($output, strlen($favoriteThing));
        foreach (str_split($favoriteThing) as $char) {
            usleep(rand(100000, 1000000));

            $progressHelper->advance();
        }

        $output->writeln('');
        $output->writeln(sprintf(
            'I heard you liked <comment>%s</comment>, so I put more <comment>%s</comment> in your <comment>%s</comment>',
            $favoriteThing,
            $favoriteThing,
            $favoriteThing
        ));
    }
}