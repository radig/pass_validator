<?php
class WithoutConfirmationFixture extends CakeTestFixture {
	var $name = 'WithoutConfirmation';

	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'password' => array('type'=>'string', 'null' => false),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);

	var $records = array(
		array(
			'id'  => 1000,
			'name'  => 'joao',
			'password'  => '40bd001563085fc35165329ea1ff5c5ecbdbbeef'
		),
		array(
			'id'  => 10001,
			'name'  => 'tonho',
			'password'  => 'sem_hash'
		)
	);
}
