<?php
class UserFixture extends CakeTestFixture {
	var $name = 'User';

	var $fields = array(
		'id' => array('type'=>'integer', 'key' => 'primary'),
		'name' => array('type'=>'string', 'null' => false, 'default' => NULL),
		'password' => array('type'=>'string', 'null' => true),
		'type' => array('type' => 'string', 'length' => '30', 'default' => 'default'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
	);
}
