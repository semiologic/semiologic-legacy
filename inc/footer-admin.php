<?php
class sem_footer_admin
{
	#
	# footer_widget_control()
	#

	function footer_widget_control()
	{
		global $sem_options;
		global $sem_captions;

		if ( $_POST['update_sem_footer']['nav_menu'] )
		{
			$new_options = $sem_options;
			$new_captions = $sem_captions;

			$new_options['show_copyright'] = isset($_POST['sem_footer']['show_copyright']);
			$new_options['float_footer'] = isset($_POST['sem_footer']['float_footer']);
			
			if ( current_user_can('unfiltered_html') )
			{
				$new_captions['copyright'] = stripslashes($_POST['sem_footer']['label_copyright']);
			}
			else
			{
				$new_captions['copyright'] = strip_tags(stripslashes($_POST['sem_footer']['label_copyright']));
			}

			if ( $new_options != $sem_options )
			{
				$sem_options = $new_options;

				update_option('sem5_options', $sem_options);
			}
			if ( $new_captions != $sem_captions )
			{
				$sem_captions = $new_captions;

				update_option('sem5_captions', $sem_captions);
			}
		}

		echo '<input type="hidden" name="update_sem_footer[nav_menu]" value="1" />';

		echo '<h3>'
			. __('Config')
			. '</h3>';

		echo '<div style="margin-bottom: .2em;">'
			. '<label>'
			. '<input type="checkbox"'
				. ' name="sem_footer[show_copyright]"'
				. ( $sem_options['show_copyright']
					? ' checked="checked"'
					: ''
					)
				. ' />'
			. ' '
			. __('Show Copyright Notice')
			. '</label>'
			. '</div>';

		echo '<div style="margin-bottom: .2em;">'
			. '<label>'
			. '<input type="checkbox"'
				. ' name="sem_footer[float_footer]"'
				. ( $sem_options['float_footer']
					? ' checked="checked"'
					: ''
					)
				. ' />'
			. ' '
			. __('Show copyright and menu as a single line')
			. '</label>'
			. '</div>';
			
		echo '<h3>'
			. __('Captions')
			. '</h3>';

		echo '<div style="margin-bottom: .2em;">'
			. '<label>'
			. __('Copyright Notice, e.g. Copyright %year%')
			. '<br />'
			. '<input type="text" style="width: 95%"'
				. ' name="sem_footer[label_copyright]"'
				. ' value="' . attribute_escape($sem_captions['copyright']) . '"'
				. ' />'
			. '</label>'
			. '</div>';

		sem_nav_menus_admin::widget_control('footer');
	} # footer_widget_control()

	#
	# credits_widget_control()
	#

	function credits_widget_control()
	{
		global $sem_options;
		global $sem_captions;

		if ( $_POST['update_sem_footer']['nav_menu'] )
		{
			$new_options = $sem_options;
			$new_captions = $sem_captions;

			$new_options['show_credits'] = isset($_POST['sem_footer']['show_credits']);			

			if ( $new_options != $sem_options )
			{
				$sem_options = $new_options;

				update_option('sem5_options', $sem_options);
			}
			if ( $new_captions != $sem_captions )
			{
				$sem_captions = $new_captions;

				update_option('sem5_captions', $sem_captions);
			}
		}

		echo '<input type="hidden" name="update_sem_footer[credits]" value="1" />';

		echo '<h3>'
			. __('Config')
			. '</h3>';

		echo '<div style="margin-bottom: .2em;">'
			. '<label>'
			. '<input type="checkbox"'
				. ' name="sem_footer[show_credits]"'
				. ( $sem_options['show_credits']
					? ' checked="checked"'
					: ''
					)
				. ' />'
			. ' '
			. __('Show Theme Credits')
			. '</label>'
			. '</div>';			

/*		echo '<h3>'
			. __('Captions')
			. '</h3>';

		echo '<div style="margin-bottom: .2em;">'
			. '<label>'
			. __('Copyright Notice, e.g. Copyright %year%')
			. '<br />'
			. '<input type="text" style="width: 95%"'
				. ' name="sem_footer[label_copyright]"'
				. ' value="' . attribute_escape($sem_captions['copyright']) . '"'
				. ' />'
			. '</label>'
			. '</div>';
*/
	} # credits_widget_control()	
} # sem_footer_admin
?>