<?= $resource_id ?>:
    driver: doctrine/orm
    classes:
        model:      <?= $model_class ?>
        interface:  Sylius\Component\Resource\Model\ResourceInterface
        controller: <?= $controller_class ?>
        repository: Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository
        form:       <?= $form_class ?>


