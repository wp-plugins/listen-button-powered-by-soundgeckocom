<?php
/*
Plugin Name: Listen button powered by SoundGecko.com
Plugin URI: http://soundgecko.com/developers#wordpress-listen-button-plugin
Description: The SoundGecko plugin for WordPress adds a listen button to each post on your WordPress site that when clicked allows readers to hear your post read out.
Version: 1.0.0
Author: SoundGecko
Author URI: http://www.soundgecko.com
License: GPL2
*/
/*  Copyright 2013 SoundGecko (email: support@soundgecko.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('admin_menu', 'soundgecko_admin_add_page');
add_action('soundgecko_listen_button', 'soundgecko_listen_button_template_tag');
add_filter('admin_init', 'soundgecko_settings_init');
add_filter('the_excerpt', 'soundgecko_content');
add_filter('the_content', 'soundgecko_content');

function soundgecko_defaults($key = null) {
    $defaults = array(
        'soundgecko_setting_placement'         => 'bottom',
        'soundgecko_setting_size'              => 'regular',
        'soundgecko_setting_text'              => 'Listen',
        'soundgecko_setting_show_on_pages'     => 'hide',
        'soundgecko_setting_only_template_tag' => 'no'
    );

    return ($key === null) ? $defaults : $defaults[$key];
}

function soundgecko_button() {
    $added_css_classes = array();

    $size = get_option('soundgecko_setting_size', soundgecko_defaults('soundgecko_setting_size'));

    if ($size == 'large') {
        array_push($added_css_classes, 'soundgecko-large');
    }

    $added_css = count($added_css_classes) > 0 
        ? ' ' . implode(' ', $added_css_classes)
        : '';

    $text = get_option('soundgecko_setting_text', soundgecko_defaults('soundgecko_setting_text'));

    $url = get_permalink();

    return <<<HTML
    <p><a href="http://soundgecko.com#listen" class="soundgecko-listen-button${added_css}" data-url="${url}">${text}</a></p>
    <script>
    !function(d,s,id){
    var js,fjs=d.getElementsByTagName(s)[0],
    g=(typeof window.SoundGecko != 'object')?window.SoundGecko:null;
    if (g != null){
      g.Widgets.bindings();
    } else {
      js=d.createElement(s);
      js.id=id;
      js.src="//az412101.vo.msecnd.net/platform/widgets.js";
      fjs.parentNode.insertBefore(js,fjs);
    }}(document,"script","soundgecko-widgets");
    </script>
HTML;
}

function soundgecko_content($content) {
    if (get_option('soundgecko_setting_only_template_tag') === 'yes') {
        return $content;
    }

    if (is_page()) {
        $hide_from_pages = get_option(
            'soundgecko_setting_show_on_pages', 
            soundgecko_defaults('soundgecko_setting_show_on_pages'));

        if ($hide_from_pages  == 'hide') {
            return $content;
        }
    }

    $placement = get_option('soundgecko_setting_placement', soundgecko_defaults('soundgecko_setting_placement'));

    if ($placement == 'top') {
        return soundgecko_button() . $content;
    } else {
        return $content . soundgecko_button();
    }
}

function soundgecko_listen_button_template_tag() {
    echo soundgecko_button();
}

function soundgecko_admin_add_page() {
    add_options_page('SoundGecko', 'SoundGecko listen button', 'manage_options', 'soundgecko', 'soundgecko_options_page');
}

function soundgecko_options_page() {
?>
    <div class="wrap">
        <h2>Listen button powered by SoundGecko.com</h2>
        
        <p>
            <span>To use the template tag you will need to add the &#39;soundgecko_listen_button&#39; template tag inside the WordPress loop:</span><br /><br />
            <code>&lt;?php do_action('soundgecko_listen_button'); ?&gt;</code>
        </p>

        <form action="options.php" method="post">
            <?php settings_fields('soundgecko'); ?>
            <?php do_settings_sections('soundgecko'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

function soundgecko_settings_init() {
    add_settings_section(
        'soundgecko_setting_section',
        'Widget options',
        'soundgecko_setting_section_callback_function',
        'soundgecko');

    add_settings_field(
        'soundgecko_setting_show_on_pages',
        'Show button on pages as well',
        'soundgecko_setting_show_on_pages_callback_function',
        'soundgecko',
        'soundgecko_setting_section');

    register_setting('soundgecko', 'soundgecko_setting_show_on_pages');

    add_settings_field(
        'soundgecko_setting_placement',
        'Display at top or bottom',
        'soundgecko_setting_placement_callback_function',
        'soundgecko',
        'soundgecko_setting_section');

    register_setting('soundgecko', 'soundgecko_setting_placement');

    add_settings_field(
        'soundgecko_setting_size',
        'Listen button size',
        'soundgecko_setting_size_callback_function',
        'soundgecko',
        'soundgecko_setting_section');

    register_setting('soundgecko', 'soundgecko_setting_size');

    add_settings_field(
        'soundgecko_setting_text',
        'Listen button text',
        'soundgecko_setting_text_callback_function',
        'soundgecko',
        'soundgecko_setting_section');

    register_setting('soundgecko', 'soundgecko_setting_text');

    add_settings_field(
        'soundgecko_setting_only_template_tag',
        'Only use template tag',
        'soundgecko_setting_only_template_tag_callback_function',
        'soundgecko',
        'soundgecko_setting_section');

    register_setting('soundgecko', 'soundgecko_setting_only_template_tag');
}
 
function soundgecko_setting_section_callback_function() {
    echo '<p>Set SoundGecko listen button widget settings</p>';
}
 
function soundgecko_setting_placement_callback_function() {
    echo '<input name="soundgecko_setting_placement" type="radio" value="top" class="code" ' . 
        checked('top', get_option('soundgecko_setting_placement', soundgecko_defaults('soundgecko_setting_placement')), false) . 
        ' /> Top<br/>';

    echo '<input name="soundgecko_setting_placement" type="radio" value="bottom" class="code" ' . 
        checked('bottom', get_option('soundgecko_setting_placement', soundgecko_defaults('soundgecko_setting_placement')), false) . 
        ' /> Bottom';
}

function soundgecko_setting_size_callback_function() {
    echo '<input name="soundgecko_setting_size" type="radio" value="regular" class="code" ' . 
        checked('regular', get_option('soundgecko_setting_size', soundgecko_defaults('soundgecko_setting_size')), false) . 
        ' /> Small<br/>';

    echo '<input name="soundgecko_setting_size" type="radio" value="large" class="code" ' . 
        checked('large', get_option('soundgecko_setting_size', soundgecko_defaults('soundgecko_setting_size')), false) . 
        ' /> Large';
}

function soundgecko_setting_text_callback_function() {
    echo '<input name="soundgecko_setting_text" type="text" class="code" ' .  
        'value="' . get_option('soundgecko_setting_text', soundgecko_defaults('soundgecko_setting_text')) . '" />';
}

function soundgecko_setting_show_on_pages_callback_function() {
    echo '<input name="soundgecko_setting_show_on_pages" type="radio" value="hide" class="code" ' . 
        checked('hide', get_option('soundgecko_setting_show_on_pages', soundgecko_defaults('soundgecko_setting_show_on_pages')), false) . 
        ' /> Hide<br/>';

    echo '<input name="soundgecko_setting_show_on_pages" type="radio" value="show" class="code" ' . 
        checked('show', get_option('soundgecko_setting_show_on_pages', soundgecko_defaults('soundgecko_setting_show_on_pages')), false) . 
        ' /> Show';
}

function soundgecko_setting_only_template_tag_callback_function() {
    echo '<input name="soundgecko_setting_only_template_tag" type="checkbox" value="yes" class="code" ' . 
        checked('yes', get_option('soundgecko_setting_only_template_tag', soundgecko_defaults('soundgecko_setting_only_template_tag')), false) . 
        ' /> Disable automatic buttons and only use template tag';
}


