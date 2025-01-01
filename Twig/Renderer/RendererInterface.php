<?php namespace Vankosoft\ApplicationBundle\Twig\Renderer;

interface RendererInterface
{
    public function render( bool|array $widgets ): string;
}
