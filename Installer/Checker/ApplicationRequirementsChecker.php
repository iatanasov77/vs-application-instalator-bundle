<?php namespace Vankosoft\ApplicationInstalatorBundle\Installer\Checker;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Vankosoft\ApplicationInstalatorBundle\Installer\Renderer\TableRenderer;
use Vankosoft\ApplicationInstalatorBundle\Installer\Requirement\Requirement;
use Vankosoft\ApplicationInstalatorBundle\Installer\Requirement\RequirementCollection;
use Vankosoft\ApplicationInstalatorBundle\Installer\Requirement\ApplicationRequirements;

final class ApplicationRequirementsChecker implements RequirementsCheckerInterface
{
    /** @var ApplicationRequirements */
    private $applicationRequirements;

    /** @var bool */
    private $fulfilled = true;

    public function __construct( ApplicationRequirements $applicationRequirements )
    {
        $this->applicationRequirements  = $applicationRequirements;
    }

    public function check( InputInterface $input, OutputInterface $output ): bool
    {
        $helpTable  = new TableRenderer( $output );
        $helpTable->setHeaders( ['Issue', 'Recommendation'] );

        foreach ( $this->applicationRequirements as $collection ) {
            $notFulfilledTable  = new TableRenderer($output);
            $notFulfilledTable->setHeaders( ['Requirement', 'Status'] );
            $this->checkRequirementsInCollection( $collection, $notFulfilledTable, $helpTable, $input->getOption( 'verbose' ) );
        }

        if ( ! $helpTable->isEmpty() ) {
            $helpTable->render();
        }

        return $this->fulfilled;
    }

    private function checkRequirementsInCollection(
        RequirementCollection $collection,
        TableRenderer $notFulfilledTable,
        TableRenderer $helpTable,
        $verbose
    ): void {
        /** @var Requirement $requirement */
        foreach ( $collection as $requirement ) {
            $label = $requirement->getLabel();

            if ( $requirement->isFulfilled() ) {
                $notFulfilledTable->addRow( [$label, '<info>OK!</info>'] );

                continue;
            }

            $notFulfilledTable->addRow( [$label, $this->getRequirementRequiredMessage( $requirement )] );
            $helpTable->addRow( [$label, sprintf( '<comment>%s</comment>', $requirement->getHelp() )]);
        }

        if ( $verbose || ! $this->fulfilled)  {
            $notFulfilledTable->setLabel( $collection->getLabel() );
            $notFulfilledTable->render();
        }
    }

    private function getRequirementRequiredMessage( Requirement $requirement ): string
    {
        if ( $requirement->isRequired() ) {
            $this->fulfilled    = false;

            return '<error>ERROR!</error>';
        }

        return '<comment>WARNING!</comment>';
    }
}
