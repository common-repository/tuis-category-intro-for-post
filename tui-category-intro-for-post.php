<?php

  /*
  Plugin Name: Tui's Category Intro For Posts
  Plugin URI:  http://www.stephenbaugh.com/blog/wordpress-plugins/category-intro-post/
  Version:     1.02
  Description: Provides a category intro for posts, based on the category name and description. Fully configurable and easy to use with no theme editing required. For further information and installation instructions visit this plugins <a href="http://www.stephenbaugh.com/blog/wordpress-plugins/category-intro-post/">home page.</a>
  Author:      Stephen Baugh
  Author URI:  http://www.stephenbaugh.com/
  */

  /*
    Copyright 2009-2010 Stephen Baugh  (email : stephen@stephenbaugh.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

	http://www.gnu.org/licenses/quick-guide-gplv3.html

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
  */

//Check to see if user has sufficient privileges
function tui_cifp_is_authorized() 
{
        global $user_level;
        if (function_exists("current_user_can")) {
                return current_user_can('activate_plugins');
        } else {
                return $user_level > 5;
        }
}


// Hook for adding admin menus
register_activation_hook(__FILE__, 'tui_cifp_activate');
add_action('admin_menu', 'tui_cifp_add_pages');


add_filter('the_content', 'tui_cifp_content_filter');



if ( strpos($_SERVER['REQUEST_URI'], 'wp-admin') === false ) {
		
	
	add_action('template_redirect','tui_cifp_ob_start'); // 

}

	
function tui_cifp_ob_start()
{

	global $tui_cifp_message, $tui_cifp_div, $wp_query;
	
	if (is_single()) 
	{
	
		$tui_cifp_div = get_option('tui_cifp_div');
		
		if ($tui_cifp_div !== '') 
		{

		$thePostID = $wp_query->post->ID;
		$categories = get_the_category($thePostID); 
		$categoryID = $categories[0]->cat_ID;
		
		$tui_cifp_message = get_option('tui_cifp_message');
		
		$categoryTitle = $categories[0]->cat_name;
		$categoryDescription = $categories[0]->category_description;
		
		$tui_cifp_message = str_replace("[categoryID]", $categoryID, $tui_cifp_message);
		$tui_cifp_message = str_replace("[categoryTitle]", $categoryTitle, $tui_cifp_message);
		$tui_cifp_message = str_replace("[categoryDescription]", $categoryDescription, $tui_cifp_message);
		$tui_cifp_message = $tui_cifp_div.$tui_cifp_message;
		
		$tui_cifp_message = tui_cifp_evaluate_html($tui_cifp_message);
		
		}
		
		ob_start('tui_cifp_templatefilter');
			
	}
	
}
		


function tui_cifp_add_pages() {

    // Add a new submenu under Options:
    add_options_page("Category Intro (Post)", "Category Intro (Post)", 8, 'tui_categoryintroforpost', 'tui_cifp_options_page');

}



