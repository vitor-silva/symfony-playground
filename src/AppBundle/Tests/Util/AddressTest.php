<?php
namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Address;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Loader;
use AppBundle\DataFixtures\ORM\LoadAddressData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class AddressTest extends KernelTestCase
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

        // loading Address fixtures
        // https://github.com/doctrine/data-fixtures
        $loader = new Loader();
        $loader->addFixture(new LoadAddressData);

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
        $address = new Address();
        $address->setStreetName('stabu');
        $address->setStreetNumber(50);
        $address->setLatitude(56.954611);
        $address->setLongitude(24.131838);

        $this->em->persist($address);
        $this->em->flush();

        /** @var Address */
        $addressRetrieved = $this->em
            ->getRepository('AppBundle:Address')
            ->createQueryBuilder('a')
            ->andWhere('a.streetName = :streetName AND a.streetNumber = :streetNumber')
            ->setParameter('streetName', 'stabu')
            ->setParameter('streetNumber', 50)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->assertInstanceOf('AppBundle\Entity\Address', $address);
        $this->assertEquals('stabu', $addressRetrieved->getStreetName());
        $this->assertEquals(50, $addressRetrieved->getStreetNumber());
    }
}