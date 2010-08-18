<?php
/** 
 * @author Cauan Cabral - cauan@radig.com.br
 *
 * @copyright 2009-2010, Radig - Soluções em TI, www.radig.com.br
 * @license MIT
 *
 * @package Radig
 * @subpackage L10n
 * 
 * Este behavior requer PHP versão >= 5.2.4
 */

App::import('Core', 'Security');

class PassValidatorBehavior extends ModelBehavior
{
	protected $model;
	
	public function setup(&$model, $config = array())
	{
		$this->model =& $model;
		
		$this->settings = array(
			'fields' => array(
				'password' => 'password',
				'confirm' => 'passoword_confirm'
			),
			'minLength' => 4,
			'allowEmpty' => false
		);
		
		if(!empty($config) && is_array($config))
		{
			$this->settings = array_merge($this->settings, $config);
		}
		
		$this->settings['errors'] = array(
			'required' => __('Campo obrigatório', true),
			'minLength' => __(sprintf('Insira pelo menos %d caracteres', $this->settings['minLength']), true),
			'confirm' => __('A confirmação não bate com a senha', true)
		);
	}
	
	public function beforeValidate(&$model)
	{
		parent::beforeValidate($model);
		
		return $this->isValidPassword($this->model->data[$this->model->name]['password'], $this->model->data[$this->model->name]['password_confirm']);
	}
	
	public function isValidPassword(&$pass, &$confirm)
	{
		// caso seja uma atualização do registro (onde a senha não pode ser alterada)
		if(isset($this->model->data[$this->model->name]['id']) && !isset($this->model->data[$this->name]['password_confirm']))
		{
			return true;
		}
		
		// campo esta vazio e isso é permitido
		if(empty($pass) && $this->settings['allowEmpty'])
		{
			return true;
		}
		
		// campo esta vazio mas isso não é permitido
		if(empty($pass) && !$this->settings['allowEmpty'])
		{
			$this->validationErrors[$this->settings['fields']['password']] = $this->settings['errors']['required'];
			
			return false;
		}
		
		// campo de confirmação esta vazio
		if(empty($confirm))
		{
			$this->validationErrors[$this->settings['fields']['confirm']] = $this->settings['errors']['confirm'];
			
			return false;
		}
				
		// valida o tamanho da senha, pela sua confirmação
		if(mb_strlen($confirm) < $this->settings['minLength'])
		{
			$this->validationErrors[$this->settings['fields']['confirm']] = $this->settings['errors']['minLength'];
			
			return false;
		}
		
		$hash = Security::hash($confirm, null, true);
		
		// valida se o hash da senha é o mesmo da confirmação
		if($passwd != $hash)
		{
			$this->validationErrors[$this->settings['fields']['confirm']] = $this->settings['errors']['confirm'];
			
			return false;
		}
		
		return true;
	}
}