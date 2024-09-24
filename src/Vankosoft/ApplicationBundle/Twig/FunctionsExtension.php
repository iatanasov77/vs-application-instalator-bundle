<?php namespace Vankosoft\ApplicationBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FunctionsExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction( 'extension_loaded', [$this, 'extensionLoaded'] ),
            new TwigFunction( 'class_exists', [$this, 'classExists'] ),
        ];
    }
    
    public function extensionLoaded( string $extension ): bool
    {
        return \extension_loaded( $extension );
    }
    
    public function classExists( string $class ): bool
    {
        return \class_exists( $class );
    }
}