<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Assets
 *
 * @package     Assets
 * @subpackage  Libraries
 * @category    Asset Management
 * @author      Topic Deisgn
 * @link        https://github.com/topicdesign/codeigniter-assets
 */

/**
 * Based on:
 *
 * @package     Carabiner
 * @author		Tony Dewan <tonydewan.com/contact>	
 * @link        https://github.com/tonydewan/Carabiner
 */


class Assets {
    
    public $base_uri = '';
    
    public $script_dir  = '';
	public $script_path = '';
	public $script_uri  = '';
	
	public $style_dir  = '';
	public $style_path = '';
	public $style_uri  = '';
	
	public $cache_dir  = '';
	public $cache_path = '';
	public $cache_uri  = '';
	
	public $dev     = FALSE;
	public $combine = TRUE;
	
	public $minify_js  = TRUE;
	public $minify_css = TRUE;
	public $force_curl = FALSE;
	
	private $js  = array('main'=>array());
	private $css = array('main'=>array());
	private $loaded = array();
	
    private $CI;
	
    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * @access  public
     * @param   array   config preferences
     *
     * @return  void
     */
    public function __construct($config = array())
	{
		$this->CI =& get_instance();
		log_message('debug', 'Assets: Library initialized.');

        if (count($config) > 0)
        {
            $this->initialize($config);
        }
	}

    // --------------------------------------------------------------------

    /**
     * Initialize the configuration options
     *
     * @access  public
     * @param   array   config options 
     * @return  void
     */
    public function initialize($config = array())
    {
        foreach ($config as $key => $val)
        {
            if($key == 'groups')
            {
                foreach($value as $group_name => $assets)
                {
					$this->group($group_name, $assets);
				}
				break;
			}
            if (method_exists($this, 'set_'.$key))
            {
                $this->{'set_'.$key}($val);
            }
            else if (isset($this->$key))
            {
                $this->$key = $val;
            }
        }
        // set the default value for base_uri from the config
        if($this->base_uri == '')
        {
            $this->base_uri = $this->CI->config->item('base_url');
        }

		// use the provided values to define the rest of them
		$this->script_path = FCPATH.$this->script_dir;
		$this->script_uri = $this->base_uri.$this->script_dir;

		$this->style_path = FCPATH.$this->style_dir;
		$this->style_uri = $this->base_uri.$this->style_dir;

		$this->cache_path = FCPATH.$this->cache_dir;
		$this->cache_uri = $this->base_uri.$this->cache_dir;

		log_message('debug', 'Assets: library configured.');
    }

    // --------------------------------------------------------------------

	/** 
     * Add JS file to queue
     *
     * @access	public
     * @param   mixed   $dev_file   (string) path to development file
     *                              (array) array of strings, may be nested
     * @param   string  $prod_file  path to production file (optional)
     * @param   bool    $combine    toggle whether the file is to be combined (optional)
     * @param   bool    $minify     toggle whether the file is to be minified (optional)
     * @param   string  $group      name with which the asset is to be associated (optional)
     *
     * @return  void
     */
    public function js($dev_file, $prod_file = '', $combine = TRUE, $minify = TRUE, $group = 'main')
	{	
        if( is_array($dev_file) )
        {
            if( is_array($dev_file[0]) )
            {
                foreach($dev_file as $file)
                {
					$d = $file[0];
					$p = (isset($file[1])) ? $file[1] : '';
					$c = (isset($file[2])) ? $file[2] : $combine;
					$m = (isset($file[3])) ? $file[3] : $minify;
					$g = (isset($file[4])) ? $file[4] : $group;

					$this->_asset('js', $d, $p, $c, $m, NULL, $g);
				}
            }
            else
            {
				$d = $dev_file[0];
				$p = (isset($dev_file[1])) ? $dev_file[1] : '';
				$c = (isset($dev_file[2])) ? $dev_file[2] : $combine;
				$m = (isset($dev_file[3])) ? $dev_file[3] : $minify;
				$g = (isset($dev_file[4])) ? $dev_file[4] : $group;

				$this->_asset('js', $d, $p, $c, $m, NULL, $g);
			}
        }
        else
        {
			$this->_asset('js', $dev_file, $prod_file, $combine, $minify, NULL, $group);
		}
    }

