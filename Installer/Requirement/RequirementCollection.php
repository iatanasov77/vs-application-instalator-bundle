<?php namespace Vankosoft\ApplicationInstalatorBundle\Installer\Requirement;

abstract class RequirementCollection implements \IteratorAggregate
{
    /** @var string */
    protected $label;

    /** @var Requirement[] */
    protected $requirements = [];

    public function __construct( string $label )
    {
        $this->label = $label;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator( $this->requirements );
    }

    /**
     * @return RequirementCollection
     */
    public function add( Requirement $requirement ): self
    {
        $this->requirements[] = $requirement;

        return $this;
    }
}
