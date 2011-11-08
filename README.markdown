Codeigniter Assets
==================

This package is based on the Carabiner library devloped by Tony Dewan. It will be cleaned, and 
updated to work with CI v2.x and refined to the tastes of the team at Topic.

-----

Carabiner manages javascript and CSS assets.  It will react differently depending on whether
it is in a production or development environment.  In a production environment, it will combine, 
minify, and cache assets. (As files are changed, new cache files will be generated.) In a 
development environment, it will simply include references to the original assets.

Carabiner requires the [JSMin][jsmin] and [CSSMin][cssmin] libraries (included).
You don't need to load them unless you'll be using them elsewhise.  Carabiner will load them
automatically as needed.

[jsmin]:http://codeigniter.com/forums/viewthread/103269/
[cssmin]:http://codeigniter.com/forums/viewthread/103269/

Notes: Carabiner does not implement GZIP encoding, because I think that the web server should  
handle that.  If you need GZIP in an Asset Library, [AssetLibPro][assetlib]
does it.  I've also chosen not to implement any kind of javascript obfuscation (like packer), 
because of the client-side decompression overhead. More about this idea from [John Resig][jresig].
However, that's not to say you can't do it.  You can easily provide a production version of a script
that is packed.  However, note that combining a packed script with minified scripts could cause
problems.  In that case, you can flag it to be not combined.

[assetlib]:http://code.google.com/p/assetlib-pro/
[jresig]:http://ejohn.org/blog/library-loading-speed/

Carabiner is inspired by Minify {@link http://code.google.com/p/minify/ by Steve Clay}, PHP 
Combine {@link http://rakaz.nl/extra/code/combine/ by Niels Leenheer} and AssetLibPro 
{@link http://code.google.com/p/assetlib-pro/ by Vincent Esche}, among other things.



## USAGE

Load the library as normal:

	$this->load->library('assets');
	
Configuration can happen in either a config file (included), or by passing an array of values 
to the config() method. Config options passed to the config() method will override options in 
the	config file.

See the included config file for more info.

To configure Carabiner using the config() method, do this:

	$assets = array(
		'script_dir' => 'assets/scripts/', 
		'style_dir'  => 'assets/styles/',
		'cache_dir'  => 'assets/cache/',
		'base_uri'	 => base_url(),
		'combine'	 => TRUE,
		'dev' 		 => FALSE,
		'minify_js'  => TRUE,
		'minify_css' => TRUE
	);

	$this->assets->config($assets);


### Required configuration

    'script_dir'

STRING Path to the script directory.  Relative to the CI front controller (index.php)

    style_dir
STRING Path to the style directory.  Relative to the CI front controller (index.php)

    cache_dir
STRING Path to the cache directory.  Must be writable. Relative to the CI front controller (index.php)


### Optional configuration

    base_uri
STRING Base uri of the site, like http://www.example.com/ Defaults to the CI config value for 
base\_url.

    dev
BOOL Flags whether your in a development environment or not.  See above for what this means.  
Defaults to FALSE.

    combine
BOOLEAN Flags whether to combine files.  Defaults to TRUE.

    minify_js
BOOLEAN Flags whether to minify javascript. Defaults to TRUE.

    minify_css
BOOLEAN Flags whether to minify CSS. Defaults to TRUE.

    force_curl
BOOLEAN Flags whether cURL should always be used for URL file references. Defaults to FALSE.

## Adding Assets

Add assets like so:

	// add a js file
	$this->assets->js('scripts.js');

	// add a css file
	$this->assets->css('reset.css');

	// add a css file with a mediatype
	$this->assets->css('admin/print.css','print');


To set a (prebuilt) production version of an asset:

	// JS: pass a second string to the method with a path to the production version
	$this->assets->js('wymeditor/wymeditor.js', 'wymeditor/wymeditor.pack.js' );

	// add a css file with prebuilt production version
	$this->assets->css('framework/type.css', 'screen', 'framework/type.pack.css');


And to prevent an individual asset file from being combined:

	// JS: pass a boolean FALSE as the third attribute of the method
	$this->assets->js('wymeditor/wymeditor.js', 'wymeditor.pack.js', FALSE );

	// CSS: pass a boolean FALSE as the fourth attribute of the method
	$this->assets->css('framework/type.css', 'screen', 'framework/type.pack.css', FALSE);


You can also pass arrays (and arrays of arrays) to these methods. Like so:	

	// a single array (this is redundant, but supported anyway)
	$this->assets->css( array('mobile.css', 'handheld', 'mobile.prod.css') );

	// an array of arrays
	$js_assets = array(
		array('dev/jquery.js', 'prod/jquery.js'),
		array('dev/jquery.ext.js', 'prod/jquery.ext.js'),
	)

    $this->assets->js( $js_assets );


Carabiner is smart enough to recognize URLs and treat them differently:

	$this->assets->js('http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.js');


You can also define groups of assets

	// Define JS
	$js = array(
		array('prototype.js'),
		array('scriptaculous.js')
	);

	// create group
	$this->assets->group('prototaculous', array('js'=>$js) );


	// an IE only group
	$css = array('iefix.css');
	$js = array('iefix.js');
	$this->assets->group('iefix', array('js'=>$js, 'css'=>$js) );

	// you can even assign an asset to a group individually 
	// by passing the group name to the last parameter of the css/js functions
	$this->assets->css('spec.css', 'screen', 'spec-min.css', TRUE, FALSE, 'spec');


To output your assets, including appropriate markup:

	// display css
	$this->assets->display('css');

	//display js
	$this->assets->display('js');

	// display both
	$this->assets->display(); // OR $this->assets->display('both');

	// display group
	$this->assets->display('jquery'); // group name defined as jquery

	// display filterd group
	$this->assets->display('main', 'js'); // group name defined as main, only display JS

	// return string of asset references
	$string = $this->assets->display_string('main');

Note that the standard display function calls (the first 3 listed above) will only output
those assets not associated with a group (which are all included in the 'main' group).  Groups 
must be explicitly displayed via the 4th call listed above.



Since Carabiner won't delete old cached files, you'll need to clear them out manually.  
To do so programatically:

	// clear css cache
	$this->assets->empty_cache('css');

	//clear js cache
	$this->assets->empty_cache('js');

	// clear both
	$this->assets->empty_cache(); // OR $this->assets->empty_cache('both');

	// clear before a certain date
	$this->assets->empty_cache('both', 'now');	// String denoting a time before which cache 
													// files will be removed.  Any string that 
													// strtotime() can take is acceptable. 
													// Defaults to 'now'.
