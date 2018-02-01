<?php
/**
 * PHP Codeigniter Simplicity
 *
 *
 * Copyright (C) 2013  John Skoumbourdis.
 *
 * GROCERY CRUD LICENSE
 *
 * Codeigniter Simplicity is released with dual licensing, using the GPL v3 and the MIT license.
 * You don't have to do anything special to choose one license or the other and you don't have to notify anyone which license you are using.
 * Please see the corresponding license file for details of these licenses.
 * You are free to use, modify and distribute this software, but all copyright information must remain.
 *
 * @package    	Codeigniter Simplicity
 * @copyright  	Copyright (c) 2013, John Skoumbourdis
 * @license    	https://github.com/scoumbourdis/grocery-crud/blob/master/license-grocery-crud.txt
 * @version    	0.6
 * @author     	John Skoumbourdis <scoumbourdisj@gmail.com>
 */
class MY_Output extends CI_Output {
  
	const OUTPUT_MODE_NORMAL = 10;
	const OUTPUT_MODE_TEMPLATE = 11;
	const TEMPLATE_ROOT = "themes/";

	protected $_title = "";
	protected $_charset = "utf-8";
	protected $_language = "en-us";
	protected $_canonical = "";
	protected $_meta = array("keywords"=>array(), "description"=>null);
	protected $_rdf = array("keywords"=>array(), "description"=>null);
	protected $_template = null;
	protected $_mode = self::OUTPUT_MODE_NORMAL;
	protected $_messages = array("error"=>"", "info"=>"", "debug"=>"");
	protected $_output_data = array();

  // add codeigniter simplicity function from my_output
  private $_javascript = array();
	private $_css = array();
	private $_inline_scripting = array("infile"=>"", "stripped"=>"", "unstripped"=>"");
	private $_sections = array();
	private $_cached_css = array();
	private $_cached_js = array();

	function __construct(){

		if(!defined('SPARKPATH'))
		{
			define('SPARKPATH', 'sparks/');
		}

		parent::__construct();
  }

	function css(){
    
		$css_files = func_get_args();

		foreach($css_files as $css_file){
			$css_file = substr($css_file,0,1) == '/' ? substr($css_file,1) : $css_file;
      //echo $css_file;
			$is_external = false;
			if(is_bool($css_file))
				continue;

			$is_external = preg_match("/^https?:\/\//", trim($css_file)) > 0 ? true : false;

			if(!$is_external)
				if(!file_exists($css_file))
					show_error("Cannot locate stylesheet file: {$css_file}.");

			$css_file = $is_external == FALSE ? base_url($css_file) : $css_file;

			if(!in_array($css_file, $this->_css)){
				$this->_css[] = $css_file;
      }

		}

		return;
    
	}

  public function check_css(){
    return $this->_css;
  }
  
	public function js(){
    
		$script_files = func_get_args();

		foreach($script_files as $script_file){
			$script_file = substr($script_file,0,1) == '/' ? substr($script_file,1) : $script_file;

			$is_external = false;
			if(is_bool($script_file))
				continue;

			$is_external = preg_match("/^https?:\/\//", trim($script_file)) > 0 ? true : false;

			if(!$is_external)
				if(!file_exists($script_file))
					show_error("Cannot locate javascript file: {$script_file}.");

			$script_file = $is_external == FALSE ?  base_url($script_file)  : $script_file;

			if(!in_array($script_file, $this->_javascript))
				$this->_javascript[] = $script_file ;
		}

		return;
    
	}

	function start_inline_scripting(){
		ob_start();
	}

	function end_inline_scripting($strip_tags=true, $append_to_file=true){
		$source = ob_get_clean();

		 if($strip_tags){
			 $source = preg_replace("/<script.[^>]*>/", '', $source);
			 $source = preg_replace("/<\/script>/", '', $source);
		 }

		 if($append_to_file){

		 	$this->_inline_scripting['infile'] .= $source;

		 }else{

		 	if($strip_tags){
		 		$this->_inline_scripting['stripped'] .= $source;
		 	}else{
		 		$this->_inline_scripting['unstripped'] .= $source;
		 	}
		 }
	}

	public function get_css_files(){
		return $this->_css;
	}

	function get_cached_css_files(){
		return $this->_cached_css;
	}

	function get_js_files(){
		return $this->_javascript;
	}

	function get_cached_js_files(){
		return $this->_cached_js;
	}

	function get_inline_scripting(){
		return $this->_inline_scripting;
	}