    // --------------------------------------------------------------------

	/**
     * Add CSS file to queue
     *
     * @access	public
     * @param   mixed   $dev_file   (string) path to development file
     *                              (array) array of strings, may be nested
     * @param   string  $prod_file  path to production file (optional)
     * @param   bool    $combine    toggle whether the file is to be combined (optional)
     * @param   bool    $minify     toggle whether the file is to be minified (optional)
     * @param   string  $group      name with which the asset is to be associated (optional)
     *
     * @return  void
     */
    public function css($dev_file, $media = 'screen', $prod_file = '', $combine = TRUE, $minify = TRUE, $group = 'main')
	{
        if(is_array($dev_file))
        {
            if(is_array($dev_file[0]))
            {
                foreach($dev_file as $file)
                {
					$d = $file[0];
					$m = (isset($file[1])) ? $file[1] : $media;
					$p = (isset($file[2])) ? $file[2] : '';
					$c = (isset($file[3])) ? $file[3] : $combine;
					$y = (isset($file[4])) ? $file[4] : $minify;
					$g = (isset($file[5])) ? $file[5] : $group;

					$this->_asset('css', $d, $p, $c, $y, $m, $g);
				}
            }
            else
            {
				$d = $dev_file[0];
				$m = (isset($dev_file[1])) ? $dev_file[1] : $media;
				$p = (isset($dev_file[2])) ? $dev_file[2] : '';
				$c = (isset($dev_file[3])) ? $dev_file[3] : $combine;
				$y = (isset($dev_file[4])) ? $dev_file[4] : $minify;
				$g = (isset($dev_file[5])) ? $dev_file[5] : $group;
									
				$this->_asset('css', $d, $p, $c, $y, $m, $g);
			}
        }
        else
        {
			$this->_asset('css', $dev_file, $prod_file, $combine, $minify, $media, $group);
		}
    }

    // --------------------------------------------------------------------
	
	/**
     * Add Assets to a group
     *
     * @access	public
     * @param   string  $group_name     the name of the group
     * @param   array   $assets         assets to be included in the group
     *
     * @return  void
     */
    public function group($group_name, $assets)
	{
        if( ! isset($assets['js']) && ! isset($assets['css']))
        {
			log_message('error', "Assets: The asset group definition named '{$group_name}' does not contain a well formed array.");
			return;
		}

        if(isset($assets['js']))
        {
            $this->js($assets['js'], '', TRUE, TRUE, $group_name);
        }
        if(isset($assets['css']))
        {
            $this->css($assets['css'], 'screen', '', TRUE, TRUE, $group_name);
        }
	}

    // --------------------------------------------------------------------

	/**
     * Add an asset to queue
     *
     * @access	private
     * @param	string  $type       type of asset lowercase (css || js)
     * @param   string  $dev_file   path to development version of the asset
     * @param   string  $prod_file  path to production version of the asset (optional)
     * @param   bool    $combine    toggle whether the file is to be combined (optional)
     * @param   bool    $minify     toggle whether the file is to be minified (optional)
     * @param   string  $media      media type associated with the css asset (optional)
     * @param   string  $group      group name the asset is to be associated with (optional)
     *
     * @return  void
     */
    private function _asset($type, $dev_file, $prod_file = '', $combine, $minify, $media = 'screen', $group = 'main')
	{
        if ($type == 'css')
        {
			$this->css[$group][$media][] = array( 'dev'=>$dev_file );
			$index = count($this->css[$group][$media]) - 1;

            if($prod_file != '')
            {
                $this->css[$group][$media][$index]['prod'] = $prod_file;
            }
			$this->css[$group][$media][$index]['combine'] = $combine;
			$this->css[$group][$media][$index]['minify'] = $minify;
        }
        else
        {
			$this->js[$group][] = array( 'dev'=>$dev_file );
			$index = count($this->js[$group]) - 1;
			
            if($prod_file != '')
            {
                $this->js[$group][$index]['prod'] = $prod_file;
            }
			$this->js[$group][$index]['combine'] = $combine;
			$this->js[$group][$index]['minify'] = $minify;
        }
	}

