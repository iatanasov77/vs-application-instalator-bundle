<?php namespace Vankosoft\ApplicationBundle\Component\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Serializer\SerializerInterface;

use Vankosoft\ApplicationBundle\Component\Form\DataTransformer\JsonTransformer;

/**
 * Handles json based input (for example multiselect tag)
 * Will only transform the json to array (and back),
 *
 * If necessary, could probably accept custom options (className) which would be used to
 * transform the data into given class via {@see SerializerInterface}
 * 
 * Format for Form Field Value
 * ---------------------------
 * {
 *     "email": "api@example.com",
 *     "password": "sylius-api"
 * }
 */
class JsonType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function getParent(): string
    {
        //return TextType::class;
        return TextareaType::class;
    }
    
    /**
     * @inheritDoc
     */
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder->addModelTransformer( new JsonTransformer() );
    }
}
