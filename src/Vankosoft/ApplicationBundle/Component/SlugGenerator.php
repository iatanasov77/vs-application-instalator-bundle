<?php namespace Vankosoft\ApplicationBundle\Component;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Gedmo\Sluggable\Util as Sluggable;

/**
 * @NOTE Read this: https://stackoverflow.com/questions/18556682/generating-doctrine-slugs-manually
 */
final class SlugGenerator
{
    private RequestStack $requestStack;
    
    public function __construct( RequestStack $requestStack )
    {
        $this->requestStack = $requestStack;
    }
    
    public function generate( $string ): string
    {
        switch ( $this->getMasterRequest()->getLocale() ) {
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
    
    private function getMasterRequest(): Request
    {
        $masterRequest = $this->requestStack->getMasterRequest();
        if ( null === $masterRequest ) {
            throw new \UnexpectedValueException( 'There are not any requests on request stack' );
        }
        
        return $masterRequest;
    }
}
