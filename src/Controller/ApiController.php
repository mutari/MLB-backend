<?php

namespace App\Controller;


use App\Repository\SongRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Gedmo\Sluggable\Util\Urlizer;
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
     * @Route("/addLyric", name="addLyric")
     */
    public function addLyric(Request $request, string $uploadDir, LoggerInterface $logger): Response
    {

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        if(0 === strpos($request->headers->get('Content-Type'), 'multipart/form-data'))
        {
            $body = $_POST;
            $file = $request->files->get('fileLyrics');
            $logger->info('upploding file'.$file->getType());

            //if file is a image
            if(1 === strpos($file->getType(), 'file')) {
                $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$file->guessExtension();
                $file->move($destination, $newFilename);
            } else {
                $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.jpg';
                $imagick = new \Imagick();

                $imagick->readImage($file);

                $imagick->writeImages($destination.'/'.$newFilename);
            }

            //save to db
            $entityManager = $this->getDoctrine()->getManager();

            $song = new Song();
            $song->setTitle($body['title'] ?? '');
            $song->setLyricsText($body['textLyrics'] ?? '');
            $song->setLyricsImagePath($newFilename ?? '');

            $entityManager->persist($song);

            $entityManager->flush();

            $response->setContent(json_encode([
                'message' => 'file upploded'
            ]));

            $response->setStatusCode(200);

            return $response;
        }

        $response->setContent(json_encode([
            'error' => 'somthig whent wrong'
        ]));
        return $response;
    }

    /**
     * @Route("/get/all", name="get_all")
     */
    public function getAll(SongRepository $songRepository) {
        $response = new Response();

        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(200);

        $response->setContent(json_encode($songRepository->findAllAsArray()));

        return $response;
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
