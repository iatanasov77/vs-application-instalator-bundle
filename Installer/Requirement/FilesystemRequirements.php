<?php namespace Vankosoft\ApplicationInstalatorBundle\Installer\Requirement;

use Symfony\Contracts\Translation\TranslatorInterface;

final class FilesystemRequirements extends RequirementCollection
{
    /**
     * @param string $rootDir Deprecated.
     */
    public function __construct( TranslatorInterface $translator, string $cacheDir, string $logsDir )
    {
        parent::__construct( $translator->trans( 'vs_application_instalator.installer.filesystem.header', [], 'VSApplicationInstalatorBundle' ) );

        $this
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.filesystem.cache.header', [], 'VSApplicationInstalatorBundle' ),
                is_writable( $cacheDir ),
                true,
                $translator->trans( 'vs_application_instalator.installer.filesystem.cache.help', ['%path%' => $cacheDir], 'VSApplicationInstalatorBundle' )
            ) )
            ->add( new Requirement(
                $translator->trans( 'vs_application_instalator.installer.filesystem.logs.header', [] ),
                is_writable( $logsDir ),
                true,
                $translator->trans( 'vs_application_instalator.installer.filesystem.logs.help', ['%path%' => $logsDir], 'VSApplicationInstalatorBundle' )
            ) )
        ;
    }
}
