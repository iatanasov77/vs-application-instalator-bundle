<?php namespace Vankosoft\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;

use Vankosoft\ApplicationBundle\Form\SettingsForm;
use Vankosoft\ApplicationBundle\Repository\TaxonomyRepository;
use Vankosoft\ApplicationBundle\Component\Settings\Settings as SettingsManager;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Factory\Factory;

class SettingsExtController extends AbstractController
{
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    protected $settingsManager;
    
    protected $applicationRepository;
    
    protected $settingsRepository;
    
    protected $settingsFactory;
    
    /** @var TaxonomyRepository */
    protected $taxonomyRepository;
    
    public function __construct(
        ManagerRegistry $doctrine,
        SettingsManager $settingsManager,
        EntityRepository $applicationRepository,
        EntityRepository $settingsRepository,
        Factory $settingsFactory,
        TaxonomyRepository $taxonomyRepository
    ) {
        $this->doctrine                 = $doctrine;
        $this->settingsManager          = $settingsManager;
        $this->applicationRepository    = $applicationRepository;
        $this->settingsRepository       = $settingsRepository;
        $this->settingsFactory          = $settingsFactory;
        $this->taxonomyRepository       = $taxonomyRepository;
    }
    
    public function index( int $applicationId, Request $request ): Response
    {
        $application                = $this->applicationRepository->find( $applicationId );
        $settings                   = $this->settingsRepository->getSettings( $application );
        $form                       = $this->createForm( SettingsForm::class, $settings ?: 
                                            $this->settingsFactory->createNew() );
        $taxonomyPagesCategories    = $this->taxonomyRepository->findByCode(
                                            $this->getParameter( 'vs_application.page_categories.taxonomy_code' )
                                        );
        
        return $this->render( '@VSApplication/Pages/Settings/partial/settings-form.html.twig', [
            'applicationId' => $applicationId,
            'form'          => $form->createView(),
            'pcTaxonomyId'  => $taxonomyPagesCategories ? $taxonomyPagesCategories->getId() : 0,
        ]);
    }
    
    public function handle( int $applicationId, Request $request ): Response
    {
        $form   = $this->createForm( SettingsForm::class );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() ) {
            $entity = $form->getData();
            if ( $applicationId && ( ! $entity->getApplication() ) ) {
                $entity->setApplication( $this->applicationRepository->find( $applicationId ) );
            }
            
            $em = $this->doctrine->getManager();
            $em->persist( $entity );
            $em->flush();
            
            //$this->settingsManager->clearCache( $applicationId, true );
            $this->settingsManager->saveSettings( $applicationId );
            
            return $this->redirect( $this->generateUrl( 'vs_application_settings_index' ) );
        }
        
        throw new \Exception( 'Settings Form Not Submited properly!<br /><br />' . (string) $form->getErrors( true, false ), 500 );
    }
}