    // --------------------------------------------------------------------

	/**
     * Display HTML references to the assets
     *
     * @access  public
     * @param   string  $flag           toggle the asset type: (css || js || both || group)
     * @param   string  $group_filter   asset type to filter a group (js || css)
     *
     * @return  void
     */
    public function display($flag = 'both', $group_filter = NULL)
	{	
        switch($flag)
        {
			case 'JS':
			case 'js':
				$this->_display_js();
                break;
			case 'CSS':
			case 'css':
				$this->_display_css();
                break;
			case 'both':
				$this->_display_js();
				$this->_display_css();
                break;
			default:
                if(isset($this->js[$flag]) && ($group_filter == NULL || $group_filter == 'js'))
                {
                    $this->_display_js($flag);
                }
                if(isset($this->css[$flag]) && ($group_filter == NULL || $group_filter == 'css'))
                {
                    $this->_display_css($flag);
                }
                break;
		}
    }

    // --------------------------------------------------------------------

	/** 
     * HTML references to the assets, returned as a string
     *
     * @access	public
     * @param   string  $flag   the asset type (css || js || both || group name)
     *
     * @return  string  HTML references
     */
    public function display_string($flag='both', $group_filter = NULL)
	{
        ob_start();		

        $this->display($flag, $group_filter);
		$contents = ob_get_contents();

        ob_end_clean();

		return $contents;
	}

    // --------------------------------------------------------------------

