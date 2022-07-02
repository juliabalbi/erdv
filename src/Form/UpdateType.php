<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UpdateType extends AbstractType{
	
	public function buildForm(FormBuilderInterface $builder, array $options){
		$host = '<a href="http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"].'/../assets/terminos_y_condiciones_dr_lib.pdf">Télécharger</a>';
		$builder->add('name', TextType::class, array(
			'label' => 'Prenom'
		))
		->add('surname', TextType::class, array(
			'label' => 'Nom'
		))
		->add('email', EmailType::class, array(
			'label' => 'Mail'
		))
	 
		->add('submit', SubmitType::class, array(
			'label' => 'Update'
		));
		
	}
	
}