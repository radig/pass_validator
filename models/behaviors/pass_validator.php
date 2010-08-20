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
				'confirm' => 'password_confirm'
			),
			'haveConfirm' => true,
			'isSecurityPassword' => true,
			'minLength' => 4,
			'allowEmpty' => false,
			'unsetInFailure' => true
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
		$pass = $this->model->data[$this->model->name][$this->settings['fields']['password']];
		
		if($this->settings['haveConfirm'])
		{
			$confirm = $this->model->data[$this->model->name][$this->settings['fields']['confirm']];
		}
		else
		{
			$confirm = null;
		}
		
		$success = $this->isValidPassword($pass, $confirm);
		
		if($success !== true)
		{
			$this->model->validationErrors = array_merge($this->model->validationErrors, $success);
			
			if($this->settings['unsetInFailure'])
			{
				unset($this->model->data[$this->model->name][$this->settings['fields']['password']]);
				unset($this->model->data[$this->model->name][$this->settings['fields']['confirm']]);
			}
		}
		
		parent::beforeValidate($model);
		
		return true;
	}
	
	public function isValidPassword($pass, $confirm = null)
	{
		$errors = array();
		
		// caso seja uma atualização do registro (onde a senha já está em hash)
		if(isset($this->model->data[$this->model->name]['id']))
		{
			// saí da validação
			return true;
		}
		
		// caso seja permitido não preencher o campo de senha
		if($this->settings['allowEmpty'])
		{
			if(empty($pass))
			{
				return true;
			}
		}
		//caso não seja permitido
		else
		{
			if(empty($pass))
			{
				$errors[$this->settings['fields']['password']] = $this->settings['errors']['required'];
			}
		}
		
		
		// validações que dependem do campo de confirmação
		if($this->settings['haveConfirm'])
		{
			// campo de confirmação esta vazio
			if(empty($confirm))
			{
				$errors[$this->settings['fields']['confirm']] = $this->settings['errors']['confirm'];
			}
					
			// valida o tamanho da senha, depende de acesso ao campo de confirmação
			if(mb_strlen($confirm) < $this->settings['minLength'])
			{
				$errors[$this->settings['fields']['confirm']] = $this->settings['errors']['minLength'];
			}
			
			$hash = Security::hash($confirm, null, true);
			
			// valida se o hash da senha é o mesmo da confirmação
			if($pass != $hash)
			{
				$errors[$this->settings['fields']['confirm']] = $this->settings['errors']['confirm'];
			}
		}
		// caso o campo de senha não venha em hash, é possível usar o próprio campo
		else if(!$this->settings['isSecurityPassword'])
		{
			// valida o tamanho da senha, depende de acesso ao campo de confirmação
			if(mb_strlen($pass) < $this->settings['minLength'])
			{
				$errors[$this->settings['fields']['password']] = $this->settings['errors']['minLength'];
			}
		}
		
		if(empty($errors))
		{
			return true;
		}
		else
		{
			return $errors;
		}
	}
}