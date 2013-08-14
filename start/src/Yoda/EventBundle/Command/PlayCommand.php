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

        $favoriteThing = $dialog->ask($output, 'What do you want more of? ');

        $output->writeln(sprintf(
            'I heard you liked <comment>%s</comment>, so I put more <comment>%s</comment> in your <comment>%s</comment>',
            $favoriteThing,
            $favoriteThing,
            $favoriteThing
        ));
    }
}