	/**
     * Display HTML references to the js assets
     *
     * @access	private
     * @param   string  $group  asset group name
     *
     * @return  void
     */
    private function _display_js($group = 'main')
	{
        if(empty($this->js))
        {
            return; // if there aren't any js files, just stop!
        }

        if( ! isset($this->js[$group]))
        {
            // the group you asked for doesn't exist. This should never happen, but better to be safe than sorry.
			log_message('error', "Assets: The JavaScript asset group named '{$group}' does not exist.");
            return;
        }

        if($this->dev)
        {
            // in a dev environment
            foreach($this->js[$group] as $ref)
            {
				echo $this->_tag('js', $ref['dev']);
		    }
        }
        elseif($this->combine && $this->minify_js)
        {
            // we're combining files and minifying them
			$lastmodified = 0;
			$files = array();
			$filenames = '';

            foreach($this->js[$group] as $ref)
            {
				// get the last modified date of the most recently modified file
				$lastmodified = max($lastmodified , filemtime(realpath($this->script_path.$ref['dev'])));

				$filenames .= $ref['dev'];

                if( ! $ref['combine'])
                {
                    echo (isset($ref['prod']))
                        ? $this->_tag('js', $ref['prod'])
                        : $this->_tag('js', $ref['dev'])
                        ;					
                }
                elseif( ! $ref['minify'])
                {
                    $files[] = (isset($ref['prod']))
                        ? array('prod'=>$ref['prod'], 'dev'=>$ref['dev'], 'minify'=>$ref['minify'] )
                        : array('dev'=>$ref['dev'], 'minify'=>$ref['minify'])
                        ;
                }
                else
                {
                    $files[] = (isset($ref['prod']))
                        ? array('prod'=>$ref['prod'], 'dev'=>$ref['dev'] )
                        : array('dev'=>$ref['dev'])
                        ;
                }
		    }

			$lastmodified = ($lastmodified == 0) ? '0000000000' : $lastmodified;
			
			$filename = $lastmodified . md5($filenames).'.js';
			
            if( ! file_exists($this->cache_path.$filename))
            {
                $this->_combine('js', $files, $filename);
            }
			echo $this->_tag('js', $filename, TRUE);
        }
        elseif($this->combine && ! $this->minify_js)
        {
            // we're combining files but not minifying
			$lastmodified = 0;
			$files = array();
			$filenames = '';

            foreach($this->js[$group] as $ref)
            {
				// get the last modified date of the most recently modified file
				$lastmodified = max( $lastmodified , filemtime(realpath($this->script_path.$ref['dev'])) );

				$filenames .= $ref['dev'];

                if(!$ref['combine'])
                {
                    echo (isset($ref['prod']))
                        ? $this->_tag('js', $ref['prod'])
                        : $this->_tag('js', $ref['dev'])
                        ;					
                }
                else
                {
                    $files[] = (isset($ref['prod']))
                        ? array('prod'=>$ref['prod'], 'dev'=>$ref['dev'], 'minify'=> FALSE )
                        : array('dev'=>$ref['dev'], 'minify'=> FALSE)
                        ;
                }
            }	

			$lastmodified = ($lastmodified == 0) ? '0000000000' : $lastmodified;
			
			$filename = $lastmodified . md5($filenames).'.js';
			
            if( ! file_exists($this->cache_path.$filename))
            {
                $this->_combine('js', $files, $filename);
            }
			echo $this->_tag('js', $filename, TRUE);
        }
        elseif( ! $this->combine && $this->minify_js)
        {
            // we're minifying. but not combining
            foreach($this->js[$group] as $ref)
            {
                if(isset($ref['prod']))
                {
					$f = $ref['prod'];
                }
                elseif( ! $ref['minify'])
                {
					$f = $ref['dev'];
                }
                else
                {
					$f = filemtime(realpath($this->script_path . $ref['dev'])) . md5($ref['dev']) . '.js';
                    if( ! file_exists($this->cache_path.$f) )
                    {
						$c = $this->_minify( 'js', $ref['dev'] );
						$this->_cache($f, $c);
			        }	
				}
				echo $this->_tag('js', $f, TRUE);
            }
        }
        else
        {
            // we're not in dev mode, but combining isn't okay and minifying isn't allowed.
            // -- this will just display the production version if there is one, dev if there isn't.
            foreach($this->js[$group] as $ref)
            {
				$f = (isset($ref['prod'])) ? $ref['prod'] : $ref['dev'];
				echo $this->_tag('js', $f);
            }
		}
	}

    // --------------------------------------------------------------------

