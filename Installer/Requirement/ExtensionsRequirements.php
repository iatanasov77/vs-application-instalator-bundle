<?php namespace Vankosoft\ApplicationInstalatorBundle\Installer\Requirement;

use ReflectionExtension;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ExtensionsRequirements extends RequirementCollection
{
    public function __construct( TranslatorInterface $translator )
    {
        parent::__construct( $translator->trans( 'vs_application_instalator.installer.extensions.header', [], 'VSApplicationInstalatorBundle' ) );

        $this
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.json_encode', [], 'VSApplicationInstalatorBundle' ),
                function_exists( 'json_encode' ),
                true,
                $translator->trans( 'vs_application_instalator.installer.extensions.help', ['%extension%' => 'JSON'], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.session_start', [], 'VSApplicationInstalatorBundle' ),
                function_exists( 'session_start' ),
                true,
                $translator->trans( 'vs_application_instalator.installer.extensions.help', ['%extension%' => 'session'], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.ctype', [], 'VSApplicationInstalatorBundle' ),
                function_exists( 'ctype_alpha' ),
                true,
                $translator->trans( 'vs_application_instalator.installer.extensions.help', ['%extension%' => 'ctype'], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.token_get_all', [], 'VSApplicationInstalatorBundle' ),
                function_exists( 'token_get_all' ),
                true,
                $translator->trans( 'vs_application_instalator.installer.extensions.help', ['%extension%' => 'JSON'], 'VSApplicationInstalatorBundle' )
            ) )
            ->add (new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.simplexml_import_dom', [], 'VSApplicationInstalatorBundle' ),
                function_exists( 'simplexml_import_dom' ),
                true,
                $translator->trans( 'vs_application_instalator.installer.extensions.help', ['%extension%' => 'SimpleXML'], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.apc', [], 'VSApplicationInstalatorBundle' ),
                ! ( function_exists( 'apc_store' ) && ini_get( 'apc.enabled' )) || version_compare( phpversion( 'apc' ), '3.0.17', '>=' ),
                true,
                $translator->trans( 'vs_application_instalator.installer.extensions.help', ['%extension%' => 'APC (>=3.0.17)'], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.pcre', [], 'VSApplicationInstalatorBundle' ),
                defined( 'PCRE_VERSION' ) ? ( (float ) substr( \PCRE_VERSION, 0, (int) strpos( \PCRE_VERSION, ' ') ) ) > 8.0 : false,
                true,
                $translator->trans( 'vs_application_instalator.installer.extensions.help', ['%extension%' => 'PCRE (>=8.0)'], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.php_xml', [], 'VSApplicationInstalatorBundle' ),
                class_exists( \DOMDocument::class ),
                false,
                $translator->trans( 'vs_application_instalator.installer.extensions.help', ['%extension%' => 'PHP-XML'], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.mbstring', [], 'VSApplicationInstalatorBundle' ),
                function_exists('mb_strlen'),
                false,
                $translator->trans( 'vs_application_instalator.installer.extensions.help', ['%extension%' => 'mbstring'], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.iconv', [], 'VSApplicationInstalatorBundle' ),
                function_exists( 'iconv' ),
                false,
                $translator->trans( 'vs_application_instalator.installer.extensions.help', ['%extension%' => 'iconv'], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.exif', [], 'VSApplicationInstalatorBundle' ),
                function_exists( 'exif_read_data' ),
                true,
                $translator->trans( 'vs_application_instalator.installer.extensions.help', ['%extension%' => 'exif'], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.intl', [], 'VSApplicationInstalatorBundle' ),
                extension_loaded( 'intl' ),
                true,
                $translator->trans( 'vs_application_instalator.installer.extensions.help', ['%extension%' => 'intl'], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.fileinfo', [], 'VSApplicationInstalatorBundle' ),
                extension_loaded( 'fileinfo' ),
                true,
                $translator->trans( 'vs_application_instalator.installer.extensions.help', ['%extension%' => 'fileinfo'], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.accelerator.header', [], 'VSApplicationInstalatorBundle' ),
                ! empty( ini_get( 'opcache.enable' ) ),
                false,
                $translator->trans( 'vs_application_instalator.installer.extensions.accelerator.help', [], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.pdo', [], 'VSApplicationInstalatorBundle' ),
                class_exists( 'PDO' ),
                false,
                $translator->trans( 'vs_application_instalator.installer.extensions.help', ['%extension%' => 'PDO'], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.gd', [], 'VSApplicationInstalatorBundle' ),
                defined( 'GD_VERSION' ),
                true,
                $translator->trans( 'vs_application_instalator.installer.extensions.help', ['%extension%' => 'gd'], 'VSApplicationInstalatorBundle' )
            ) )
        ;

        if ( extension_loaded( 'intl' ) ) {
            if ( defined( 'INTL_ICU_VERSION' ) ) {
                $version    = \INTL_ICU_VERSION;
            } else {
                $reflector  = new ReflectionExtension( 'intl' );

                ob_start();
                $reflector->info();
                $output = strip_tags( ob_get_clean() );

                preg_match( '/^ICU version +(?:=> )?(.*)$/m', $output, $matches );
                $version    = $matches[1];
            }

            $this->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.extensions.icu', [], 'VSApplicationInstalatorBundle' ),
                version_compare( $version, '4.0', '>=' ),
                false,
                $translator->trans( 'vs_application_instalator.installer.extensions.help', ['%extension%' => 'ICU (>=4.0)'], 'VSApplicationInstalatorBundle' )
            ) );
        }
    }
}
