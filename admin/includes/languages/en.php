<?php
/*abd:
*exp of phrase $home var could be ar or en for exp*/
	function lang($phrase){
		/*abd: static : no dynamic allocation doesn't change*/
		static $lang = array(
			/*abd: we divise strings depending on app parts*/

			//Navbar
			'HOME_ADMIN' 	   => 'Home',
			'CATEGORIES' 	   => 'Categories',
			'ITEMS'			     => 'Items',
			'MEMBERS'		     => 'Members',
			'COMMENTS'		   => 'Comments',
			'STATS'			 		 => 'Statistics',
			'LOGS'			     => 'Logs',
			'ADMIN_EDIT' 	   => 'Edit Profile',
			'ADMIN_SETTINGS' => 'Settings',
			'ADMIN_OUT' 	 	 => 'Log out'
		);
		return $lang[$phrase];
	}