	/**
     * Display HTML references to the css assets
     *
     * @access	private
     * @param   string  $group  the asset group name
     *
     * @return  void
     */
    private function _display_css($group = 'main')
	{
        if(empty($this->css))
        {
            return; // there aren't any css assets, so just stop!
        }

        if( ! isset($this->css[$group]))
        {
            // the group you asked for doesn't exist. This should never happen, but better to be safe than sorry.
			log_message('error', "Assets: The CSS asset group named '{$group}' does not exist.");
            return;
        }

        if($this->dev)
        {
            // we're in a development environment
            foreach($this->css[$group] as $media => $refs)
            {
                foreach($refs as $ref)
                {
					echo $this->_tag('css', $ref['dev'], FALSE, $media);
		        }
            }
        }
        elseif($this->combine && $this->minify_css)
        {
            // we're combining and minifying
            foreach($this->css[$group] as $media => $refs)
            {
				// lets try to cache it, shall we?
				$lastmodified = 0;
				$files = array();
				$filenames = '';
		
                foreach ($refs as $ref)
                {
					$lastmodified = max($lastmodified, filemtime( realpath( $this->style_path . $ref['dev'] ) ) );
					$filenames .= $ref['dev'];
			
                    if( ! $ref['combine'])
                    {
                        echo (isset($ref['prod']))
                            ? $this->_tag('css', $ref['prod'], $media)
                            : $this->_tag('css', $ref['dev'], $media)
                            ;
                    }
                    elseif( ! $ref['minify'])
                    {
                        $files[] = (isset($ref['prod']))
                            ? array('prod'=>$ref['prod'], 'dev'=>$ref['dev'], 'minify'=>$ref['minify'] )
                            : array('dev'=>$ref['dev'], 'minify'=>$ref['minify'])
                            ;
                    }
                    else
                    {
                        $files[] = (isset($ref['prod']))
                            ? array('prod'=>$ref['prod'], 'dev'=>$ref['dev'] )
                            : array('dev'=>$ref['dev'])
                            ;
                    }
				}	
				
				$lastmodified = ($lastmodified == 0) ? '0000000000' : $lastmodified;
				
				$filename = $lastmodified . md5($filenames).'.css';
		
                if( ! file_exists($this->cache_path.$filename))
                {
                    $this->_combine('css', $files, $filename);
                }
				echo $this->_tag('css',  $filename, TRUE, $media);
		    }
        }
        elseif($this->combine && ! $this->minify_css)
        {
            // we're combining bot not minifying
            foreach($this->css[$group] as $media => $refs)
            {
				// lets try to cache it, shall we?
				$lastmodified = 0;
				$files = array();
				$filenames = '';
			
                foreach ($refs as $ref)
                {
			
					$lastmodified = max($lastmodified, filemtime( realpath( $this->style_path . $ref['dev'] ) ) );
					$filenames .= $ref['dev'];
				
                    if($ref['combine'] == false)
                    {
                        echo (isset($ref['prod']))
                            ? $this->_tag('css', $ref['prod'], $media)
                            : $this->_tag('css', $ref['dev'], $media)
                            ;
                    }
                    else
                    {
                        $files[] = (isset($ref['prod']))
                            ? array('prod'=>$ref['prod'], 'dev'=>$ref['dev'], 'minify'=>FALSE )
                            : array('dev'=>$ref['dev'], 'minify'=>FALSE)
                            ;
                    }
                }
				
				$lastmodified = ($lastmodified == 0) ? '0000000000' : $lastmodified;
				
				$filename = $lastmodified . md5($filenames).'.css';
			
                if( ! file_exists($this->cache_path.$filename))
                {
                    $this->_combine('css', $files, $filename);
                }
				echo $this->_tag('css',  $filename, TRUE, $media);
            }
        }
        elseif( ! $this->combine && $this->minify_css)
        {
            // we want to minify, but not combine
            foreach($this->css[$group] as $media => $refs)
            {
                foreach($refs as $ref)
                {
                    if(isset($ref['prod']))
                    {
						$f = $this->style_uri . $ref['prod'];
                    }
                    elseif( ! $ref['minify'])
                    {
						$f = $this->style_uri . $ref['dev'];
                    }
                    else
                    {
						$f = filemtime(realpath($this->style_path . $ref['dev'])) . md5($ref['dev']) . '.css';
				
                        if( ! file_exists($this->cache_path.$f) )
                        {
							$c = $this->_minify( 'css', $ref['dev'] );
							$this->_cache($f, $c);
				        }
					}
					echo $this->_tag('css', $f, TRUE, $media);
	            }	
            }
        }
        else
        {
            // we're in a production environment, but not minifying or combining.
            foreach($this->css[$group] as $media => $refs)
            {
                foreach($refs as $ref)
                {
					$f = (isset($ref['prod'])) ? $ref['prod'] : $ref['dev'];
					echo $this->_tag('css', $f, FALSE, $media);
			    }
		    }
		}
    }

    // --------------------------------------------------------------------

