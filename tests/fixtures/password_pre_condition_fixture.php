<?php
class PasswordPreConditionFixture extends CakeTestFixture {
	var $name = 'PasswordPreCondition';

	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'password' => array('type'=>'string', 'null' => true, 'default' => NULL),
		'type' => array('type'=>'string', 'null' => false, 'default' => 'regular'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);

	var $records = array(
		array(
			'id'  => 1000,
			'name'  => 'joao',
			'password' => null,
			'type' => 'regular'
		),
		array(
			'id'  => 1001,
			'name'  => 'tonho',
			'password'  => 'sem_hash',
			'type' => 'admin'
		)
	);
}
