<?php namespace IA\CmsBundle\Controller;

use Sylius\Component\Resource\Factory\FactoryInterface;
use  IA\CmsBundle\Entity\TaxonomyTerm;
use Doctrine\ORM\EntityRepository;

/**
 * Creates resources based on theirs FQCN.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TaxonomyTermFactory implements FactoryInterface
{

    /**
     * Vocabulary Entity Repository
     * 
     * @var EntityRepository
     */
    private $vr;
    
    public function __construct(EntityRepository $vr)
    {
        $this->vr = $vr;
    }
    
    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        throw new \Exception('Use method "createNewTerm($vocabularyId)"  instead.');
    }
    
    public function createNewTerm($vocabularyId)
    {
        $vocabulary = $this->vr->find($vocabularyId);
        if(!$vocabulary) {
            throw new \Exception("Cannot find this vocabulary");
        }
        return new Term($vocabulary);
    }
}