function tui_cifp_options_page() {

       $message = '';
        
        global $ol_flash, $_POST;
        
        if (tui_cifp_is_authorized()) {
	
			// update options

				
          
      		if ($_POST['tui_cifp_update_settings']) {
			
			    update_option('tui_cifp_message', stripslashes($_POST['tui_cifp_message']));
                update_option('tui_cifp_div', stripslashes($_POST['tui_cifp_div']));
                update_option('tui_cifp_hasphp', $_POST['tui_cifp_hasphp']);
                update_option('tui_cifp_addcss', $_POST['tui_cifp_addcss']);

                $ol_flash = "Your settings have been saved.";
				$message = "Your settings have been saved";
		
                $ol_flash = $message;
            
      	    } else if ($_POST['tui_cifp_reset_settings']) {

				$message = "Your settings have been reset";
				tui_cifp_initialize_and_get_settings();
				
			}

        } else {
          
          $ol_flash = "You don't have sufficient privilges.";
        
        }
        
        
		echo '<div class="wrap">';
		echo '<table width="100%" border="0" cellpadding="0">';
		echo '<tr>';
		echo '<td align="left" valign="top" width="70%"><h2>Set up for Tui&#8217;s Category Intro for Post Plugin</h2></td>';
		echo '<td align="left" valign="top" width="5%">&nbsp;</td>';
		echo '<td align="left" valign="top" width="25%">&nbsp;</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td align="left" valign="top">';

        if (tui_cifp_is_authorized()) {
        
           
            echo '<p>This plugin gives you the ability to add a category introduction to each post. I have multiple topics I blog about ';
            echo 'and therefore want to put the post in prespective by sharing the category I added it to. No editing of your themes is ';
            echo 'required to use the plugin and a default layout is setup on install. Play with the html layout until you get exactly what you want. ';
            echo '3 tags are provided to give you access to the category data <b>[categoryID], [categoryTitle]</b> and <b>[categoryDescription]</b> also optionally you can use php if you are an advanced user. ';
            echo 'Tags are evaluated first, then php before the results are returned. ';
            echo 'For more detailed information and installation information please visit this plugins <A HREF="http://www.stephenbaugh.com/blog/wordpress-plugins/category-intro-post/" target="_blank">home page</A></p>';
         
        	echo '<h3>Category Intro for Post Settings</h3>';
		    echo '<form action="" method="post" name="setup page  header">';
            echo '<input type="hidden" name="tui_cifp_update_settings" value="true" />';
            echo '<input type="hidden" name="redirect" value="true" />';
			echo '<p>&nbsp;</p>';
            echo '<H4>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HTML to have added to your <em>Post</em> to introduce your <em>Category</em></H4>';
			echo '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea rows="10" cols="80" name="tui_cifp_message">'.get_option('tui_cifp_message').'</textarea><br />';
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Your layout can contain simple php, but please be careful. Php needs to be contained in proper php tags, and properly terminated.</p>';
			echo '<p>&nbsp;</p>';
			echo '<H4>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HTML Tag to use for positioning (e.g. &#60;div id=&#34;content-wrapper&#34;&#62;)</H4>';
    		echo '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea rows="2" cols="80" name="tui_cifp_div">'.get_option('tui_cifp_div').'</textarea><br />';
    		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To postition automatically below the Post introduction just delete this tag.</p>';
            echo '<p>&nbsp;</p>';
	   		echo '<H4>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CSS style to add for extra control</H4>';
    		echo '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea rows="10" cols="80" name="tui_cifp_addcss">'.get_option('tui_cifp_addcss').'</textarea></p>';
			echo '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="submit" value="Save Settings" /></p>';
			echo '</form>';

        	echo '<form action="" method="post">';
			echo '<input type="hidden" name="tui_cifp_reset_settings" value="true" />';
			echo '<p>&nbsp;</p>';
			echo '<h3>Reset plugin</h3>';
			echo '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;If you mess up your HTML click here to restore.</p>';
			echo '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="submit" value="Reset Settings" /></p>';
			
			if (function_exists('wp_nonce_field')) {
				wp_nonce_field('tui_cifp_reset_options');
			}
			echo '</form>';

			echo '<p>&nbsp;</p>';
			echo '</div>';

        } else {
          
          	$ol_flash = "You don't have sufficient privilges.";

        }
        
        
	echo '</td>';
	echo '<td align="left" valign="top" width="5%">&nbsp;</td>';
	echo '<td align="left" valign="top">';
    echo '<p><strong>Additional Puggin Information</strong></p>';
    echo '<p></p>';

  	echo 'Plugin Home Page : <A HREF="http://www.stephenbaugh.com/blog/wordpress-plugins/category-intro-post/" target="_blank">Click Here</A><br />';
  	echo 'Support Forums : <A HREF="http://www.stephenbaugh.com/blog/forums/" target="_blank">Click Here</A><br />';
  	echo 'To Donate : <A HREF="http://www.stephenbaugh.com/donation.php" target="_blank">Click Here (thanks)</A><br />';
  	echo 'Plugin Author : <A HREF="http://www.stephenbaugh.com" target="_blank">Stephen Baugh</A><br />';
  	echo '<p>&nbsp;</p>';
  	echo 'Rate this plugin : <A HREF="http://wordpress.org/extend/plugins/tuis-category-intro-for-post/" target="_blank">Click Here (thanks)</A><br />';
  	echo '<p>&nbsp;</p>';
  	echo "<a href='http://secure.hostgator.com/cgi-bin/affiliates/clickthru.cgi?id=tui701' target='_blank'><img src='http://secure.hostgator.com/~affiliat/banners/hostgator2-220x240.gif' /></a>";
	echo '<p>&nbsp;</p>';
  	echo '<a href="https://www.e-junkie.com/ecom/gb.php?cl=10214&c=ib&aff=46868" title="Revolution Two WordPress Themes" target="_blank"><img border="0" src="http://www.stephenbaugh.com/revolution2banner.png" alt="Revolution Two WordPress Themes" width="234" height="60" /></a>';
  	echo '</tr>';
    echo '</table>';
        
}




function tui_cifp_initialize_and_get_settings()
{

	delete_option('tui_cifp_message');
    delete_option('tui_cifp_div');
    delete_option('tui_cifp_hasphp');
    delete_option('tui_cifp_addcss');

	tui_cifp_defaultdata();
	
}



function tui_cifp_activate()
{
	
	tui_cifp_defaultdata();
	
}






