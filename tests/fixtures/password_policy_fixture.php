<?php
class PasswordPolicyFixture extends CakeTestFixture {
	var $name = 'PasswordPolicy';

	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'password' => array('type'=>'string', 'null' => false),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);

	var $records = array(
		array(
			'id'  => 1,
			'name'  => 'joao',
			'password'  => '40bd001563085fc35165329ea1ff5c5ecbdbbeef'
		),
		array(
			'id'  => 2,
			'name'  => 'tonho',
			'password'  => 'sem_hash'
		)
	);
}
