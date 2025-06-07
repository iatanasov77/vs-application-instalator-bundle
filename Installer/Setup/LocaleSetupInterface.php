<?php namespace Vankosoft\ApplicationInstalatorBundle\Installer\Setup;

use Sylius\Component\Locale\Model\LocaleInterface;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface LocaleSetupInterface
{
    public function setup( InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper ): LocaleInterface;
}
