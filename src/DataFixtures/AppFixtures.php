<?php

namespace App\DataFixtures;

use App\Entity\CommandLine;
use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Service;
use App\Entity\Category;
use App\Entity\Material;
use App\Entity\Article;

class AppFixtures extends Fixture {
// Variable USER SUPER-ADMIN
    private const SUP_ADMIN_NAME = "Bernard / Laetitia";
    private const SUP_ADMIN_EMAIL = "superadmin@admin.com";
    private const SUP_ADMIN_PASSWORD = "superadmin";

// Variable USER ADMIN
    private const ADMIN_NAME = "Romain";
    private const ADMIN_EMAIL = "admin@admin.com";
    private const ADMIN_PASSWORD = "admin";

// Variable USER EMPLOYES
    private const NAME_EMPLOYEES = ["Margot", "Mathilde", "Adrien", "Maxime"];
    private const EMAIL_EMPLOYEES = ["margot@cleanup.com", "mathilde@cleanup.com", "adrien@cleanup.com", "maxime@cleanup.com"];

// Variable USER CLIENT
    private const CLIENTS_NB = 10;
// Variable Code postaux
    private const ZIP_CODE = ['69001', '69002', '69003', '69004', '69005', '69006', '69007', '69008', '69009'];

// Variable SERVICES
    private const NAME_SERVICE = ["Repassage", "Lavage", "Réparation", "Blanchisserie", "Détachage"];
    private const PRICE_SERVICE = ["3", "5", "6", "2", "4"];
    private const DESCRIPTION_SERVICE = [
        "Découvrez la qualité exceptionnelle du service de repassage chez CleanUp'. Nos experts veillent à vous faire bénéficier d’un repassage qualifié et un service irréprochable, dans les respect des matières et des couleurs de vos vêtements. Avec des délais rapides et des tarifs transparents, nous nous engageons à utiliser les techniques adaptés et éco-responsable. Terminé les pertes de temps, dite adieu aux vêtements brûlés ou abimés. Choisissez la simplicité et la qualité avec CleanUp' pour un repassage qui dépassera sans aucun doute vos attentes.", 
        "Découvrez la différence avec CleanUp', là où chaque vêtement retrouve son éclat d'origine. Notre service de lavage va au-delà du simple nettoyage ; c'est une expérience de rafraîchissement pour vos vêtements préférés. Notre équipe dédiée utilise des techniques de pointe et des produits de qualité pour garantir un nettoyage en profondeur, préservant la texture et la couleur de vos textiles. Avec CleanUp', notre processus est transparent et efficace, avec des résultats qui parlent d'eux-mêmes. Optez pour la tranquillité d'esprit et la fraîcheur inégalée, car chaque vêtement mérite un soin exceptionnel.", 
        "CleanUp' fait appel à des couturiers expérimentés qui veillent à ce que chaque pièce retrouve son éclat d'origine. Que ce soit pour des ajustements mineurs ou des réparations plus importantes, nous traitons chaque vêtement avec le plus grand soin. Avec notre service de réparation/couture, vous pouvez prolonger la durée de vie de votre garde-robe préférée. Faites confiance à notre savoir-faire pour des résultats impeccables et redécouvrez le plaisir de porter des vêtements parfaitement ajustés.", 
        "Plongez dans la fraîcheur avec Blanchisserie Pure. Notre service de blanchisserie va au-delà du simple lavage ; c'est une expérience de pureté pour votre linge. Nous utilisons des techniques de blanchiment avancées et des produits de qualité pour garantir des résultats éclatants. Que ce soit pour les draps, les nappes ou les vêtements délicats, notre équipe dévouée assure un soin méticuleux. Avec Blanchisserie Pure, la propreté est notre priorité, et nous sommes fiers de rendre chaque tissu aussi immaculé que possible. Optez pour la fraîcheur sans compromis avec Blanchisserie Pure, car chaque tissu mérite un traitement d'exception.", 
        "Découvrez le pouvoir du détachage grâce à CleanUp', votre solution infaillible contre les taches tenaces. Nous comprenons que chaque tache est unique, c'est pourquoi notre équipe de spécialistes en détachage utilise des techniques avancées pour traiter chaque vêtement avec soin. Que ce soit pour des taches de vin rouge, d'herbe ou d'huile, nous avons la solution. Grâce à notre expertise approfondie et à des produits de détachage de qualité, nous redonnons vie à vos vêtements. Dites adieu aux taches tenaces et accueillez des vêtements impeccables. Faites confiance à notre savoir-faire pour un détachage efficace et sans tracas."
    ];
    private const IMG_SERVICE = [
        "https://media.istockphoto.com/id/1368091437/fr/photo/jeune-femme-repassant-de-pr%C3%A8s.jpg?s=612x612&w=0&k=20&c=Yvv6zc9cvLGDRlQq5igbEEInMa77ZkMTrPULOGVIvd0=", 
        "https://fac.img.pmdstatic.net/fit/http.3A.2F.2Fprd2-bone-image.2Es3-website-eu-west-1.2Eamazonaws.2Ecom.2Ffac.2F2020.2F10.2F28.2F252249e3-d591-427d-89fd-88454cfb5522.2Ejpeg/1200x900/quality/80/crop-from/center/machine-a-laver-le-top-10-des-erreurs-a-ne-plus-jamais-faire.jpeg", 
        "https://fac.img.pmdstatic.net/fit/https.3A.2F.2Fi.2Epmdstatic.2Enet.2Ffac.2F2021.2F03.2F15.2Fcf239593-878a-432a-940d-effabfea4b5e.2Ejpeg/1200x1200/quality/80/crop-from/center/quelle-machine-a-coudre-choisir-pour-debuter.jpeg", 
        "https://www.modesettravaux.fr/wp-content/uploads/modesettravaux/2021/09/shutterstock_1418013815-615x410.jpg", 
        "https://fac.img.pmdstatic.net/fit/https.3A.2F.2Fi.2Epmdstatic.2Enet.2Ffac.2F2022.2F07.2F05.2F9ac9eead-3c83-45f7-9431-16dfc94eca64.2Ejpeg/750x562/quality/80/crop-from/center/cr/wqkgaXN0b2NrcGhvdG8gLyBGZW1tZSBBY3R1ZWxsZQ%3D%3D/5-astuces-pour-retirer-une-tache-de-cafe-sur-un-vetement.jpeg"
    ];

