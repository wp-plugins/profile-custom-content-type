<?php

/* the current default settings
*/
$note = '';
$default_options = $this->default_options( 'settings' );
if(	empty($this->settings_options['picture'] ) )
	$this->settings_options['picture'] = $default_options['picture'];

if(	empty($this->settings_options['slug'] ) )
	$this->settings_options['slug'] = $default_options['slug'];

if(	empty($this->settings_options['permissions'] ) )
	$this->settings_options['permissions'] = $default_options['permissions'];
	
if( !empty($_POST) ):
	if(wp_verify_nonce($_POST['update_settings_nonce_field'], 'update_settings_nonce')):
		
		
	
		//Validate pic options
		$width = intval($_POST['picture_width']);
		$height = intval($_POST['picture_height']);
		if( $width >= 100 && $width <= 560 && $height >= 100 && $height <= 560):
			$picture_options = array ( 'width'=>$width, 'height'=>$height );
			$this->settings_options['picture'] = $picture_options;
		else:
			$note = '<div class="error settings-error"><p>Picture dimensions should be between 100x100 and 560x560</p></div>';
		endif;
		
		$slug = trim( $_POST['slug'] );
		if( !empty( $slug ) ):
			$this->settings_options['slug'] = sanitize_title( trim( $_POST['slug'] ) );
		else:
			$this->settings_options['slug'] = 'person';
		endif;
	
		// lets deal with permissions	
		$post_permissions = $_POST['options']['permissions'];
		
		foreach($this->settings_options['permissions'] as $user=>$permission_array):
			if($user != 'administrator'): // don't want people changing the permissions of the admin
				
				$role = get_role( $user );
				
				foreach($permission_array as $permission => $can):
					if( isset( $this->settings_options['permissions'][$user][$permission] ) ): // does the permission exist in the settings
						$this->settings_options['permissions'][$user][$permission] = (bool)$post_permissions[$user][$permission];
						// add the new capability
						if( (bool)$post_permissions[$user][$permission] ): 
							$role->add_cap( $permission );
						else:
  							$role->remove_cap(  $permission );
  							
  						endif;
					endif;
				endforeach;
				
			else: 
				// admin role you can't change the default permissions for the administater
				$role = get_role( 'administrator' );
				// the admin gets the best permissions
				foreach($this->settings_options['permissions']['administrator'] as $permission => $can):
						$role->add_cap( $permission );
				endforeach;
				
			endif;
			
		endforeach;
		
		
		//Store updated options
		update_option('Profile_CCT_settings', $this->settings_options);

		$note = '<div class="updated below-h2"><p> Settings saved.</p></div>';
		// lets flush the rules again
		$this->register_cpt_profile_cct();
		flush_rewrite_rules();
	else:	//if nonce failed
		$note = '<div class="error settings-error"><p>Verification error. Try again.</p></div>';
	endif;
endif;


?>
<h2>General Settings</h2>
<?php echo $note; ?>
<form method="post" action="">
	<h3>Picture Dimensions</h3>
	<?php wp_nonce_field( 'update_settings_nonce','update_settings_nonce_field' ); ?>	
	<table class="form-table">
	<tbody>
	<tr valign="top">
		<th scope="row"><label for="picture_width">Width</label></th>
		<td><input type="text" size="3" name="picture_width" id="picture_width" value="<?php echo esc_attr($this->settings_options['picture']['width']); ?>" /> pixels</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="picture_height">Height</label></th>
		<td><input type="text" size="3" name="picture_height" id="picture_height" value="<?php echo esc_attr($this->settings_options['picture']['height']); ?>" /> pixels</td>
	</tr>
	</tbody></table>
	
	
	<h3>Permalink</h3>
	<table class="form-table">
	<tbody>
	<tr valign="top">
		<th scope="row"><label for="slug">Slug</label></th>
		<td><input type="text"  name="slug" id="slug" value="<?php echo esc_attr($this->settings_options['slug']); ?>" /><br />
			By default it is set to 'person'
		</td>
	</tr>

	</tbody></table>
	
	
	<h3>Profile Permissions</h3>

	<table class="wp-list-table widefat fixed posts ">
		<thead>
			<tr>
				<th>Role</th>
				<th>Enable public profile</th>
				<th>Manage own profiles</th>
				<th>Manage all profiles</th>
				<th>Publish profile</th>
				<th>Read private profile</th>
				<th>Delete own profile</th>
				<th>Delete all profiles</th>
			</tr>
		</thead>		
		<tbody id="the-list">
				<?php 
				$count = 0;
				foreach($this->settings_options['permissions'] as $user=>$permission):
					$this->permissions_table($user, ($count%2)); $count++;
				endforeach; ?>
		</tbody>
	</table>
	<br />
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</form>	
