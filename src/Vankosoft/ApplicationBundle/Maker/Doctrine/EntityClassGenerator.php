<?php namespace Vankosoft\ApplicationBundle\Maker\Doctrine;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @internal
 */
final class EntityClassGenerator
{
    private string $makerTemplatesPath;
    private $generator;
    private $doctrineHelper;

    public function __construct(KernelInterface $kernel, Generator $generator, DoctrineHelper $doctrineHelper)
    {
        $this->makerTemplatesPath   = $kernel->locateResource( '@VSApplicationBundle/Resources/maker' );
        $this->generator            = $generator;
        $this->doctrineHelper       = $doctrineHelper;
    }

    public function generateEntityClass(ClassNameDetails $entityClassDetails, bool $apiResource, bool $withPasswordUpgrade = false, bool $generateRepositoryClass = true, bool $broadcast = false): string
    {
        $repoClassDetails = $this->generator->createClassNameDetails(
            $entityClassDetails->getRelativeName(),
            'Repository\\',
            'Repository'
        );

        $tableName = $this->doctrineHelper->getPotentialTableName($entityClassDetails->getFullName());

        $useStatements = new UseStatementGenerator([
            [Mapping::class => 'ORM'],
            \Sylius\Component\Resource\Model\ResourceInterface::class,
        ]);

        if ($generateRepositoryClass) {
            $useStatements->addUseStatement($repoClassDetails->getFullName());
        }
        
        if ($broadcast) {
            $useStatements->addUseStatement(Broadcast::class);
        }

        if ($apiResource) {
            // @legacy Drop annotation class when annotations are no longer supported.
            $useStatements->addUseStatement(class_exists(ApiResource::class) ? ApiResource::class : \ApiPlatform\Core\Annotation\ApiResource::class);
        }

        $entityPath = $this->generator->generateClass(
            $entityClassDetails->getFullName(),
            $this->makerTemplatesPath . '/doctrine/Entity.tpl.php',
            [
                'use_statements' => $useStatements,
                'repository_class_name' => $repoClassDetails->getShortName(),
                'api_resource' => $apiResource,
                'broadcast' => $broadcast,
                'should_escape_table_name' => $this->doctrineHelper->isKeyword($tableName),
                'table_name' => $tableName,
                'doctrine_use_attributes' => $this->doctrineHelper->isDoctrineSupportingAttributes() && $this->doctrineHelper->doesClassUsesAttributes($entityClassDetails->getFullName()),
            ]
        );

        if ($generateRepositoryClass) {
            $this->generateRepositoryClass(
                $repoClassDetails->getFullName(),
                $entityClassDetails->getFullName(),
                $withPasswordUpgrade,
                true
            );
        }

        return $entityPath;
    }

    public function generateRepositoryClass(string $repositoryClass, string $entityClass, bool $withPasswordUpgrade, bool $includeExampleComments = true): void
    {
        $shortEntityClass = Str::getShortClassName($entityClass);
        $entityAlias = strtolower($shortEntityClass[0]);

        $passwordUserInterfaceName = UserInterface::class;

        if (interface_exists(PasswordAuthenticatedUserInterface::class)) {
            $passwordUserInterfaceName = PasswordAuthenticatedUserInterface::class;
        }

        $interfaceClassNameDetails = new ClassNameDetails($passwordUserInterfaceName, 'Symfony\Component\Security\Core\User');

        $useStatements = new UseStatementGenerator([
            $entityClass,
            ManagerRegistry::class,
            ServiceEntityRepository::class,
        ]);

        if ($withPasswordUpgrade) {
            $useStatements->addUseStatement([
                $interfaceClassNameDetails->getFullName(),
                PasswordUpgraderInterface::class,
                UnsupportedUserException::class,
            ]);
        }

        $this->generator->generateClass(
            $repositoryClass,
            $this->makerTemplatesPath . '/doctrine/Repository.tpl.php',
            [
                'use_statements' => $useStatements,
                'entity_class_name' => $shortEntityClass,
                'entity_alias' => $entityAlias,
                'with_password_upgrade' => $withPasswordUpgrade,
                'password_upgrade_user_interface' => $interfaceClassNameDetails,
                'include_example_comments' => $includeExampleComments,
            ]
        );
    }
}
