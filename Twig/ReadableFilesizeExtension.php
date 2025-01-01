<?php namespace Vankosoft\ApplicationBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Webmozart\Assert\Assert;

final class ReadableFilesizeExtension extends AbstractExtension
{
    /** @return TwigFilter[] */
    public function getFilters(): array
    {
        return [
            new TwigFilter( 'readable_filesize', [$this, 'readableFilesize'] ),
        ];
    }
    
    public function readableFilesize( float | int $bytes, int $precision = 2 ): string
    {
        Assert::greaterThanEq( $bytes, 0 );
        
        $units  = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'];
        $bytes  = max( $bytes, 0 );
        $pow    = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
        $pow    = min( $pow, count( $units ) - 1 );
        
        // Uncomment one of the following alternatives
        $bytes /= pow( 1024, $pow );
        
        return round( $bytes, $precision ) . ' ' . $units[$pow];
    }
}
