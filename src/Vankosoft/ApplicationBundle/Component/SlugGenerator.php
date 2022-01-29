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
        if ( $requestStack->getMasterRequest() ) {
            $this->localeCode = $requestStack->getMasterRequest()->getLocale();
        }
    }
    
    public function generate( $string ): string
    {
        switch ( $this->localeCode ) {
            case 'bg_BG':
            case 'ru_RU':
                $slug   = SlugTransliterator\Cyrilic::transliterate( $string, '-' );
                break;
            default:
                $slug   = Sluggable\Urlizer::urlize( $string, '-' );
        }
        
        if( empty( $slug ) ) // if $string is like '=))' or 'トライアングル・サービス' an empty slug will be returned, that causes troubles and throws no exception
            return 'error, empty slug!!!';
        
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
}