	/**
     * Internal function for compressing/combining scripts
     *
     * @access	private
     * @param   string  $flag       the asset type: css|js
     * @param	array   $files      file references to be combined
     *                              Should contain arrays, as included in primary
     *                              asset arrays: ('dev'=>$dev, 'prod'=>$prod, 'minify'=>TRUE||FALSE)
     * @param   string  $filename   filename of the file-to-be
     *
     * @return  void
     */
    private function _combine($flag, $files, $filename)
	{
		$file_data = '';

		$path = ($flag == 'css') ? $this->style_path : $this->script_path;
		$minify = ($flag == 'css') ? $this->minify_css : $this->minify_js;

        foreach($files as $file)
        {
			$v = (isset($file['prod'])) ? 'prod' : 'dev';
			
            if((isset($file['minify']) && $file['minify'] == true) || ( ! isset($file['minify']) && $minify))
            {
				$file_data .=  $this->_minify($flag, $file['dev']) . "\n";
			}
            else
            {
				$r = ($this->isURL($file[$v])) ? $file[$v] : realpath($path.$file[$v]);
				$file_data .=  $this->_get_contents( $r ) ."\n";
			}
        }
		$this->_cache($filename, $file_data);
	}

    // --------------------------------------------------------------------

	/**
     * Internal function for minifying assets
     *
     * @access	private
     * @param   string  $flag       the asset type: css|js
     * @param   string  $file_ref   path to the file whose contents should be minified
     *
     * @return  string  minified contents of file
     */
    private function _minify($flag, $file_ref)
	{
		$path = ($flag == 'css') ? $this->style_path : $this->script_path;
		$ref  = ($this->isURL($file_ref)) ? $file_ref : realpath($path.$file_ref);

        switch($flag)
        {
			case 'js':
				$this->_load('jsmin');
				
				$contents = $this->_get_contents($ref);
				return $this->CI->jsmin->minify($contents);
                break;
			case 'css':
				$this->_load('cssmin');
				
				$rel = ($this->isURL($file_ref)) ? $file_ref : dirname($this->style_uri.$file_ref).'/';
				$this->CI->cssmin->config(array('relativePath'=>$rel));

				$contents = $this->_get_contents($ref);
				return $this->CI->cssmin->minify($contents);
                break;
		}
	}

    // --------------------------------------------------------------------

	/** 
     * Internal function for getting a files contents, using cURL or file_get_contents, depending on circumstances
     *
     * @access	private
     * @param   string  $ref    full path to the file (or full URL, if appropriate)
     *
     * @return  string  files contents
     */
    private function _get_contents($ref)
	{
        if($this->isURL($ref) && (ini_get('allow_url_fopen') == 0 || $this->force_curl))
        {
			$this->_load('curl');
			$contents = $this->CI->curl->simple_get($ref);
		}
        else
        {
			$contents = file_get_contents($ref);
	    }
		return $contents;
    }

    // --------------------------------------------------------------------

	/**
     * Internal function for writing cache files
     *
     * @access	private
     * @param   string  $filename   filename of the new file
     * @param   string  $file_data  contents of the new file
     *
     * @return  bool
     */
    private function _cache($filename, $file_data)
	{
        if(empty($file_data))
        {
			log_message('debug', 'Assets: Cache file '.$filename.' was empty and therefore not written to disk at '.$this->cache_path);
			return false;
        }

		$filepath = $this->cache_path . $filename;
		$success = file_put_contents($filepath, $file_data);
		
        if($success)
        {
			log_message('debug', 'Assets: Cache file '.$filename.' was written to '.$this->cache_path);
			return TRUE;
        }
        else
        {
			log_message('error', 'Assets: There was an error writing cache file '.$filename.' to '.$this->cache_path);
			return FALSE;
        }
	}

    // --------------------------------------------------------------------

