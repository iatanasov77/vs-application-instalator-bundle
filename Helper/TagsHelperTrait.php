<?php namespace Vankosoft\ApplicationBundle\Helper;

trait TagsHelperTrait
{
    public function tagsToArray( ?string $tags ): array
    {
        $tagsToArray    = [];
        if ( ! $tags ) {
            return $tagsToArray;
        }
        
        $tagsArray      = \json_decode( $tags, true );
        foreach ( $tagsArray as $tag ) {
            $tagsToArray[]  = $tag['value'];
        }
        
        return $tagsToArray;
    }
    
    public function tagsToString( ?string $tags ): ?string
    {
        if( ! $tags ) {
            return null;
        }
        
        $tagsArray      = \json_decode( $tags, true );
        $decodedTags    = '';
        
        foreach ( $tagsArray as $key => $tag ) {
            $decodedTags    .= $tag['value'];
            if ( $key !== \array_key_last( $tagsArray ) ) {
                $decodedTags    .= ', ';
            }
        }
        
        return $decodedTags;
    }
}