	/**
	 * Loads the requested view in the given area
	 * <em>Useful if you want to fill a side area with data</em>
	 * <em><b>Note: </b> Areas are defined by the template, those might differs in each template.</em>
	 *
	 * @param string $area
	 * @param string $view
	 * @param array $data
	 * @return string
	 */
	function section($area, $view, $data=array()){
		if(!array_key_exists($area, $this->_sections))
			$this->_sections[$area] = array();

    $CI =& get_instance();
        
		$content = $CI->load->view($view, $data, true);

    
		$checksum = md5( $view . serialize($data) );

		$this->_sections[$area][$checksum] = $content;
    
    return $checksum;
	}

	function get_section($section_name)
	{
		$section_string = '';
		if(isset($this->_sections[$section_name]))
			foreach($this->_sections[$section_name] as $section)
				$section_string .= $section;

		return $section_string;
	}
  
	/**
	 * Gets the declared sections
	 *
	 * @return object
	 */
	function get_sections(){
		return (object)$this->_sections;
	}

   /*
    * Can load a view file from an absolute path and
    * relative to the CodeIgniter index.php file
    * Handy if you have views outside the usual CI views dir
    */
  function viewfile($viewfile, $vars = array(), $return = FALSE)
  {
    return $this->_ci_load(
          array('_ci_path' => $viewfile,
              '_ci_vars' => $this->_ci_object_to_array($vars),
              '_ci_return' => $return)
    );
  }


  /**
   * Specific Autoloader (99% ripped from the parent)
   *
   * The config/autoload.php file contains an array that permits sub-systems,
   * libraries, and helpers to be loaded automatically.
   *
   * @access	protected
   * @param	array
   * @return	void
   */
  function _ci_autoloader($basepath = NULL)
  {
    if($basepath !== NULL)
    {
      $autoload_path = $basepath.'config/autoload.php';
    }
    else
    {
    $autoload_path = APPPATH.'config/autoload.php';
    }
    
    if(! file_exists($autoload_path))
    {
      return FALSE;
    }

    include_once($autoload_path);

    if ( ! isset($autoload))
    {
      return FALSE;
    }

    // Autoload packages
    if (isset($autoload['packages']))
    {
      foreach ($autoload['packages'] as $package_path)
      {
        $this->add_package_path($package_path);
      }
    }

    // Autoload sparks
    if (isset($autoload['sparks']))
    {
      foreach ($autoload['sparks'] as $spark)
      {
        $this->spark($spark);
      }
    }

    if (isset($autoload['config']))
    {
      // Load any custom config file
      if (count($autoload['config']) > 0)
      {
        $CI =& get_instance();
        foreach ($autoload['config'] as $key => $val)
        {
        $CI->config->load($val);
        }
      }
    }

    // Autoload helpers and languages
    foreach (array('helper', 'language') as $type)
    {
      if (isset($autoload[$type]) AND count($autoload[$type]) > 0)
      {
        $this->$type($autoload[$type]);
      }
    }

    // A little tweak to remain backward compatible
    // The $autoload['core'] item was deprecated
    if ( ! isset($autoload['libraries']) AND isset($autoload['core']))
    {
      $autoload['libraries'] = $autoload['core'];
    }

    // Load libraries
    if (isset($autoload['libraries']) AND count($autoload['libraries']) > 0)
    {
      // Load the database driver.
      if (in_array('database', $autoload['libraries']))
      {
        $this->database();
        $autoload['libraries'] = array_diff($autoload['libraries'], array('database'));
      }

      // Load all other libraries
      foreach ($autoload['libraries'] as $item)
      {
        $this->library($item);
      }
    }

    // Autoload models
    if (isset($autoload['model']))
    {
    $this->model($autoload['model']);
    }
  }
  // end of codeigniter simplicity loader nethods
    
  
	/**
	 * Set the  template that should be contain the output <br /><em><b>Note:</b> This method set the output mode to MY_Output::OUTPUT_MODE_TEMPLATE</em>
	 *
	 * @uses MY_Output::set_mode()
	 * @param string $template_view
	 * @return void
	 */
	function set_template($template_view){
		$this->set_mode(self::OUTPUT_MODE_TEMPLATE);
		$template_view = str_replace(".php", "", $template_view);
		$this->_template = self::TEMPLATE_ROOT . $template_view;
	}

	/**set_mode alias
	 *
	 * Enter description here ...
	 */
	function unset_template()
	{
		$this->_template = null;
		$this->set_mode(self::OUTPUT_MODE_NORMAL);
	}

	public function set_common_meta($title, $description, $keywords)
	{
		$this->set_meta("description", $description);
		$this->set_meta("keywords", $keywords);
		$this->set_title($title);
	}

