<?php namespace Vankosoft\ApplicationBundle\Component\Widget\Builder;

use Symfony\Component\HttpFoundation\Request;

/**
 * Widget Item Builder.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class Item implements ItemInterface
{
    private $id;
    private string $name        = '';
    private $description        = '';
    private string $content     = '';
    private string $template    = '';
    private array $params       = [];
    
    /**
     * @var callable
     */
    private $data;
    private array $config;

    /**
     * @var callable
     */
    private $configProcess;
    private $order;
    private array $role     = [];
    private bool $allowAnonymous    = false;
    private string $group   = '';
    private bool $active    = false;
    private int|bool $cacheExpires;

    public function __construct( $id, $cacheExpires = 3600 )
    {
        $this->id           = $id;
        $this->cacheExpires = $cacheExpires;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName( string $name ): ItemInterface
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription( ?string $description ): ItemInterface
    {
        $this->description = $description;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent( string $content ): ItemInterface
    {
        $this->content = $content;

        return $this;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }
    
    public function getParams(): array
    {
        return $this->params;
    }

    public function setTemplate( string $template, array $params = [] ): ItemInterface
    {
        $this->template = $template;
        $this->params   = $params;

        return $this;
    }

    public function getData()
    {
        if ( null !== $this->data ) {
            $data = $this->data;

            return $data( $this->config );
        }

        return false;
    }

    public function setData( callable $data ): ItemInterface
    {
        $this->data = $data;

        return $this;
    }

    public function getConfig(): ?array
    {
        return $this->config;
    }

    public function setConfig( array $config ): ItemInterface
    {
        $this->config = $config;

        return $this;
    }

    public function getConfigProcess( Request $request ): ?array
    {
        if ( null !== $this->configProcess ) {
            $data = $this->configProcess;

            return $data( $request );
        }

        return null;
    }

    public function setConfigProcess( callable $process ): ItemInterface
    {
        $this->configProcess = $process;

        return $this;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder( int $order ): ItemInterface
    {
        $this->order = $order;

        return $this;
    }

    public function getRole(): array
    {
        return $this->role;
    }

    public function setRole( array $role ): ItemInterface
    {
        $this->role = $role;

        return $this;
    }
    
    public function getAllowAnonymous(): bool
    {
        return $this->allowAnonymous;
    }
    
    public function setAllowAnonymous( bool $allow ): ItemInterface
    {
        $this->allowAnonymous = $allow;
        
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive( bool $status ): ItemInterface
    {
        $this->active = $status;

        return $this;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function setGroup( string $name ): ItemInterface
    {
        $this->group = $name;

        return $this;
    }

    public function getCacheTime(): bool|int
    {
        return $this->cacheExpires;
    }

    public function setCacheTime( bool|int $time ): ItemInterface
    {
        $this->cacheExpires = $time;

        return $this;
    }
}
