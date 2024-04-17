<?php namespace Vankosoft\ApplicationBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Webmozart\Assert\Assert;

final class VsTagsExtension extends AbstractExtension
{
    /** @return TwigFilter[] */
    public function getFilters(): array
    {
        return [
            new TwigFilter( 'vs_tags', [$this, 'decodeTags'] ),
        ];
    }
    
    public function decodeTags( ?string $tagsString ): string
    {
        Assert::string( $tagsString );
        
        $tagsArray      = \json_decode( $tagsString, true );
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