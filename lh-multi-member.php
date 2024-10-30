<?php
/*
Plugin Name: LH Multi Member
Plugin URI: https://lhero.org/portfolio/lh-multi-member/
Description: Gives all users in the network a role on the site
Author: Peter Shaw
Version: 1.02
Author URI: https://shawfactor.com/
*/

class LH_multi_member_plugin {


private function return_roles() {
    $editable_roles = get_editable_roles();
    foreach ($editable_roles as $role => $details) {
        $sub['role'] = esc_attr($role);
        $sub['name'] = translate_user_role($details['name']);
        $roles[] = $sub;
    }
    return $roles;
}


/**
 * Selects all the users with a given role and returns an array of the users' IDs. 
 * 
 * @param $role string The role to get the users for
 * @return $users array Array of user IDs for those users with the given role. 
 */


private function get_users_without_role() {
	global $wpdb;

 // get users without a role for current site
		$sql = "SELECT DISTINCT($wpdb->users.ID) FROM $wpdb->users
				 WHERE $wpdb->users.ID NOT IN (
					SELECT $wpdb->usermeta.user_id FROM $wpdb->usermeta
					WHERE $wpdb->usermeta.meta_key = '{$wpdb->prefix}capabilities' 
					)";


	$users = $wpdb->get_col( $sql );

	if( $role == 'none' ) { // if we got all users without a capability for the site, that includes super admins
		$super_users = get_super_admins();

		foreach( $users as $key => $user ){ //never modify caps for super admins
			if( is_super_admin( $user ) )
				unset( $users[$key] );
		}
	}

	return $users;
}


private function get_users_with_empty_role() {

global $wpdb;

global $wpdb;

$sql = "SELECT ".$wpdb->users.".ID FROM ".$wpdb->users." INNER JOIN ".$wpdb->usermeta." ON ".$wpdb->users.".ID = ".$wpdb->usermeta.".user_id WHERE ".$wpdb->usermeta.".meta_key = '".$wpdb->prefix."capabilities' 
AND";


$roles = $this->return_roles();

$i = 0;

foreach ($roles as $role ) {



if ($i == 0) {




} else {

$sql .= " and";


}


$sql .= " (".$wpdb->usermeta.".meta_value not like '%".$role['role']."%' )";


$i++;

}



$sql .= " ORDER BY display_name";

$users = $wpdb->get_col( $sql );

foreach( $users as $key => $user ){ 

if (is_super_admin( $user )){

unset( $users[$key] );

}


}


return $users;

}




/**
 * On activation, set a time, frequency and name of an action hook to be scheduled.
 */
public function on_activate($network_wide) {

 if ( is_multisite() && $network_wide ) { 

        global $wpdb;

        foreach ($wpdb->get_col("SELECT blog_id FROM $wpdb->blogs") as $blog_id) {
            switch_to_blog($blog_id);

$this->run_processes();
wp_clear_scheduled_hook( 'lh_multi_member_run' ); 
wp_schedule_event( time(), 'hourly', 'lh_multi_member_run' );

            restore_current_blog();
        } 

    } else {

$this->run_processes();
wp_clear_scheduled_hook( 'lh_multi_member_run' ); 
wp_schedule_event( time(), 'hourly', 'lh_multi_member_run' );

}





}

/**
 * On deactivate, remove the cron.
 */

public function deactivate_hook() {

wp_clear_scheduled_hook( 'lh_multi_member_run' ); 

}


public function add_unclaimed_role(){



if (!get_role('unclaimed')){
        add_role('unclaimed', 'Unclaimed User', array(
            'read' => false, // True allows that capability, False specifically removes it.
        ));

}



}

public function run_processes(){

if (is_main_site()){

$this->add_unclaimed_role();

$blog_users = $this->get_users_without_role();

$blog_id = get_current_blog_id();

foreach( $blog_users as $blog_user ) {

add_user_to_blog( $blog_id, $blog_user, 'unclaimed' );

}

} else {


$this->add_unclaimed_role();

$blog_users = $this->get_users_with_empty_role();

$blog_id = get_current_blog_id();

foreach( $blog_users as $blog_user ) {

add_user_to_blog( $blog_id, $blog_user, 'unclaimed' );

}


}

}


/**
 * Adds the default roles for all sites to a user, specified by $user_id
 */
function add_user_to_main_site( $user_id ){


if ( !is_user_member_of_blog( $user_id, BLOG_ID_CURRENT_SITE ) ){

add_user_to_blog( BLOG_ID_CURRENT_SITE, $user_id, 'unclaimed' );

}

}





function __construct() {

//cron the user copier
add_action('lh_multi_member_run', array($this,"run_processes"));

//Run when new user added
add_action('wpmu_activate_user', array($this,"add_user_to_main_site"), 10, 1 );
add_action('wpmu_new_user', array($this,"add_user_to_main_site"), 10, 1 );
add_action('user_register', array($this,"add_user_to_main_site"), 10, 1 );


}

}

$lh_multi_member_instance = new LH_multi_member_plugin();

register_activation_hook(__FILE__,array($lh_multi_member_instance,'on_activate') );
register_deactivation_hook( __FILE__, array($lh_multi_member_instance,'deactivate_hook') );

?>