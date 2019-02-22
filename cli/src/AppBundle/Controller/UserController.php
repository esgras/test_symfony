<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\UserType;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends Controller
{
    /**
     * @Route("/users", name="users")
     */
    public function indexAction()
    {
        $client = new Client();
        $res = $client->request('GET', 'http://localhost:8000/users');

        return $this->render('user/index.html.twig', [
            'users' => json_decode($res->getBody()->__toString(), true)
        ]);
    }

    /**
     * @Route("/user-create", name="user_create")
     */
    public function createUserAction(Request $request)
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client = new Client();
            $data = $form->getData();
            try {
                $response = $client->request('POST', 'http://localhost:8000/user', [
                    'form_params' => [
                        'name' => $data['name'],
                        'email' => $data['email']
                    ]
                ]);
            } catch (RequestException $exception) {
                $this->addFlash('danger', $exception->getResponse()->getReasonPhrase());
                return $this->redirect($request->getRequestUri());
            }

            $text = $response->getBody()->__toString();
            $this->addFlash('info', $text);

            return $this->redirect($request->getRequestUri());
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/user-update/{id}", name="user_update")
     */
    public function updateUserAction(Request $request, $id)
    {
        $client = new Client();
        try {
            $response = $client->request('GET', 'http://localhost:8000/user/' . $id);
        } catch (RequestException $exception) {
            return new Response($exception->getResponse()->getReasonPhrase());
        }
        $userData = json_decode($response->getBody()->__toString(), true);

        $form = $this->createForm(UserType::class, $userData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $client = new Client();
            $data = $form->getData();

            try {
            $response = $client->request('PUT', 'http://localhost:8000/user/' . $userData['id'], [
                'form_params' => [
                    'name' => $data['name'],
                    'email' => $data['email']
                ]
            ]);
            } catch (RequestException $exception) {
                $this->addFlash('danger', $exception->getResponse()->getReasonPhrase());
                return $this->redirect($request->getRequestUri());
            }

            $text = $response->getBody()->__toString();
            $this->addFlash('info', $text);

            return $this->redirect($request->getRequestUri());
        }


        return $this->render('user/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user-delete/{id}", name="user_delete")
     */
    public function deleteUserAction(Request $request, $id)
    {
        $client = new Client();
        try {
            $response = $client->request('GET', 'http://localhost:8000/user/' . $id);
        } catch (RequestException $exception) {
            $this->addFlash('danger', $exception->getResponse()->getReasonPhrase());
            return $this->redirectToRoute('users');
        }
        $client = new Client();
        $userData = json_decode($response->getBody()->__toString(), true);

        $response = $client->request('DELETE', 'http://localhost:8000/user/' . $userData['id']);

        $text = $response->getBody()->__toString();
        $this->addFlash('info', $text);

        return $this->redirectToRoute('users');

    }
}