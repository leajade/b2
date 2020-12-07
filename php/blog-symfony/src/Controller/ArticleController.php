<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/articles", name="articles")
     */
    public function index(ArticleRepository $articleRepos)
    {
        /*$article1 = new Article();
        $article2 = new Article();

        $article1->setTitle('Manon Lagon');
        $article2->setTitle('Gaston Etalon');

        $article1->setCreatedAt(new \DateTime());
        $article2->setCreatedAt(new \DateTime());


        $article1->setDescription('Pour que ça glisse');
        $article2->setDescription('Un beau manche a balai');

        $articleList = [];
        array_push($articleList, $article1, $article2);

        $articleManager = $this->getDoctrine()->getManager();
        $articleManager->persist($article1);
        $articleManager->persist($article2);
        $articleManager->flush();*/

        $articleListe = $articleRepos->findAll();

        dd($articleListe);

        return $this->render('article/index.html.twig', [
            'articleList' => $articleList,
        ]);
    }
}
