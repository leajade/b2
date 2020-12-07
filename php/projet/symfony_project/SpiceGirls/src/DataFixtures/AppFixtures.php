<?php

namespace App\DataFixtures;

use App\Entity\KindsContracts;
use App\Entity\Offers;
use App\Entity\TypesContracts;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');

        $allContracts = array();
        $allTypes = array();

        foreach (array("CDI", "CDD", "FREE") as &$val) {
            $contract = new KindsContracts();
            $contract->setTitle($val);
            array_push($allContracts, $contract);
            $manager->persist($contract);
        }

        foreach (array("Temps plein", "Temps partiel") as &$val) {
            $contractType = new TypesContracts();
            $contractType->setTitle($val);
            array_push($allTypes, $contractType);
            $manager->persist($contractType);
        }
        for ($i = 1; $i <= 50; $i++) {
            $contractIndex = rand(0, sizeof($allContracts) - 1);
            $contract = $allContracts[$contractIndex];

            $offer = new Offers();
            $offer->setTitle( $faker-> jobTitle )
                ->setDescription($faker -> sentence($nbWords = 6, $variableNbWords = true))
                ->setAddress($faker -> streetAddress)
                ->setZipCode($faker -> postcode)
                ->setCity($faker -> city)
                ->setCreationDate($faker -> dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null))
                ->setUpdateDate($faker -> dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null))
                ->setTypesContracts($allTypes[rand(0, sizeof($allTypes) - 1)])
                ->setKindContract($contract);
            if ($contractIndex >= 1 and $contractIndex <= 2) {
                $offer->setEndContract($faker->dateTimeBetween($startDate = 'now', $endDate = '+1 years', $timezone = null));
            }
            $manager->persist($offer);
        }
        $manager->flush();
    }
}
