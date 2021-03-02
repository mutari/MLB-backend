<?php

namespace App\Repository;

use App\Entity\Playlist;
use App\Entity\PlayListSong;
use App\Entity\Song;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlayListSong|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayListSong|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayListSong[]    findAll()
 * @method PlayListSong[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayListSongRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayListSong::class);
    }

    public function findAllAsArrayPlayList(Playlist $playlist) {
        return $this->createQueryBuilder('s')
            ->where('s.PlayList = :playlist')
            ->setParameter('playlist', $playlist)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    public function findByTwo(Playlist $playlist, Song $song) {
        return $this->createQueryBuilder('s')
            ->where('s.PlayList = :playlist')
            ->setParameter('playlist', $playlist)
            ->andWhere('s.Song = :song')
            ->setParameter('song', $song)
            ->getQuery()
            ->getResult();
    }

    public function deleteAllNotInIdArray(PlayList $playlist, $ids) {
        return $this->createQueryBuilder('s')
            ->delete()
            ->where('s.PlayList = :playlist')
            ->setParameter('playlist', $playlist)
            ->andWhere('id not in (:ids)')
            ->setParameter('ids', array($ids), Connection::PARAM_INT_ARRAY);
    }

    // /**
    //  * @return PlayListSong[] Returns an array of PlayListSong objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlayListSong
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