function tui_cifp_defaultdata()
{

	$tui_cifp_message = '<div class="categoryintro">'.chr(13);
	$tui_cifp_message = $tui_cifp_message.'<table width="100%" border="0" cellpadding="0">'.chr(13);
	$tui_cifp_message = $tui_cifp_message.'<tr>'.chr(13);
	$tui_cifp_message = $tui_cifp_message.'<td width="100" height="100">'.chr(13);
	
	if (function_exists('tui_findPostThumbIMG')) {
	$tui_cifp_message = $tui_cifp_message.'<?php tui_findPostThumbIMG([categoryID],100,100,"categoryintro-thumbnail","","",""); ?>'.chr(13);
	}

	$tui_cifp_message = $tui_cifp_message.'</td>'.chr(13);
	$tui_cifp_message = $tui_cifp_message.'<td width="20"></td>'.chr(13);
	$tui_cifp_message = $tui_cifp_message.'<td>'.chr(13);
	$tui_cifp_message = $tui_cifp_message.'<H3>A post from my "[categoryTitle]" category</H3>'.chr(13);
	$tui_cifp_message = $tui_cifp_message.'[categoryDescription]'.chr(13);
	$tui_cifp_message = $tui_cifp_message.'</td>'.chr(13);
	$tui_cifp_message = $tui_cifp_message.'</tr>'.chr(13);
	$tui_cifp_message = $tui_cifp_message.'</table>'.chr(13);
	$tui_cifp_message = $tui_cifp_message.'</div>'.chr(13);
	
	$newcss = '.categoryintro {'.chr(13);
	$newcss = $newcss.'width: 585px;'.chr(13);
	$newcss = $newcss.'height: 110px;'.chr(13);
	$newcss = $newcss.'overflow: hidden;'.chr(13);
	$newcss = $newcss.'padding: 15px;'.chr(13);
	$newcss = $newcss.'margin-right: 15px;'.chr(13);
	$newcss = $newcss.'margin-bottom: 10px;'.chr(13);
	$newcss = $newcss.'margin-left: 19px;'.chr(13);
	$newcss = $newcss.'float: left;'.chr(13);
	$newcss = $newcss.'background-color: #FFF;'.chr(13);
	$newcss = $newcss.'border: 1px solid #D6D6D6;'.chr(13);
	$newcss = $newcss.'}'.chr(13);
	$newcss = $newcss.'.categoryintro-thumbnail {'.chr(13);
	$newcss = $newcss.'border: 0px;'.chr(13);
	$newcss = $newcss.'margin: 0px 0px 0px 0px;'.chr(13);
	$newcss = $newcss.'}';
	
	$tui_cifp_div = '<div id="content-wrapper">';
	
  	add_option('tui_cifp_message', $tui_cifp_message);
    add_option('tui_cifp_div', $tui_cifp_div);
    add_option('tui_cifp_hasphp', $tui_cifp_hasphp);
	add_option('tui_cifp_addcss', $newcss);

}	





function tui_cifp_content_filter($content = '') {
	
	if ((is_single()) && (get_option('tui_cifp_div') == '')) 
	{
		
		$tui_cifp_message = get_option('tui_cifp_message');
		$tui_cifp_div = get_option('tui_cifp_div');
		$tui_cifp_hasphp = get_option('tui_cifp_hasphp');

		$categories = get_the_category(); 
		$categoryID = $categories[0]->cat_ID;
		$categoryTitle = $categories[0]->cat_name;
		$categoryDescription = $categories[0]->category_description;
		
		$tui_cifp_message = str_replace("[categoryID]", $categoryID, $tui_cifp_message);
		$tui_cifp_message = str_replace("[categoryTitle]", $categoryTitle, $tui_cifp_message);
		$tui_cifp_message = str_replace("[categoryDescription]", $categoryDescription, $tui_cifp_message);
			
		$tui_cifp_message = tui_cifp_evaluate_html($tui_cifp_message);
	
		return $tui_cifp_message . $content;
			
	} else {
		
		return $content;
		
	}
	
}




function tui_cifp_templatefilter($content) {

	global $tui_cifp_message;
	global $tui_cifp_div;
	
	if (is_single()) 
	{
	
		if ($tui_cifp_div !== '') 
		{
		
		$content = str_replace($tui_cifp_div, $tui_cifp_message, $content);	
		
		}
		
		
		if (get_option('tui_cifp_addcss') !== '')
		{
			$styletoadd = '<style type="text/css">'.get_option('tui_cifp_addcss').'</style>'.chr(13).'</head>';
			$content = str_ireplace('</head>', $styletoadd, $content);
	
		}
	
	}
	
	return $content;
	ob_end_flush();

}



function tui_cifp_evaluate_html($string) {

      return preg_replace_callback("/(<\?php|<\?|< \?php)(.*?)\?>/si",'EvalBuffer', $string);

} 



// Runs (evals()) a '$string' of PHP code.
function EvalBuffer($string) {

      ob_start();
      eval("$string[2];");
      $ret = ob_get_contents();
      ob_end_clean();
      return $ret;

}



?>