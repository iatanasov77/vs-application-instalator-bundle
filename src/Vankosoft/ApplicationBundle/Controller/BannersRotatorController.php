<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vankosoft\ApplicationBundle\Component\Status;

class BannersRotatorController extends AbstractController
{
    /** @var RepositoryInterface */
    protected $bannerPlaceRepository;
    
    /** @var CacheManager */
    protected $imagineCacheManager;
    
    public function __construct(
        RepositoryInterface $bannerPlaceRepository,
        CacheManager $imagineCacheManager
    ) {
        $this->bannerPlaceRepository    = $bannerPlaceRepository;
        $this->imagineCacheManager      = $imagineCacheManager;
    }
    
    public function getBannersForPlaceAction( $place, Request $request ): JsonResponse
    {
        $place  = $this->bannerPlaceRepository->findByTaxonCode( $place );
        
        $data   = [];
        if ( $place ) {
            foreach ( $place->getPublishedBanners() as $banner ) {
                $data[] = [
                    'url'   => $banner->getUrl(),
                    'img'   => $this->imagineCacheManager->getBrowserPath(
                        $banner->getImage()->getPath(),
                        $place->getImagineFilter()
                    ),
                ];
            }
        }
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data,
        ]);
    }
}
