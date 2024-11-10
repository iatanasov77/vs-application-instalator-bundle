<?php namespace Vankosoft\ApplicationBundle\Component\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Handles transforming json to array and backward
 * 
 * Format for Form Field Value
 * ---------------------------
 * {
 *     "email": "api@example.com",
 *     "password": "sylius-api"
 * }
 */
class JsonTransformer implements DataTransformerInterface
{
    
    /**
     * @inheritDoc
     */
    public function reverseTransform( $value ): array
    {
        if ( empty( $value ) ) {
            return [];
        }
        
        return \json_decode( $value, true );
    }
    
    /**
     * @ihneritdoc
     */
    public function transform( $value ): string
    {
        if ( empty( $value ) ) {
            return \json_encode( [], JSON_PRETTY_PRINT );
        }
        
        return \json_encode( $value, JSON_PRETTY_PRINT );
    }
}
