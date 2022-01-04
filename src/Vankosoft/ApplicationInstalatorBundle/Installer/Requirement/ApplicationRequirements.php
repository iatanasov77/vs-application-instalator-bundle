<?php  namespace Vankosoft\ApplicationInstalatorBundle\Installer\Requirement;

final class ApplicationRequirements implements \IteratorAggregate
{
    /** @var array|RequirementCollection[] */
    private $collections = [];

    /**
     * @param array|RequirementCollection[] $requirementCollections
     */
    public function __construct( array $requirementCollections )
    {
        foreach ( $requirementCollections as $requirementCollection ) {
            $this->add( $requirementCollection );
        }
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator( $this->collections );
    }

    public function add( RequirementCollection $collection ): void
    {
        $this->collections[] = $collection;
    }
}
