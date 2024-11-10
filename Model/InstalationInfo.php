<?php namespace Vankosoft\ApplicationInstalatorBundle\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;

class InstalationInfo implements InstalationInfoInterface
{
    use TimestampableTrait;
    
    /** @var mixed  */
    protected $id = null;
    
    /** @var string */
    protected $version;
    
    /** @var array */
    protected $data;
    
    public function __construct()
    {
        $this->createdAt    = new \DateTime();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getVersion(): string
    {
        return $this->version;
    }
    
    public function setVersion( string $version ): void
    {
        $this->version = $version;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
    
    public function setData( array $data ): void
    {
        $this->data = $data;
    }
}
