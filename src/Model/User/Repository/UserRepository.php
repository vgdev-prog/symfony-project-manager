<?php

namespace App\Model\User\Repository;

use App\Model\User\Contracts\UserRepositoryInterface;
use App\Model\User\Entity\User\User;
use App\Model\User\ValueObject\Email;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private EntityManagerInterface $entityManager,
    )
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function add(User $user): void
    {
        $this->entityManager->persist($user);
    }

    public function findByConfirmToken(string $token): ?User
    {
        return $this->findOneBy(['token' => $token]);
    }

    public function hasByNetworkIdentity(string $network, string $identity): bool
    {
        return $this->createQueryBuilder('user')
            ->select('user.id')
            ->innerJoin('user.network', 'network')
            ->where('network.network = :network')
            ->andWhere('network.identity = :identity')
            ->setParameter('network', $network)
            ->setParameter('identity', $identity)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function hasByEmail(Email $email): bool
    {
        // TODO: Implement hasByEmail() method.
    }
}
