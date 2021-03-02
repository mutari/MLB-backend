<?php

// src/Controller/SecurityController.php
namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Emarref\Jwt\Claim;
use Emarref\Jwt\Token;
use Emarref\Jwt\Jwt;
use Emarref\Jwt\Algorithm\Hs256;
use Emarref\Jwt\Verification\Context;
use Emarref\Jwt\Encryption\Factory;
use Emarref\Jwt\Exception\VerificationException;
use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class UserController extends AbstractController
{

    public function __construct()
    {

        $this->jwt = new Jwt();
        $algorithm = new Hs256('awojfawj982uq98u8fh3wq8yq288d');
        $this->encryption = Factory::create($algorithm);

    }

    /**
     * @Route("/", name="index_main")
     */
    public function index(Request $request, LoggerInterface $logger): Response
    {
        try {
            $user = $this->getUser();

            if(!isset($user)) throw new Exception("You are not loged in");

            return new Response(json_encode([
                'user' => [
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'id' => $user->getId()
                ],
            ]));
        } catch (Exception $e) {
            return new Response(json_encode([
                'response' => $e->getMessage(),
                'status' => 403
            ]));
        }
    }

    /**
    * @Route("/login_fetch", name="login")
    */
    public function requestLoginLink(Request $request, LoggerInterface $logger, MailerInterface $mailer)
    {
        try {
            //get data
            $body = $request->request->all();

            $logger->info(json_encode($body));

            $emailAddress = $body['email'];

            $entityManager = $this->getDoctrine()->getManager();
            $userRepository = $entityManager->getRepository(user::class);
            $user = $userRepository->findOneBy(array('email' => $emailAddress));

            if(!isset($user)) throw new \Exception("No user by that mail");

            $six_digit_random_number = mt_rand(100000, 999999);

            //create jwt token
            $token = new Token();

            $token->addClaim(new Claim\Expiration(new \DateTime('1 minutes')));
            $token->addClaim(new Claim\PublicClaim('code', $six_digit_random_number));

            $serializedToken = $this->jwt->serialize($token, $this->encryption);

            $user->setCode($serializedToken);
            $entityManager->flush();

            $logger->info(json_encode($this->jwt->deserialize($serializedToken)->getPayload()));

            $email = (new Email())
                ->from('pjohansson01@yahoo.com')
                ->to($emailAddress)
                ->subject('The requested code')
                ->text('Code: '.$six_digit_random_number);

            $mailer->send($email);

            return new Response(json_encode([
                "response" => "Email sent",
                "status" => 200
            ]));
        } catch (\Exception $e) {
            return new Response(json_encode([
                "response" => $e->getMessage(),
                "status" => 404
            ]));
        }

    }

    /**
     * @Route("/createAcc", name="createAcc")
     */
    public function createNewAcount(Request $request, LoggerInterface $logger) {
        $body = $_POST;

        $logger->info(json_encode($_POST));

        $entityManager = $this->getDoctrine()->getManager();

        $user = new User();

        $user->setUsername($body['username']);
        $user->setEmail($body['email']);

        $entityManager->persist($user);
        $entityManager->flush();

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->container->get('security.token_storage')->setToken($token);
        $this->container->get('session')->set('_security_main', serialize($token));

        return new Response(json_encode([
            "response" => "acount created",
            "status" => 200
        ]));
    }

    /**
     * @Route("/logout_fetch", name="logout")
     */
    public function logout() {

        return $this->redirectToRoute('logout');

        //$this->get('security.token_storage')->setToken(null);
        //$this->get('session')->invalidate();
/*
        return new Response(json_encode([
            "response" => "you are loged out",
            "status" => 200
        ]));
  */
    }

    /**
     * @Route("/code", name="code")
     */
    public function checkCode(Request $request, LoggerInterface $logger, UserRepository $userRepository) {

        try {
            $body = $request->request->all();
            $code = $body['code'];

            $user = $userRepository->findOneBy(array('email' => $body['email']));

            $serializeToken = $user->getCode();

            $token = $this->jwt->deserialize($serializeToken);

            $context = new Context($this->encryption);

            $this->jwt->verify($token, $context);

            $serializeCode = json_encode($token->getPayload()->findClaimByName('code')->getValue());

            if($serializeCode != $code) throw new \Exception('wrong code');

            $loginToken = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->container->get('security.token_storage')->setToken($loginToken);
            $this->container->get('session')->set('_security_main', serialize($loginToken));

            return new Response(json_encode([
                "status" => 200,
                "response" => "code okey!",
                'user' => [
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'id' => $user->getId()
                ]
            ]), 200);
        } catch(VerificationException $e) {
            return new Response(json_encode([
                'response' => $e->getMessage(),
                'status' => 406
            ]), 406);
        } catch(\Exception $e) {
            return new Response(json_encode([
                'response' => $e->getMessage(),
                'status' => 403
            ]), 403);
        }
    }

    /**
     * @Route("/testNoLogin", name="testNL")
     */
    public function testNoLogin() {
        return new Response(json_encode([
            "response" => "this was a sucess",
            "status" => 69420
        ]));
    }

    /**
     * @Route("/test", name="test")
     */
    public function test() {
        return new Response(json_encode([
            "response" => "this was a sucess",
            "status" => 69420
        ]));
    }

}
