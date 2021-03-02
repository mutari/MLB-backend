<?php

namespace App\Controller;


use App\Entity\Playlist;
use App\Entity\PlayListSong;
use App\Repository\PlaylistRepository;
use App\Repository\PlayListSongRepository;
use App\Repository\SongRepository;
use ContainerIpx57Vo\getPlayListSongRepositoryService;
use Exception;
use Imagick;
use Monolog\Logger;
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
     * @Route("/addLyric", name="addLyric")
     */
    public function addLyric(Request $request, LoggerInterface $logger): Response
    {

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        //get database manager
        $entityManager = $this->getDoctrine()->getManager();

        $song = new Song();

        if(0 === strpos($request->headers->get('Content-Type'), 'multipart/form-data'))
        {
            $body = $_POST;
            $file = $request->files->get('fileLyrics');
            $mp3File = $request->files->get("fileMP3");

            if(isset($file)) {
                $song->setLyricsImagePath($this->uploadLyricsFile($file)['fileName'] ?? '');
            }

            if(isset($mp3File)) {
                $logger->info('start mp3 upload'.$mp3File);

                $song->setMp3Path($this->uploadMP3($mp3File)['fileName'] ?? '');
            }

            $song->setTitle($body['title'] ?? '');
            $song->setLyricsText($body['textLyrics'] ?? '');
            $song->setUser($this->getUser());

            $entityManager->persist($song);

            $entityManager->flush();

            $response->setContent(json_encode([
                'song' => [
                    'id' => $song->getId(),
                    'lyrics_text' => $song->getLyricsText(),
                    'lyrics_image_path' => $song->getLyricsImagePath(),
                    'title' => $song->getTitle(),
                    'mp3_path' => $song->getMp3Path()
                ],
                'message' => 'file upploded',
                'status' => 200
            ]));

            $response->setStatusCode(200);

            return $response;
        }

        $response->setContent(json_encode([
            'error' => 'somthig whent wrong',
            'status' => 400
        ]));
        return $response;
    }

    /**
     * @Route("/updateLyrics", name="updateLyric")
     */
    public function updateLyric(Request $request, LoggerInterface $logger): Response
    {
        $body = $_POST;

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $logger->info(json_encode($body));

        //get database manager
        $entityManager = $this->getDoctrine()->getManager();

        $song = $entityManager->getRepository(Song::class)->find($body['id']);

        if(0 === strpos($request->headers->get('Content-Type'), 'multipart/form-data'))
        {
            $file = $request->files->get('fileLyrics');
            $mp3File = $request->files->get("fileMP3");

            if(isset($file)) {
                $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
                unlink($destination.'/'.$song->getLyricsImagePath());
                $song->setLyricsImagePath($this->uploadLyricsFile($file)['fileName'] ?? '');
            }

            if(isset($mp3File)) {
                $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
                unlink($destination.'/'.$song->getMp3Path());
                $song->setMp3Path($this->uploadMP3($mp3File)['fileName'] ?? '');
            }

            $body['title'] ? $song->setTitle($body['title']) : '';
            $body['textLyrics'] ? $song->setLyricsText($body['textLyrics']) : '';

            $entityManager->persist($song);

            $entityManager->flush();

            $response->setContent(json_encode([
                'song' => [
                    'id' => $song->getId(),
                    'lyrics_text' => $song->getLyricsText(),
                    'lyrics_image_path' => $song->getLyricsImagePath(),
                    'title' => $song->getTitle(),
                    'mp3_path' => $song->getMp3Path()
                ],
                'message' => 'file upploded',
                'status' => 200
            ]));

            $response->setStatusCode(200);

            return $response;
        }

        $response->setContent(json_encode([
            'error' => 'somthig whent wrong',
            'status' => 400
        ]));
        return $response;
    }

    /**
     * @Route("/get/all", name="get_all")
     */
    public function getAll(LoggerInterface $logger, SongRepository $songRepository, PlaylistRepository $playlistRepository, PlayListSongRepository $playListSongRepository) {
        $response = new Response();

        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(200);

        $playlists = $playlistRepository->findAllAsArrayUser($this->getUser());

        foreach($playlists as &$value) {
            foreach($playListSongRepository->findAllAsArrayPlayList($playlistRepository->find($value['id'])) as $key=>$v) {
                $playListSong = $playListSongRepository->findOneBy(['id' => $v]);
                $value['songs'][$key] = ['id' => $playListSong->getSong()->getId(), 'LineNumber' => $playListSong->getNumberInList()];
            }
            //$value['songs'] = $playListSongRepository->findAllAsArrayPlayList($playlistRepository->find($value['id']));
        }

        $logger->info(json_encode($playlists));

        unset($value);

        $response->setContent(json_encode([
            'songs' => $songRepository->findAllAsArrayUser($this->getUser()),
            'playlists' => $playlists
        ]));

        return $response;
    }

    /**
     * @Route("/del/{id}", name="delete")
     */
    public function delete(Song $song) {

        try {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($song);

            $entityManager->flush();

            return new Response(json_encode([
                "message" => "song delete",
                "status" => 200
            ]));

        } catch(Exception $e) {
            return new Response(json_encode([
                "message" => $e->getMessage(),
                "status" => 400
            ]));
        }

    }

    /**
     * @Route("/delPlayList/{id}", name="deletePlaylist")
     */
    public function deletePlaylist(Playlist $playlist) {

        try {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->remove($playlist);

            $entityManager->flush();

            return new Response(json_encode([
                "message" => "playlist deleted",
                "status" => 200
            ]));

        } catch(Exception $e) {
            return new Response(json_encode([
                "message" => $e->getMessage(),
                "status" => 400
            ]));
        }

    }

    /**
     * @Route("/addSongToPlaylist", name="addSongToPlaylist")
     */
    public function addSongToPlaylist(PlaylistRepository $playlistRepository, SongRepository $songRepository) {
        try {

            $body = $_POST;
            $SongID = $_POST['songID'];
            $PlayListID = $_POST['PlayListID'];

            $entityManager = $this->getDoctrine()->getManager();

            $playListSong = new PlayListSong();
            $playListSong->setPlayList($playlistRepository->find($PlayListID));
            $playListSong->setSong($songRepository->find($SongID));

            $entityManager->persist($playListSong);
            $entityManager->flush();

            return new Response(json_encode([
                'response' => "Song added to playlist",
                'status' => 200
            ]));

        } catch(Exception $e) {
            return new Response(json_encode([
                'response' => $e->getMessage(),
                'status' => 400
            ]));
        }
    }

    /**
     * @Route("/createPlayList", name="playList")
     */
    public function createNewPlaylist() {
        try {
            $body = $_POST;
            $name = $_POST['name'];

            $entityManager = $this->getDoctrine()->getManager();

            $playList = new Playlist();

            $playList->setName($name);
            $playList->setAuthor($this->getUser());
            $playList->setCreatedDate(new \DateTime());

            $entityManager->persist($playList);
            $entityManager->flush();

            return new Response(json_encode([
                'response' => [
                    'id' => $playList->getId(),
                    'name' => $playList->getName(),
                    'createdDate' => $playList->getCreatedDate()
                ],
                'status' => 200
            ]));
        } catch(Exception $e) {
            return new Response(json_encode([
                'response' => $e->getMessage(),
                'status' => 400
            ]));
        }
    }

    /**
     * @Route("/updatePlayList", name="updatePlayList")
     */
    public function updatePlayList(LoggerInterface $logger, PlaylistRepository $playlistRepository, SongRepository $songRepository, PlayListSongRepository $playListSongRepository) {
        $body = $_POST;

        $entityManager = $this->getDoctrine()->getManager();

        //need to rewrite this part (i think that i can do one doctine call instead of 3)
        $playlist = $playlistRepository->find($body['id']);

        $playlist->setName($body['name']);

        $songIds = json_decode(stripslashes($body['songOrder']));

        $playlistsongs = $playListSongRepository->findBy(['PlayList' => $playlist]);

        foreach($playlistsongs as $key=>$playListSong) {
            if(in_array($playListSong->getSong()->getId(), $songIds)) {
                //update pos in list
                if($playListSong->getNumberInList() != $key) {
                    $playListSong->setNumberInList($key);
                    $entityManager->persist($playListSong);
                }
            } else {
                //remove list
                $entityManager->remove($playListSong);
            }
        }

        $entityManager->persist($playlist);

        $entityManager->flush();

        return new Response(json_encode([
            'response' => $body,
            'status' => 200
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


    public function uploadMP3($mp3File) {
        $extension = $mp3File->guessExtension();
        $newFilename = '';

        if($extension == 'mp3') {
            $destination = $this->getParameter('kernel.project_dir').'/public/sound';
            $originalFilename = pathinfo($mp3File->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$extension;
            $mp3File->move($destination, $newFilename);
        }

        return ['fileName' => $newFilename];
    }

    public function uploadLyricsFile($file) {
        $extension = $file->guessExtension();

        $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$extension;
        $file->move($destination, $newFilename);
        if($extension === 'pdf')
        {
            $newPdfFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.jpg';

            try
            {
                //convert pdf to image
                $imagick = new Imagick();
                $imagick->readImage($destination.'/'.$newFilename);
                $imagick->writeImages($destination.'/'.$newPdfFilename, false);

                unlink($destination.'/'.$newFilename);
            } catch (Exception $e) { return ['error' >= $e->getMessage()]; }

            $newFilename = $newPdfFilename;
        }

        return ['fileName' => $newFilename];
    }
}
