<?php namespace Vankosoft\ApplicationBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Vankosoft\ApplicationBundle\Helper\TagsHelperTrait;

final class VsTagsExtension extends AbstractExtension
{
    use TagsHelperTrait;
    
    /** @return TwigFilter[] */
    public function getFilters(): array
    {
        return [
            new TwigFilter( 'vs_tags', [$this, 'decodeTags'] ),
        ];
    }
    
    public function decodeTags( ?string $tagsString ): ?string
    {
        return $this->tagsToString( $tagsString );
    }
}