<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TaskType extends AbstractType{
	
	public function buildForm(FormBuilderInterface $builder, array $options){
		$builder->add('patient', TextType::class, array(
			'label' => 'Patient'
		))
		->add('doctor', TextType::class, array(
			'label' => 'Docteur'
		))
		->add('priority', ChoiceType::class, array(
			'label' => 'Priorité',
			'choices' => array(
				'Haut' => 'high',
				'moyen' => 'medium',
				'Bas' => 'low'
			)
		))
		// ->add('hours', TextType::class, array(
		// 	'label' => 'Date et Heure'
		// ))
		->add('hours', TextType::class, ['label' => 'Date et Heure', 'attr'=>['class'=>'form-control mb-4']])

		->add('submit', SubmitType::class, array(
			'label' => 'Sauvegarder'
		));
	}
	
}