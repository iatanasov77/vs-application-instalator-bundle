<?php namespace Vankosoft\ApplicationBundle\Component\Application;

class Project
{
    const PROJECT_TYPE_APPLICATION  = 'base_application';
    const PROJECT_TYPE_CATALOG      = 'catalog_application';
    const PROJECT_TYPE_EXTENDED     = 'extended_application';
    const PROJECT_TYPE_API          = 'api_application';
    
    /** @var string */
    private $projectType;
    
    public function __construct( string $projectType )
    {
        $this->projectType  = $projectType;
    }
    
    /**
     * Return Project Type Title
     * Used In WebProfiler Data Collector
     * 
     * @return string
     */
    public function projectType(): string
    {
        switch ( $this->projectType ) {
            case self::PROJECT_TYPE_CATALOG:
                return 'Catalog';
                
                break;
            case self::PROJECT_TYPE_EXTENDED:
                return 'Extended';
                
                break;
            default:
                return 'Standard';
        }
    }
}