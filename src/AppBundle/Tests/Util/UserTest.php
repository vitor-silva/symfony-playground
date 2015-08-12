<?php
namespace AppBundle\Tests\Entity;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Loader;
use AppBundle\DataFixtures\ORM\LoadUserData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class UserTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        // loading User fixtures
        // https://github.com/doctrine/data-fixtures
        $loader = new Loader();
        $loader->addFixture(new LoadUserData);

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures());
    }

    /**
     * Clean up Kernel usage in this test.
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function testAdd()
    {
        $user = new User();
        $user->setUsername('test');
        $user->setEmail('test@unit.com');
        $user->setPlainPassword('test');
        $user->setEnabled(true);

        $this->em->persist($user);
        $this->em->flush();

        /** @var User */
        $userRetrieved = $this->em
            ->getRepository('AppBundle:User')
            ->createQueryBuilder('u')
            ->andWhere('u.username = :username OR u.email = :email')
            ->setParameter('username', 'test')
            ->setParameter('email', 'test@unit.com')
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->assertInstanceOf('AppBundle\Entity\User', $user);
        $this->assertEquals('test', $userRetrieved->getUsername());
        $this->assertEquals('test@unit.com', $userRetrieved->getEmail());
    }
}