<?php namespace Vankosoft\ApplicationInstalatorBundle\Installer\Requirement;

use Symfony\Contracts\Translation\TranslatorInterface;

final class SettingsRequirements extends RequirementCollection
{
    public const RECOMMENDED_PHP_VERSION = '7.0';

    public function __construct( TranslatorInterface $translator )
    {
        parent::__construct( $translator->trans( 'vs_application_instalator.installer.settings.header', [], 'VSApplicationInstalatorBundle' ) );

        $this
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.settings.timezone', [], 'VSApplicationInstalatorBundle' ),
                $this->isOn( 'date.timezone' ),
                true,
                $translator->trans( 'vs_application_instalator.installer.settings.timezone_help', [], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.settings.version_recommended', [], 'VSApplicationInstalatorBundle' ),
                version_compare( \PHP_VERSION, self::RECOMMENDED_PHP_VERSION, '>=' ),
                false,
                $translator->trans( 'vs_application_instalator.installer.settings.version_help', [
                    '%current%'     => \PHP_VERSION,
                    '%recommended%' => self::RECOMMENDED_PHP_VERSION,
                ], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.settings.detect_unicode', [], 'VSApplicationInstalatorBundle' ),
                !$this->isOn( 'detect_unicode' ),
                false,
                $translator->trans( 'vs_application_instalator.installer.settings.detect_unicode_help', [], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.settings.session.auto_start', [], 'VSApplicationInstalatorBundle' ),
                ! $this->isOn( 'session.auto_start' ),
                false,
                $translator->trans( 'vs_application_instalator.installer.settings.session.auto_start_help', [], 'VSApplicationInstalatorBundle' )
            ) )
        ;
    }

    private function isOn( string $key ): bool
    {
        $value = ini_get( $key );

        return ! empty( $value ) && strtolower( $value ) !== 'off';
    }
}
