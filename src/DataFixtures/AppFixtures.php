<?php

namespace App\DataFixtures;

use App\Entity\Adress;
use App\Entity\Cart;

use App\Entity\Category;
use App\Entity\Products;
use App\Entity\User;
use Faker\Factory as Faker;
use Bluemmb\Faker\PicsumPhotosProvider;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {




        $faker = Faker::create();
        $faker->addProvider(new PicsumPhotosProvider($faker));

        $categorie = new Category();
        $categorie->setName('Electronics');
        // $category->setCategoryImages($faker->imageUrl(200,200, true));
        $manager->persist($categorie);
        $this->addReference('categorie-0', $categorie);

        $manager->flush();

        // creation du panier :

        $cart = new Cart();
        $cart->setQuantity(2);
        $cart->setTVA(20);

        // Assignation du panier à l'utilisateur :
        // $user->setCart($cart);
        $manager->persist($cart);

        $this->addReference('cart', $cart);

        // $manager->flush();


        // creation de l'utilisateur :
        $user = new User();
        // $user->setFirstName('Luna');
        // $user->setLastName('Doe');
        // $user->setPhone('123456789');
        // $user->setBirthday(new \DateTime('1980-01-01'));
        $user->setEmail('Pompei@gmail.com');
        $user->setPassword($this->hasher->hashPassword(
            $user,
            'soleil1234/'
        ));
        $user->setRoles(['ROLE_User']);
        // $user->setIsVerified(true);
        // $user->setCreatedAtValue(new \DateTimeImmutable('2022-04-01 12:00:00'));
        // $user->setUpdateAtValue(new \DateTimeImmutable('now'));
        $cart = $this->getReference('cart');

        $user->setCart($cart);

        $users[] = $user;
        $manager->persist($user);
        // Store a reference to the category for later use
        $this->addReference('user-0', $user);


        $manager->flush();



        $products = [];
        for ($i = 0; $i < 10; $i++) {
            $product = new Products();

            $product->setName($faker->words(3, true));
            $product->setImage($faker->imageUrl(300, true));

            // $product->setArticleStockQuantity($faker->randomDigit());
            $product->setDescription($faker->sentence(10));
            $product->setPrice($faker->randomFloat(2, 0, 1000));
            // $product->setSellPriceHT($faker->randomFloat(2, 0, 1000));
            // $product->setSellPriceTTC($faker->randomFloat(2, 0, 1000));
            // $product->setTVA(20);
            // $product->setDetails($faker->sentence(5));
             $product->setReference($faker->randomDigit());
            $product->setSlug($faker->slug);
            $product->setCategory($categorie);
            $product->setCart($cart);
            $products[] = $product;
            $manager->persist($product);
            $this->addReference('product-' . $i, $product);
        }





        $admin = new User();
        // $admin->setFirstName('admin');
        // $admin->setLastName('magdalina');
        // $admin->setPhone('123457789');
        // $admin->setBirthday(new \DateTime('1984-05-01'));

        $admin->setEmail('PierreVolcanique@gmail.com');
        $password = $this->hasher->hashPassword($admin, 'pass_12345');
        $admin->setPassword($password);
        $admin->setRoles(['ROLE_ADMIN']);
        // $admin->setIsVerified(true);
        // $admin->setCreatedAtValue(new \DateTimeImmutable('2022-04-01 12:00:00'));
        // $admin->setUpdateAtValue(new \DateTimeImmutable('now'));

        $users[] = $admin;
        $manager->persist($admin);
        // Store a reference to the cart and category and Adress for later use
        $this->addReference('user-1', $admin);
        $manager->flush();



        $addresses = [];
        for ($i = 0; $i < count($users); $i++) {
            $address = new Adress();
            $address->setNumber($faker->numberBetween(1, 100));
            $address->setStreet($faker->streetName());

            $address->setCity($faker->city());

            $address->setCodeZip($faker->postcode());
            $address->setCountry($faker->country());
            $address->setUser($users[$i]);
            $addresses[] = $address;
            $manager->persist($address);

            $this->addReference('address-' . $i, $address);

            $manager->flush();
        }
    }
}
