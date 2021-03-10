<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {


    //Read each feed's items
    $xml = simplexml_load_file("https://www.iutbayonne.univ-pau.fr/rss/news");
    $articles = $xml->xpath("//item");

    foreach ($articles as $key => $article) {
        $htmlContent = $article->description;

        //récupération image
        preg_match('/<img typeof="foaf:Image" class="img-responsive" src="(.*?)" width="100" height="100" alt="" \/>/s', $htmlContent, $matchImage);
        if(array_key_exists(1, $matchImage)){
            $article->image = $matchImage[1];
        }

        //récupération description
        preg_match('/<p>(.*?)<\/p>/s', $htmlContent, $matchDescription);
        if(array_key_exists(1, $matchDescription)){
            $article->description = $matchDescription[1];
        }

        if(sizeof($matchImage) == 0){
            unset($articles[$key]);
        }
    }







    return $this->render('home/index.html.twig', [ "articles" => $articles ]);
    }
}
