<?php namespace Vankosoft\ApplicationBundle\Component;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Gedmo\Sluggable\Util as Sluggable;

/**
 * @NOTE Read this: https://stackoverflow.com/questions/18556682/generating-doctrine-slugs-manually
 */
final class SlugGenerator
{
    /** @var string */
    private $localeCode;
    
    public function __construct( RequestStack $requestStack )
    {
        $this->localeCode = 'en_US';
        // If There is a request and it have different locale
        if ( $requestStack->getMainRequest() ) {
            $this->localeCode = $requestStack->getMainRequest()->getLocale();
        }
    }
    
    public function setLocaleCode( string $localeCode ): void
    {
        $this->localeCode   = $localeCode;    
    }
    
    public function generate( $string, $separator = '-', $uppercase = false ): string
    {
        switch ( $this->localeCode ) {
            case 'bg_BG':
            case 'ru_RU':
                $slug   = SlugTransliterator\Cyrilic::transliterate( $string, $separator );
                break;
            default:
                $slug   = Sluggable\Urlizer::urlize( $string, $separator );
        }
        
        if( empty( $slug ) ) // if $string is like '=))' or 'トライアングル・サービス' an empty slug will be returned, that causes troubles and throws no exception
            return 'error, empty slug!!!';
        
        if ( $uppercase ) {
            $slug   = \strtoupper( $slug );
        }
        
        return $slug;
    }
    
    public function generateCamelCase( $string ): string
    {
        $slug = str_replace( '-', ' ', $string );
        $slug = str_replace( '_', ' ', $slug );
        $slug = ucwords( strtolower( $slug ) );
        $slug = str_replace( ' ', '', $slug );
        
        return $slug;
    }
    
    public function generateSlugByClassName( $className, $separator = '-', $uppercase = false ): string
    {
        // https://stackoverflow.com/questions/1089613/php-put-a-space-in-front-of-capitals-in-a-string-regex
        $string = \preg_replace( '/(?<!\ )[A-Z]/', ' $0', $className );
        
        return $this->generate( $string, $separator, $uppercase );
    }
}
