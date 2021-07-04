<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

use App\Entity\Article;


class ArticleFixtures extends Fixture
{
    protected $faker;

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create('fr_FR');
        for ($i = 1; $i <= 10; $i++) {
            $article = new Article();
            $article->setTitle($this->faker->title);
            $article->setImage($this->faker->imageUrl($width = 640, $height = 480));
            $article->setContent($this->faker->text);
            $article->setCreatedAt($this->faker->dateTimeThisYear($max = 'now', $timezone = null));
            $manager->persist($article);
        }
        $manager->flush();
    }
}