	/**
	 * Sets the way that the final output should be handled.<p>Accepts two possible values 	MY_Output::OUTPUT_MODE_NORMAL for direct output
	 * or MY_Output::OUTPUT_MODE_TEMPLATE for displaying the output contained in the specified template.</p>
	 *
	 * @throws Exception when the given mode hasn't defined.
	 * @param integer $mode one of the constants MY_Output::OUTPUT_MODE_NORMAL or MY_Output::OUTPUT_MODE_TEMPLATE
	 * @return void
	 */
	function set_mode($mode){

		switch($mode){
			case self::OUTPUT_MODE_NORMAL:
			case self::OUTPUT_MODE_TEMPLATE:
				$this->_mode = $mode;
				break;
			default:
				throw new Exception(get_instance()->lang->line("Unknown output mode."));
		}

		return;
	}

	/**
	 * Set the title of a page, it works only with MY_Output::OUTPUT_MODE_TEMPLATE
	 *
	 *
	 * @param string $title
	 * @return void
	 */
	function set_title($title){
    //echo $title;
		$this->_title = $title;
	}

	/**
	 * Append the given string at the end of the current page title
	 *
	 * @param string $title
	 * @return void
	 */
	function append_title($title){
		$this->_title .= " - {$title}";
	}

	/**
	 * Prepend the given string at the bigining of the curent title.
	 *
	 * @param string $title
	 * @return void
	 */
	function prepend_title($title){
		$this->_title = "{$title} - {$this->_title}";
	}

	function set_message($message, $type="error"){
// 		log_message($type, $message);
		$this->_messages[$type] .= $message;
//		get_instance()->session->set_flashdata("__messages", serialize($this->_messages));
	}

	/**
	 * (non-PHPdoc)
	 * @see system/libraries/CI_Output#_display($output)
	 */
	function _display($output=''){

		if($output=='')
			$output = $this->get_output();

		switch($this->_mode){
			case self::OUTPUT_MODE_TEMPLATE:
				$output = $this->get_template_output($output);
				break;
			case self::OUTPUT_MODE_NORMAL:
			default:
				$output = $output;
				break;
		}

		parent::_display($output);
	}

	function set_output_data($varname, $value){
		$this->_output_data[$varname] = $value;
	}

	private function get_template_output($output){

		if(function_exists("get_instance") && class_exists("CI_Controller")){
			$ci = get_instance();

			$inline = $this->get_inline_scripting();

			if($inline["infile"]!=""){
				$checksum = md5($inline["infile"], false);
				$ci->load->driver('cache');
				$ci->cache->memcached->save($checksum, $inline["infile"], 5*60);
				$ci->load->js(site_url("content/js/{$checksum}.js"), true);
			}

			if( strlen($inline['stripped']) ){
				$inline['unstripped'] .= "\r\n\r\n<script type=\"text/javascript\">{$inline['stripped']}</script>";
			}

			$data = array();

			$css_files = $this->get_css_files();
			$js_files = $this->get_js_files();

			$cached_js_files = $this->get_cached_js_files();
			if(!empty($cached_js_files))
			{
				$cached_js_files_string = '';
				foreach ($cached_js_files as $cahed_js_file) {
					$cached_js_files_string .= str_replace("\t","",file_get_contents($cahed_js_file, FILE_USE_INCLUDE_PATH));
				}

				$cache_file_name = 'cache_'.md5(serialize($cached_js_files)).'.js';
				$cache_file_path = 'assets/themes/default/js/'.$cache_file_name;

				$fh = fopen($cache_file_path, 'w') or die("can't open file");
				fwrite($fh, $cached_js_files_string);
				fclose($fh);

				$js_files[] = base_url().$cache_file_path;

			}

			if (is_array($this->_meta["keywords"]))
			{
				$this->_meta["keywords"] = implode(" ,", $this->_meta["keywords"]);
			}
      
			$data["output"] = $output;
			$data["messages"] = $this->_messages;
			$data["modules"] = $this->get_sections();
			$data["title"] = $this->_title;
			$data["meta"] = $this->_meta;
			$data["language"] = $this->_language;
			$data["rdf"] = $this->_rdf;
			$data["charset"] = $this->_charset;
			$data["js"] = $js_files;
			$data["css"] = $css_files;
			$data["inline_scripting"] = $inline['unstripped'];
			$data["canonical"] = $this->_canonical;
			$data["ci"]			= &get_instance();

			$data = array_merge($data, $this->_output_data);
      
      $output = $ci->load->view($this->_template, $data, true);
		
    }
    
    
		return $output;
	}

	/**
	 * Adds meta tags.
	 *
	 * @access public
	 * @param string $name the name of the meta tag
	 * @param string $content the content of the meta tag
	 * @return bool
	 */
	public function set_meta($name, $content){
		$this->_meta[$name] = $content;
		return true;
	}

  public function set_canonical($url)
  {
     $this->_canonical = $url;
  }
}