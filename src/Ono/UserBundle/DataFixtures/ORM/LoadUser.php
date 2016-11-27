<?php
namespace Ono\UserBundle\DataFixtures\ORM;

use Ono\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData
       extends AbstractFixture
       implements OrderedFixtureInterface, ContainerAwareInterface
{

 /**
 * @var ContainerInterface
 */
 private $container;

 /**
 * Load data fixtures with the passed EntityManager
 * @param ObjectManager $manager
 */
 public function load(ObjectManager $manager)
 {
   // Récupération des repos pour langue & pays
   $l_repo = $manager->getRepository("OnoMapBundle:Language");
   $c_repo = $manager->getRepository("OnoMapBundle:Country");

   // Récupération du UserManager
   $userManager = $this->container->get('fos_user.user_manager');

   // Création d'un Admin
   $admin = $userManager->createUser();
   $admin->setUsername('admin');
   $admin->setEmail('admin@admin.com');
   $admin->setPlainPassword('adminpass');
   $admin->setEnabled(true);
   $admin->setName("Admin");
   $admin->setFirstname("Mr");
   $admin->addRole('ROLE_ADMIN');
   $admin->setDtnaissance(date_create_from_format("Y-m-d", "1997-08-06"));
   $admin->setDescription("Admin Ono");
   $language = $l_repo->findOneByCdLanguage("fr");
   $admin->setLanguage($language);
   $country = $c_repo->findOneByCdCountry("fra");
   $admin->setCountry($country);
   $this->addReference('user-admin', $admin);
   $userManager->updateUser($admin);

   // Création d'un Admin
   $editor = $userManager->createUser();
   $editor->setUsername('editor');
   $editor->setEmail('editor@editor.com');
   $editor->setPlainPassword('editorpass');
   $editor->setEnabled(true);
   $editor->setName("Editor");
   $editor->setFirstname("Mr");
   $editor->addRole('ROLE_EDITOR');
   $editor->setDtnaissance(date_create_from_format("Y-m-d", "1997-08-06"));
   $editor->setDescription("Admin Ono");
   $language = $l_repo->findOneByCdLanguage("fr");
   $editor->setLanguage($language);
   $country = $c_repo->findOneByCdCountry("fra");
   $editor->setCountry($country);
   $this->addReference('user-editor', $editor);
   $userManager->updateUser($editor);

   // Création d'un User
   $user = $userManager->createUser();
   $user->setUsername('user');
   $user->setEmail('user@user.com');
   $user->setPlainPassword('userpass');
   $user->setEnabled(true);
   $user->addRole('ROLE_USER');
   $user->setName("User");
   $user->setFirstname("Mr");
   $user->setDtnaissance(date_create_from_format("Y-m-d", "1999-06-03"));
   $user->setDescription("User Ono");
   $language = $l_repo->findOneByCdLanguage("zh");
   $user->setLanguage($language);
   $country = $c_repo->findOneByCdCountry("chn");
   $user->setCountry($country);
   $this->addReference('user-basic', $user);
   $userManager->updateUser($user);
 }

 /**
 * Sets the container.
 * @param ContainerInterface|null $container A ContainerInterface instance or null
 */
 public function setContainer(ContainerInterface $container = null)
 {
   $this->container = $container;
 }

 /**
 * Get the order of this fixture
 * @return integer
 */
 public function getOrder()
 {
   return 3;
 }

}
