<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Song;

/**
 * Class ApiController
 * @package App\Controller
 *
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    //https://www.php.net/manual/en/intro.imagick.php


    /**
     * @Route("/", name="api")
     */
    public function index(): Response
    {
        return new Response(json_encode([
            'hello' => 'world'
        ]));
    }

    /**
     * @Route("/get/{id}", name="get_by_id")
     */
    public function get_by_id(Song $song): Response
    {
        return new Response(json_encode([
            'get' => 'all data'
        ]));
    }
}