	/**
     * Internal function for making tag strings
     *
     * @access	private
     * @param   string  $flag   tag type: css|js
     * @param	string  $ref    reference of file
     * @param   bool    $cache  use cache dir
     * @param   string  $media  media type for the tag (CSS only)
     *
     * @return  string  HTML tag reference to given reference
     */
    private function _tag($flag, $ref, $cache = FALSE, $media = 'screen')
    {
        // set base directory
        if ($this->isURL($ref))
        {
            $dir = '';
        }
        elseif ($cache)
        {
            $dir = $this->cache_uri;
        }
        else
        {
            switch ($flag)
            {
                case 'css':
                    $dir = $this->style_uri;
                    break;
                case 'js':
                    $dir = $this->script_uri;
                    break;
            }
        }
        // get HTML
        switch($flag)
        {
            case 'css':
				return '<link type="text/css" rel="stylesheet" href="'.$dir.$ref.'" media="'.$media.'" />'."\r\n";
                break;
			case 'js':
				return '<script type="text/javascript" src="'.$dir.$ref.'" charset="'.$this->CI->config->item('charset').'"></script>'."\r\n";
                break;
		}
	}

    // --------------------------------------------------------------------

	/**
     * Function used to clear the asset cache. If no flag is set, both CSS and JS will be emptied.
     *
     * @access	public
     * @param   string  $flag       asset type: css|js|both
     * @param   string  $before     time before which cache files will be removed
     *                              Any string that strtotime() can take is acceptable
     * 
     * @return  void
     */
    public function empty_cache($flag = 'both', $before = 'now')
	{
		$this->CI->load->helper('file');
		
		$files = get_filenames($this->cache_path);
		$before = strtotime($before);
		
        switch($flag)
        {
			case 'js':
			case 'css':
                foreach( $files as $file )
                {
					$ext = substr(strrchr($file, '.'), 1);
					$fl = strlen(substr($file, 0, -(strlen($flag)+1)));
					
                    if (($ext == $flag) && $fl >= 42 && (filemtime($this->cache_path . $file) < $before))
                    {
						$success = unlink($this->cache_path . $file);
					
                        if($success)
                        {
                            log_message('debug', 'Assets: Cache file '.$file.' was removed from '.$this->cache_path);
                        }
                        else
                        {
                            log_message('error', 'Assets: There was an error removing cache file '.$file.' from '.$this->cache_path);
                        }
					}
				}
                break;
			case 'both':
			default:
                foreach($files as $file)
                {
					$ext = substr(strrchr($file, '.'), 1);
					$fl = strlen(substr($file, 0, -3));

                    if (($ext == 'js' || $ext == 'css') && $fl >= 42 && (filemtime($this->cache_path . $file) < $before))
                    {
						$success = unlink($this->cache_path . $file);
						
                        if($success)
                        {
                            log_message('debug', 'Assets: Cache file '.$file.' was removed from '.$this->cache_path);
                        }
                        else
                        {
                            log_message('error', 'Assets: There was an error removing cache file '.$file.' from '.$this->cache_path);
                        }
					}
				}	
                break;
        }
    }

    // --------------------------------------------------------------------

	/**
     * Function used to prevent multiple load calls for the same CI library
     *
     * @access	private
     * @param   string  $lib    library name
     * 
     * @return  bool
     */
    private function _load($lib=NULL)
	{
        if($lib == NULL)
        {
            return FALSE;
        }
        if(isset($this->loaded[$lib]))
        {
			return FALSE;
        }
        else
        {
			$this->CI->load->library($lib);
			$this->loaded[$lib] = TRUE;

			log_message('debug', 'Assets: Codeigniter library '."'$lib'".' loaded');
			return TRUE;
        }
    }

    // --------------------------------------------------------------------

	/**
     * isURL
     * Checks if the provided string is a URL. Allows for port, path and query string validations.
     * This should probably be moved into a helper file, but I hate to add a whole new file for 
     * one little 2-line function.
     *
     * @access	public
     * @param   string  $string     to be checked
     *
     * @return  bool
     */
    public static function isURL($string)
	{
		$pattern = '@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@';
		return preg_match($pattern, $string);
    }

    // --------------------------------------------------------------------

}
/* End of file Assets.php */
/* Location: ./libraries/Assets.php */
