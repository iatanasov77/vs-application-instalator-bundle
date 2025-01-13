<?php namespace Vankosoft\ApplicationInstalatorBundle\Installer\Setup;

use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Gedmo\Translatable\TranslatableListener;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Component\Intl\Languages;

final class LocaleSetup implements LocaleSetupInterface
{
    /** @var TranslatableListener */
    private $translatableListener;
    
    /** @var RepositoryInterface */
    private $localeRepository;
    
    /** @var FactoryInterface */
    private $localeFactory;
    
    /** @var string */
    private $defaultLocale;
    
    public function __construct(
        TranslatableListener $translatableListener,
        RepositoryInterface $localeRepository,
        FactoryInterface $localeFactory,
        string $defaultLocale
    ) {
        $this->translatableListener = $translatableListener;
        $this->localeRepository     = $localeRepository;
        $this->localeFactory        = $localeFactory;
        $this->defaultLocale        = trim( $defaultLocale );
    }
    
    public function setup( InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper ): LocaleInterface
    {
        $language   = $this->getLanguageFromUser( $input, $output, $questionHelper );
        
        $output->writeln( sprintf( 'Adding <info>%s</info> Language.', $language['language'] ) );
        $output->writeln( sprintf( 'Adding <info>%s</info> locale.', $language['code'] ) );
        
        if ( $this->defaultLocale !== $language['code'] ) {
            $output->writeln( '<info>You may also need to add this locale into config/services.yaml configuration.</info>' );
        }
        
        /** @var LocaleInterface|null $existingLocale */
        $existingLocale = $this->localeRepository->findOneBy( ['code' => $language['code']] );
        if ( null !== $existingLocale ) {
            return $existingLocale;
        }
        
        /** @var LocaleInterface $locale */
        $locale = $this->localeFactory->createNew();
        
        $locale->setTranslatableLocale( $language['code'] );
        $locale->setCode( $language['code'] );
        $locale->setTitle( $language['language'] );
        
        $this->translatableListener->setDefaultLocale( $language['code'] );
        $this->translatableListener->setTranslatableLocale( $language['code'] );
        $this->localeRepository->add( $locale );
        
        return $locale;
    }
    
    private function getLanguageFromUser( InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper ): array
    {
        $code   = $this->getNewLanguageCode( $input, $output, $questionHelper );
        $name   = $this->getLanguageName( $code );
        
        while ( null === $name ) {
            $output->writeln(
                sprintf( '<comment>Language with code <info>%s</info> could not be resolved.</comment>', $code )
            );
            
            $code   = $this->getNewLanguageCode( $input, $output, $questionHelper );
            $name   = $this->getLanguageName( $code );
        }
        
        return [
            'code'      => $code,
            'language'  => $name,
        ];
    }
    
    private function getNewLanguageCode( InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper ): string
    {
        $question   = new Question( 'Language (press enter to use ' . $this->defaultLocale . '): ', $this->defaultLocale );
        
        return trim( $questionHelper->ask( $input, $output, $question ) );
    }
    
    private function getLanguageName( string $code ): ?string
    {
        $language   = $code;
        $region     = null;
        
        if ( count( explode('_', $code, 2 ) ) === 2 ) {
            [$language, $region]    = explode( '_', $code, 2 );
        }
        
        try {
            return Languages::getName( $language, $region );
        } catch ( MissingResourceException $exception ) {
            return null;
        }
    }
}
