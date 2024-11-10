<?php namespace Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory;

interface ExampleTranslationsFactoryInterface
{
    /**
     * @return object
     */
    public function createTranslation( $entity, $localeCode, $options = [] );
}
