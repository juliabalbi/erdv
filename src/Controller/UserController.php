<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Visitor;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Form\RegisterType;
use App\Form\UpdateType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
 
class UserController extends AbstractController
{
   
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
		$em = $this->getDoctrine()->getManager();
		$visitor = new Visitor();
		$visitor->setIpAddress($request->getClientIp());
		$timestamp = new \DateTime();
		$timestamp = $timestamp->format('Y-m-d H:i:s');
		$visitor->setTimestamp($timestamp);
		$visitor->setIsLoggedIn($this->getUser() ? 1 : 0);
		// $visitor->setEmail($this->getUser()->getEmail());
		$visitor->setPage($request->getPathInfo());
		$em->persist($visitor);
		$em->flush();

		// Crear formulario
		$user = new User();
		$form = $this->createForm(RegisterType::class, $user);
		
		// Rellenar el objeto con los datos del form
		$form->handleRequest($request);
		
		// Comprobar si el form se ha enviado
		if($form->isSubmitted() && $form->isValid()){
			// Modificando el objeto para guardarlo
			// $user->setRole('ROLE_USER');
			// $user->setRole('ROLE_ADMIN');

			/*if($form->getData()->getUsertype() == 1) //admincms
			{
				$user->setRole('ROLE_ADMIN');
			}
			else if ($form->getData()->getUsertype() == 2) //user
			{*/
				$user->setRole('ROLE_USER');
				$user->setUsertype('2');
			/*}*/

			
			$user->setCreatedAt(new \Datetime('now'));
			
			// Cifrar contraseña
			$encoded = $encoder->encodePassword($user, $user->getPassword());
			$user->setPassword($encoded);
			
			// Guardar usuario
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();
			
			return $this->redirectToRoute('tasks');
		}
		
        return $this->render('user/register.html.twig', [
			'form' => $form->createView()
        ]);
    }

	public function listUsers(UserRepository $userRepository, Request $request){
	
		$em = $this->getDoctrine()->getManager();
		$visitor = new Visitor();
		$ip_address = $this->getIPAddress();

		// $visitor->setIpAddress($request->getClientIp());
		$visitor->setIpAddress($ip_address);

		$timestamp = new \DateTime();
		$timestamp = $timestamp->format('Y-m-d H:i:s');
		$visitor->setTimestamp($timestamp);
		$visitor->setIsLoggedIn($this->getUser() ? 1 : 0);
		$visitor->setEmail($this->getUser()->getEmail());
		$visitor->setPage($request->getPathInfo());
		$em->persist($visitor);
		$em->flush();
		$usersList = $userRepository->findAll();
		
	 
		 
		return $this->render('user/list.html.twig', [
            'users' => $usersList,
        ]); 
	}
	
	public function edit(Request $request, User $user, UserPasswordEncoderInterface $encoder, UserRepository $userRepository){
		$em = $this->getDoctrine()->getManager();
		
		$visitor = new Visitor();
		$visitor->setIpAddress($request->getClientIp());
		$timestamp = new \DateTime();
		$timestamp = $timestamp->format('Y-m-d H:i:s');
		$visitor->setTimestamp($timestamp);
		$visitor->setIsLoggedIn($this->getUser() ? 1 : 0);
		$visitor->setEmail($this->getUser()->getEmail());
		$visitor->setPage($request->getPathInfo());
		$em->persist($visitor);
		$em->flush();

		// $form = $this->createForm(RegisterType::class, $user);
		$form = $this->createForm(UpdateType::class, $user);
		
		$form->handleRequest($request);
		
		if($form->isSubmitted() && $form->isValid()){
			$em = $this->getDoctrine()->getManager();
	 
			// Cifrar contraseña
			// $decode = $encoder->decodePassword($user, $user->getPassword());
			//$encoded = $encoder->encodePassword($user, $user->getPassword());
			//$user->setPassword($encoded);

			$em->persist($user);
			$em->flush();
			
			return $this->redirectToRoute('user_list');
		}
		
		return $this->render('user/edit.html.twig',[
			'edit' => true,
			'form' => $form->createView()
		]);
	}
	
	public function delete(Request $request, User $user){
		if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
			$tasks = $em->getRepository(Task::class)->findBy(['user'=>$user]);
			foreach ($tasks as $task) {
				 $em->remove($task);
			}
            $em->remove($user);
			$em->flush();
        }

        return $this->redirectToRoute('user_list');
	}

	public function login(AuthenticationUtils $autenticationUtils){
		if ($this->getUser()) {
			return $this->redirectToRoute('my_tasks');
		}
		$error = $autenticationUtils->getLastAuthenticationError();
		
		$lastUsername = $autenticationUtils->getLastUsername();
		
		return $this->render('user/login.html.twig', array(
			'error' => $error,
			'last_username' => $lastUsername
		));
	}


	function getIPAddress() {  
		//whether ip is from the share internet  
		 if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
					$ip = $_SERVER['HTTP_CLIENT_IP'];  
			}  
		//whether ip is from the proxy  
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
		 }  
	//whether ip is from the remote address  
		else{  
				 $ip = $_SERVER['REMOTE_ADDR'];  
		 }  
		 return $ip;  
	}  


	 /**
     * @Route("/userinfo/{id}", name="userinfo", methods={"GET"})
     */
	public function userInfo(User $user)
	{
		return $this->render('user/userinfo.html.twig', array(
			'user' => $user,
		));
	}
}