    // Variable Category 
    private const CATEGORIES = ["Vêtement", "Vêtement déliquat", "linge de maison"];

    // Variable Matières
    private const NAME_MATERIAL = ["Cuir", "Daim", "Jeans", "Tissue", "Soie", "Coton 100%", "Cachemire", "Fourrure", "Laine", "Plume", "Synthétique", "Matière fragile", "Autre matière", "Lin", "Chanvre", "Elasthane", "Velour"];
    private const PRICE_MATERIAL = ["3", "4", "0", "0", "3", "4", "7", "12", "2", "6", "0", "15", "0", "2", "3", "0", "6"];

    // Variable Article
    private const ARTICLES =[
        "T-shirt", "T-shirt manche longue", "Chemise", "Pull", "Veste", "Manteau", "Doudoune", "Pantalon", "Jupes", "Robe légère", "Robe longue", "Tailleur",
        "Robe de soirée", "Robe de marié", "Uniforme", "Costume", "Pièce Haute Couture",
        "Couettes", "Couvertures", "Draps", "Coussins", "Tapis", "Rideaux", "Nappes"
    ];
    private const ARTICLES_PRICE =[
        "2", "2", "3", "4", "5", "8", "12", "6", "5", "7", "8", "11",
        "18", "25", "20", "30", "50",
        "23", "22", "16", "14", "16", "10", "7"
    ];

    // Variable Order

