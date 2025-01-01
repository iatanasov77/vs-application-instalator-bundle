<?php namespace Vankosoft\ApplicationBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Webmozart\Assert\Assert;

use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface;
use Vankosoft\ApplicationBundle\Repository\TaxonRepository;

final class TaxonsToCodesTransformer implements DataTransformerInterface
{
    /** @var TaxonRepository */
    private $taxonRepository;
    
    public function __construct( TaxonRepository $taxonRepository )
    {
        $this->taxonRepository  = $taxonRepository;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function transform( $value ): Collection
    {
        Assert::nullOrIsArray( $value );

        if ( empty( $value ) ) {
            return new ArrayCollection();
        }

        return new ArrayCollection( $this->taxonRepository->findBy( ['code' => $value] ) );
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function reverseTransform( $value ): array
    {
        Assert::isInstanceOf( $value, Collection::class );

        return array_map( fn ( TaxonInterface $taxon ) => $taxon->getCode(), $value->toArray() );
    }
}
