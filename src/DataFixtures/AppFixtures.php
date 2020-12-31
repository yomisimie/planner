<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles([User::ROLE_ADMIN]);
        $admin->setPassword($this->encoder->encodePassword($admin, 'parola12345'));
        $manager->persist($admin);

        $activity = new Activity();
        $activity->setName('Normal 1 week activity');
        $activity->setCreatedBy($admin);
        $activity->setAssignedTo($admin);
        $activity->setDueDate(new DateTime('+ 1 week'));
        $activity->setImportance(Activity::IMPORTANCE_NORMAL);
        $manager->persist($activity);

        $activity = new Activity();
        $activity->setName('Normal 1 month activity');
        $activity->setCreatedBy($admin);
        $activity->setAssignedTo($admin);
        $activity->setDueDate(new DateTime('+ 1 month'));
        $activity->setImportance(Activity::IMPORTANCE_NORMAL);
        $manager->persist($activity);

        $manager->flush();
    }
}
