<?php namespace VS\ApplicationBundle\Component;

use Gedmo\Sluggable\Util as Sluggable;

class Slug
{
    /**
     * @NOTE Read this: https://stackoverflow.com/questions/18556682/generating-doctrine-slugs-manually
     */
    public static function generate( $string )
    {
        $slug = Sluggable\Urlizer::urlize( $string, '-' );
        
        if( empty( $slug ) ) // if $string is like '=))' or 'トライアングル・サービス' an empty slug will be returned, that causes troubles and throws no exception
            return 'error, empty slug!!!';
            
            
        return $slug;
    }
}
