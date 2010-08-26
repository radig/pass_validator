<?php
	
App::import('Core', 'Security');

/** 
 * @author Cauan Cabral - cauan@radig.com.br
 *
 * @copyright 2009-2010, Radig - Soluções em TI, www.radig.com.br
 * @license MIT
 *
 * @package Radig
 * @subpackage Validators
 */
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
			'preConditions' => array(),
			'haveConfirm' => true,
			'isSecurityPassword' => true,
			'minLength' => 4,
			'minAlpha' => 0,
			'minNumbers' => 0,
			'minSpecialChars' => 0,
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
			'confirm' => __('A confirmação não bate com a senha', true),
			'minAlpha' => __(sprintf('A senha deve ter pelo menos %d caracteres alfabeticos', $this->settings['minAlpha']), true),
			'minNumbers' => __(sprintf('A senha deve ter pelo menos %d caracteres numericos', $this->settings['minNumbers']), true),
			'minSpecialChars' => __(sprintf('A senha deve ter pelo menos %d caracteres especiais', $this->settings['minSpecialChars']), true)
		);
	}
	
	/**
	 * 
	 * @see libs/model/ModelBehavior::beforeValidate()
	 */
	public function beforeValidate(&$model)
	{
		parent::beforeValidate($model);

		if(!empty($this->settings['preConditions']) && is_array($this->settings['preConditions']))
		{
			if(!$this->evalConditions($this->settings['preConditions']))
			{
				return true;
			}
		}

		if(isset($this->model->data[$this->model->name][$this->settings['fields']['password']]))
		{
			$pass = $this->model->data[$this->model->name][$this->settings['fields']['password']];
		}
		else
		{
			$pass = null;
		}

		
		// caso haja um campo referente a confirmação de senha
		if($this->settings['haveConfirm'] && isset($this->model->data[$this->model->name][$this->settings['fields']['confirm']]))
		{
			// recupera o valor vindo do formulário
			$confirm = $this->model->data[$this->model->name][$this->settings['fields']['confirm']];
		}
		// caso contrário
		else
		{
			// seta um valor padrão
			$confirm = null;
		}
		
		// executa validação da senha
		$success = $this->isValidPassword($pass, $confirm);
		
		// caso haja alguma falha
		if($success !== true)
		{
			// adiciona os erros encontrados no atributo validationErrors do modelo atual
			$this->model->validationErrors = array_merge($this->model->validationErrors, $success);
			
			// caso a configuração force a limpeza dos valores (senha e confirmação)
			if($this->settings['unsetInFailure'])
			{
				if(isset($this->model->data[$this->model->name][$this->settings['fields']['password']]))
					unset($this->model->data[$this->model->name][$this->settings['fields']['password']]);
				
				if(isset($this->model->data[$this->model->name][$this->settings['fields']['confirm']]))
					unset($this->model->data[$this->model->name][$this->settings['fields']['confirm']]);
			}
			
		}
		
		return true;
	}
	
	/**
	 * Método responsável pela execução da validação, baseada nas configurações do behavior
	 * 
	 * @param string $pass
	 * @param string $confirm
	 */
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
				$errors[$this->settings['fields']['confirm']] = $this->settings['errors']['required'];
			}
			else 
			{
				$policyErrors = $this->validatePasswordPolicy($confirm, $this->settings['fields']['confirm']);
				
				if($policyErrors !== true)
				{
					$errors = array_merge($errors, $policyErrors);
				}
				else
				{
					$hash = Security::hash($confirm, null, true);

					// valida se o hash da senha é o mesmo da confirmação
					if($pass != $hash)
					{
						$errors[$this->settings['fields']['confirm']] = $this->settings['errors']['confirm'];
					}
				}
			}
		}
		// caso o campo de senha não venha em hash, é possível usar o próprio campo
		else if(!$this->settings['isSecurityPassword'])
		{
			$policyErrors = $this->validatePasswordPolicy($pass, $this->settings['fields']['password']);

			if($policyErrors !== true)
			{
				$errors = array_merge($errors, $policyErrors);
			}
		}
		
		// caso não tenha sido encontrado nenhum erro
		if(empty($errors))
		{
			// retorna true
			return true;
		}
		// caso contrário
		else
		{
			// retorna o array com os erros
			return $errors;
		}
	}

	/**
	 * Avalia se uma determinada condição (passada no mesmo formato
	 * do método find() ) é válida, comparando-a com os dados vindos
	 * (ou seja, com os dados disponíveis em Model::data )
	 *
	 * @param array $conditions
	 */
	protected function evalConditions( $conditions )
	{
		foreach($conditions as $input => $value)
		{
			$field = explode('.', $input);

			if(!isset($this->model->data[$field[0]][$field[1]]) || $this->model->data[$field[0]][$field[1]] != $value)
			{
				return false;
			}
		}

		return true;
	}

	
	/**
	 * Metodo responsavel pela validacao da politica de senha
	 *
	 * @param string $password senha a ser checada
	 *
	 * @return true caso a senha case com todos os requisitos da politica,
	 *         array com os erros, caso contrario.	 
	 *
	 */
	private function validatePasswordPolicy($password, $field)
	{
		$errors = array();
		
		// valida o tamanho da senha
		if(mb_strlen($password) < $this->settings['minLength'])
		{
			$errors[$field] = $this->settings['errors']['minLength'];
		}
		
		// valida o minimo de letras na senha
		if($this->settings['minAlpha'] > 0)
		{
			$onlyLetters = mb_ereg_replace('[^a-z]', '', $password, 'i');
			
			if(mb_strlen($onlyLetters) < $this->settings['minAlpha'])
			{
				$errors[$field] = $this->settings['errors']['minAlpha'];
			}
		}
		
		// valida o nimimo de numeros na senha
		if($this->settings['minNumbers'] > 0)
		{
			$onlyNumbers = mb_ereg_replace('[^0-9]', '', $password, 'i');
			
			if(mb_strlen($onlyNumbers) < $this->settings['minNumbers'])
			{
				$errors[$field] = $this->settings['errors']['minNumbers'];
			}
		}
		
		// valida o minimo de caracteres especiais na senha
		if($this->settings['minSpecialChars'] > 0)
		{
			$onlySpecialChars = mb_ereg_replace('[a-z0-9]', '', $password, 'i');
			
			if(mb_strlen($onlySpecialChars) < $this->settings['minSpecialChars'])
			{
				$errors[$field] = $this->settings['errors']['minSpecialChars'];
			}
		}

		// valida se a senha está vazia (validação feita por último para sobreescrever outras msgs de erro)
		if(empty($password))
		{
			$errors[$field] = $this->settings['errors']['required'];
		}
		
		if(empty($errors))
		{
			return true;
		}
		
		return $errors;
	}
}
