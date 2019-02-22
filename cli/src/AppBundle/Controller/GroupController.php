<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\GroupType;
use AppBundle\Form\Type\UserType;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class GroupController extends Controller
{
    /**
     * @Route("/groups", name="groups")
     */
    public function indexAction()
    {
        $client = new Client();
        $res = $client->request('GET', 'http://localhost:8000/groups');

        return $this->render('group/index.html.twig', [
            'groups' => json_decode($res->getBody()->__toString(), true)
        ]);
    }

    /**
     * @Route("/group-create", name="group_create")
     */
    public function createUserAction(Request $request)
    {
        $form = $this->createForm(GroupType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client = new Client();
            $data = $form->getData();
            try {
                $response = $client->request('POST', 'http://localhost:8000/group', [
                    'form_params' => [
                        'name' => $data['name'],
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

        return $this->render('group/create.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/group-update/{id}", name="group_update")
     */
    public function updateUserAction(Request $request, $id)
    {
        $client = new Client();
        try {
            $response = $client->request('GET', 'http://localhost:8000/group/' . $id);
        } catch (RequestException $exception) {
            return new Response($exception->getResponse()->getReasonPhrase());
        }
        $userData = json_decode($response->getBody()->__toString(), true);

        $form = $this->createForm(GroupType::class, $userData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $client = new Client();
            $data = $form->getData();

            try {
                $response = $client->request('PUT', 'http://localhost:8000/group/' . $userData['id'], [
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


        return $this->render('group/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/group-delete/{id}", name="group_delete")
     */
    public function deleteUserAction(Request $request, $id)
    {
        $client = new Client();
        try {
            $response = $client->request('GET', 'http://localhost:8000/group/' . $id);
        } catch (RequestException $exception) {
            $this->addFlash('danger', $exception->getResponse()->getReasonPhrase());
            return $this->redirectToRoute('users');
        }
        $client = new Client();
        $groupData = json_decode($response->getBody()->__toString(), true);

        $response = $client->request('DELETE', 'http://localhost:8000/group/' . $groupData['id']);

        $text = $response->getBody()->__toString();
        $this->addFlash('info', $text);

        return $this->redirectToRoute('groups');
    }

    /**
     * @Route("/group-users/{id}", name="group_users")
     */
    public function usersAction($id)
    {
        $client = new Client();
        try {
            $response = $client->request('GET', 'http://localhost:8000/group/' . $id . '/users');
        } catch (RequestException $exception) {
            $this->addFlash('danger', $exception->getResponse()->getReasonPhrase());
            return $this->redirectToRoute('users');
        }

        return $this->render('user/index.html.twig', [
            'users' => json_decode($response->getBody()->__toString(), true)
        ]);
    }
}