    public function __construct(
        private UserPasswordHasherInterface $hasher,
        )
    {

    }
    public function load(ObjectManager $manager): void
    {
        $faker= Factory::create('fr_FR');
        
    // Fixtures pour les utilisateurs
        // SUPER-ADMIN
        $superadmin = new User();
        $superadmin
            ->setFirstname(self::SUP_ADMIN_NAME)
            ->setLastname($faker->lastName())
            ->setEmail(self::SUP_ADMIN_EMAIL)
            ->setPassword(self::SUP_ADMIN_PASSWORD)
            ->setRoles(["ROLE_SUPER_ADMIN"])
            ->setGender($faker->randomElement(['Mr', 'Mme', 'Autres(iel)']));
        $manager->persist($superadmin);

        // ADMIN
        $admin = new User();
        $admin
            ->setEmail(self::ADMIN_EMAIL)
            ->setPassword(self::ADMIN_PASSWORD)
            ->setRoles(["ROLE_ADMIN"])
            ->setFirstname(self::ADMIN_NAME)
            ->setLastname($faker->lastName())
            ->setGender($faker->randomElement(['Mr', 'Mme', 'Autres(iel)']));
        $manager->persist($admin);

        // EMPLOYES
        for($i = 0; $i < count(self::NAME_EMPLOYEES); $i++) {
            $employee = new User;
            $employee
                ->setEmail(self::EMAIL_EMPLOYEES[$i])
                ->setPassword("test")
                ->setRoles(["ROLE_EMPLOYEE"])
                ->setFirstname(self::NAME_EMPLOYEES[$i])
                ->setLastname($faker->lastName())
                ->setGender($faker->randomElement(['Mr', 'Mme', 'Autres(iel)']));
            $manager->persist($employee);
        }

        // CLIENTS
        for($i = 0; $i < self::CLIENTS_NB; $i++) {
            $client = new User;
            $client
                ->setEmail($faker->email())
                ->setPassword("test")
                ->setRoles(["ROLE_USER"])
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName())
                ->setAddress($faker->streetAddress . ' ' . $faker->randomElement(self::ZIP_CODE) . ' ' . 'Lyon, France')
                ->setGender($faker->randomElement(['Mr', 'Mme', 'Autres(iel)'])) 
                ->setBirthday($faker->dateTimeBetween('-70 years', '-25 years')); 
            $manager->persist($client);
        }

         // Fixtures pour les services
        for($i = 0; $i < count(self::NAME_SERVICE); $i++) {
            $service = new Service();
            $service
                ->setName(self::NAME_SERVICE[$i])
                ->setPrice(self::PRICE_SERVICE[$i])
                ->setDescription(self::DESCRIPTION_SERVICE[$i])
                ->setImage(self::IMG_SERVICE[$i]);
        
            $manager->persist($service);
        }

        // Fixtures pour les catégories
        foreach(self::CATEGORIES as $e) {
            $category = new Category();
            $category
                ->setName($e);
            
            $manager->persist($category);
        }

         // Fixtures pour les matières
        for($i = 0; $i < count(self::NAME_MATERIAL); $i++) {
            $material = new Material();
            $material
                ->setName(self::NAME_MATERIAL[$i])
                ->setPrice(self::PRICE_MATERIAL[$i]);
        
            $manager->persist($material);
           // $this->addReference('category_' . $i, $material);
        }

        // Fixtures pour les articles
        for ($i = 0; $i < count(self::ARTICLES); $i++) {
            $material = new Article();
            $material
                ->setName(self::ARTICLES[$i])
                ->setPrice(self::ARTICLES_PRICE[$i]);
               // ->setCategory($this->getReference(UserFixtures::USER_REFERENCE));

            $manager->persist($material);
        }

        // Fixtures pour orders
        $order = new Order();
        $order
            // ->setState("terminé")
            ->setPaymentDate(\DateTime::createFromFormat('d/m/Y', '15/01/2024'))
            ->setDepositDate(\DateTime::createFromFormat('d/m/Y', '16/01/2024'))
            ->setTotalPriceHT(32)
            ->setTotalPriceTTC(32 * 1.20);
            
        $manager->persist($order);

        // Fixtures pour command Line
        $order = new CommandLine();
        $order
            ->setPriceHT(32);
                        
        $manager->persist($order);

        $manager->flush();
    }
}
