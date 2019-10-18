<?php

	function lang($phrase){
		static $lang = array(
			'MESSAGE' => 'Welcome in Arabic',
			'ADMIN' => 'Admin in arabic'
		);
		return $lang[$phrase];
	}