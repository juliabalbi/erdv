<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Task;
use App\Entity\User;
use App\Entity\Visitor;
use App\Form\TaskType;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskController extends AbstractController
{

    public function index(Request $request)
    {
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
		// Prueba de entidades y relaciones
		$task_repo = $this->getDoctrine()->getRepository(Task::class);
		$tasks = $task_repo->findBy([], ['id' => 'DESC']);
		
        return $this->render('task/index.html.twig', [
            'tasks' => $tasks
        ]);
    }
	
	public function detail(Task $task, Request $request){
		if(!$task){
			return $this->redirectToRoute('tasks');
		}
		
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

		return $this->render('task/detail.html.twig',[
			'task' => $task
		]);
	}
	
	public function creation(Request $request, UserInterface $user){
		
		$em = $this->getDoctrine()->getManager();
		$visitor = new Visitor();
		// $visitor->setIpAddress($request->getClientIp());
		$ip_address = $this->getIPAddress();
		$visitor->setIpAddress($ip_address);
		$timestamp = new \DateTime();
		$timestamp = $timestamp->format('Y-m-d H:i:s');

	
		$visitor->setTimestamp($timestamp);
		$visitor->setIsLoggedIn($this->getUser() ? 1 : 0);
		$visitor->setEmail($this->getUser()->getEmail());
		$visitor->setPage($request->getPathInfo());
		$em->persist($visitor);
		$em->flush();
	
		$task = new Task();
		$form = $this->createForm(TaskType::class, $task);
		$form->handleRequest($request);
		
		if($form->isSubmitted() && $form->isValid()){
			 
			$task->setCreatedAt(new \Datetime());
			// $task->setCreatedAt(new \Datetime('now'));
			$task->setUser($user);
		 
			$em->persist($task);
			$em->flush();
			
			return $this->redirect($this->generateUrl('task_detail', ['id' => $task->getId()]));
		}
		
		return $this->render('task/creation.html.twig',[
			'form' => $form->createView()
		]);
	}
	
	public function myTasks(UserInterface $user, Request $request){
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

		$tasks = $user->getTasks();
				
		return $this->render('task/my-tasks.html.twig',[
			'tasks' => $tasks 
		]);	
	}
	
	public function edit(Request $request, UserInterface $user, Task $task){
		if(!$user || $user->getId() != $task->getUser()->getId()){
			return $this->redirectToRoute('tasks');
		}
		
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

		$form = $this->createForm(TaskType::class, $task);
		
		$form->handleRequest($request);
		
		if($form->isSubmitted() && $form->isValid()){
			//$task->setCreatedAt(new \Datetime('now'));
			//$task->setUser($user);
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($task);
			$em->flush();
			
			return $this->redirect($this->generateUrl('task_detail', ['id' => $task->getId()]));
		}
		
		return $this->render('task/creation.html.twig',[
			'edit' => true,
			'form' => $form->createView()
		]);
	}
	
	public function delete(UserInterface $user, Task $task, Request $request){
		if(!$user || $user->getId() != $task->getUser()->getId()){
			return $this->redirectToRoute('tasks');
		}
		
		if(!$task){
			return $this->redirectToRoute('tasks');
		}
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
		
		$em = $this->getDoctrine()->getManager();
		$em->remove($task);
		$em->flush();
		
		return $this->redirectToRoute('tasks');
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
}
