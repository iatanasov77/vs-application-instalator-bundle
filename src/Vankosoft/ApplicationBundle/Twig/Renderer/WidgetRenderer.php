<?php namespace Vankosoft\ApplicationBundle\Twig\Renderer;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use Psr\Cache\CacheItemPoolInterface;
use Vankosoft\ApplicationBundle\Component\Widget\Builder\ItemInterface;

class WidgetRenderer implements RendererInterface
{
    /** @var Environment */
    private $engine;
    
    /** @var CacheItemPoolInterface */
    private $cache;
    
    /** @var TokenStorageInterface */
    private $tokenStorage;
    
    /** @var string */
    private $baseTemplate;
    
    public function __construct(
        Environment $engine,
        CacheItemPoolInterface $cache,
        TokenStorageInterface $tokenStorage,
        string $baseTemplate
    ) {
        $this->engine       = $engine;
        $this->cache        = $cache;
        $this->tokenStorage = $tokenStorage;
        $this->baseTemplate = $baseTemplate;
    }

    /**
     * Render Widgets.
     *
     * @param $widgets ItemInterface[]
     * @param bool $base
     *
     * @return string
     */
    public function render( $widgets, bool $base = true ): string
    {
        if ( ! $widgets) {
            return false;
        }

        // Output Storage
        $output = '';

        // Get User ID
        $userId = $this->tokenStorage->getToken() ?
                    $this->tokenStorage->getToken()->getUser()->getId() :
                    0;

        foreach ( $widgets as $widget ) {
            if ( $widget->isActive() ) {
                $output .= $this->getOutput( $widget, $userId );
            }
        }

        // Render Base
        if ( $base ) {
            try {
                $output = @$this->engine->render( $this->baseTemplate, ['widgets' => $output] );
            } catch ( \Exception $e ) {
                
            }
        }

        return $output;
    }

    /**
     * Get Widget Output for Cache.
     */
    public function getOutput( ItemInterface $item, $userId ): string
    {
        if ( $item->getCacheTime() ) {
            // Get Cache Item
            $cache = $this->cache->getItem( $item->getId() . $userId );

            // Set Cache Expires
            $cache->expiresAfter( $item->getCacheTime() );

            // Save
            if ( false === $cache->isHit() ) {
                $cache->set($item->getTemplate() ? $this->engine->render( $item->getTemplate(), ['widget' => $item]) : $item->getContent() );
                $this->cache->save( $cache );
            }

            return $cache->get();
        }

        return $item->getTemplate() ? $this->engine->render( $item->getTemplate(), ['widget' => $item] ) : $item->getContent();
    }
}
