<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Assets Config
| -------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Script Directory
|--------------------------------------------------------------------------
|
| Path to the script directory.  Relative to the CI front controller.
|
*/

$config['script_dir'] = 'assets/scripts/';


/*
|--------------------------------------------------------------------------
| Style Directory
|--------------------------------------------------------------------------
|
| Path to the style directory.  Relative to the CI front controller
|
*/

$config['style_dir'] = 'assets/styles/';

/*
|--------------------------------------------------------------------------
| Cache Directory
|--------------------------------------------------------------------------
|
| Path to the cache directory. Must be writable. Relative to the CI 
| front controller.
|
*/

$config['cache_dir'] = 'assets/cache/';


// --------------------------------------------------------------------

/*
* The following config values are not required.  See README.markdown
* for more information.
*/



/*
|--------------------------------------------------------------------------
| Base URI
|--------------------------------------------------------------------------
|
|  Base uri of the site, like http://www.example.com/ Defaults to the CI 
|  config value for base_url.
|
*/

//$config['base_uri'] = 'http://www.example.com/';


/*
|--------------------------------------------------------------------------
| Development Flag
|--------------------------------------------------------------------------
|
|  Flags whether your in a development environment or not. Defaults to FALSE.
|
*/

$config['dev'] = FALSE;


/*
|--------------------------------------------------------------------------
| Combine
|--------------------------------------------------------------------------
|
| Flags whether files should be combined. Defaults to TRUE.
|
*/

$config['combine'] = TRUE;


/*
|--------------------------------------------------------------------------
| Minify Javascript
|--------------------------------------------------------------------------
|
| Global flag for whether JS should be minified. Defaults to TRUE.
|
*/

$config['minify_js'] = TRUE;


/*
|--------------------------------------------------------------------------
| Minify CSS
|--------------------------------------------------------------------------
|
| Global flag for whether CSS should be minified. Defaults to TRUE.
|
*/

$config['minify_css'] = TRUE;

/*
|--------------------------------------------------------------------------
| Force cURL
|--------------------------------------------------------------------------
|
| Global flag for whether to force the use of cURL instead of file_get_contents()
| Defaults to FALSE.
|
*/

$config['force_curl'] = FALSE;


/*
|--------------------------------------------------------------------------
| Predifined Asset Groups
|--------------------------------------------------------------------------
|
| Any groups defined here will automatically be included.  Of course, they
| won't be displayed unless you explicity display them ( like this: $this->carabiner->display('jquery') )
| See docs for more.
| 
| Currently created groups:
|	> jQuery (latest in 1.xx version)
|	> jQuery UI (latest in 1.xx version)
|	> Chrome Frame (latest in 1.xx version)
|	> SWFObject (latest in 2.xx version)
|
*/

// jQuery (latest, as of 1.xx)
$config['groups']['jquery'] = array(
	'js' => array(
		array('http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js', 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', TRUE, FALSE)
	)
);

// jQuery UI (latest, as of 1.xx)
$config['groups']['jqueryui'] = array(
	'js' => array(
		array('http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js', 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', TRUE, FALSE),
		array('http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.js', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js', TRUE, FALSE)
	)
);

// Chrome Frame (latest, as of 1.xx)
$config['groups']['chrome-frame'] = array(
	'js' => array(
		array('http://ajax.googleapis.com/ajax/libs/ext-core/3.0.0/ext-core-debug.js', 'http://ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js', TRUE, FALSE)
	)
);

// SWFObject (latest, as of 2.xx)
$config['groups']['swfobject'] = array(
    'js' => array(
        array('http://ajax.googleapis.com/ajax/libs/swfobject/2/swfobject_src.js', 'http://ajax.googleapis.com/ajax/libs/swfobject/2/swfobject.js', TRUE, FALSE)
    )
);

/* End of file assets.php */
/* Location: ./config/assets.php */
