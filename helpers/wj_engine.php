<?php
defined('WYSIJA') or die('Restricted access');
/**
 * @class Wysija Engine Helper (PHP4 version)
 */
class WYSIJA_help_wj_engine extends WYSIJA_object {
    var $_context = 'editor';

    // data holders
    var $_data = null;
    var $_styles = null;
    
    // styles: defaults
    var $TEXT_SIZES = array(8, 9, 10, 11, 12, 13, 14, 16, 18, 24, 36, 48, 72);
    var $TITLE_SIZES = array(24, 26, 28, 30, 32, 34, 36, 40, 44, 48, 54, 60, 66, 72);
    var $DIVIDER_SIZES = array(1, 2, 3, 4, 5);
    var $DIVIDER_TYPES = array('solid', 'dotted', 'dashed');
    var $FONTS = array("Arial", "Arial Black", "Comic Sans MS", "Courier New", "Georgia", "Impact", "Tahoma", "Times New Roman", "Trebuchet MS", "Verdana");
    
    /* Constructor */
    function WYSIJA_help_wj_engine(){ }
    
    /* i18n methods */
    function getTranslations() {
        return array(
            'dropLogoHere' => __('Drop your logo or title in this header.',WYSIJA),
            'clickToEditText' => __('Click here to add a title or text.', WYSIJA),
            'alignmentLeft' =>  __('Align left',WYSIJA),
            'alignmentCenter' => __('Align center',WYSIJA),
            'alignmentRight' => __('Align right',WYSIJA),
            'addImageLink' => __('Add link / Alternative text',WYSIJA),
            'removeImageLink' => __('Remove link',WYSIJA),
            'removeImage' => __('Remove image',WYSIJA),
            'editText' => __( 'Edit text',WYSIJA),
            'removeText' => __('Remove text',WYSIJA),
            'textLabel' => __('Plain text',WYSIJA),
            'dividerLabel' => __('Horizontal line',WYSIJA),
            'customDividerLabel' => __('Custom horizontal line',WYSIJA),
            'postLabel' => __('Wordpress post',WYSIJA),
            'styleBodyLabel' => __('Text',WYSIJA),
            'styleH1Label' => __('Heading 1',WYSIJA),
            'styleH2Label' => __('Heading 2',WYSIJA),
            'styleH3Label' => __('Heading 3',WYSIJA),
            'styleLinksLabel' => __('Links',WYSIJA),
            'styleLinksDecorationLabel' => __('Underline links',WYSIJA),
            'styleFooterLabel' => __('Footer text',WYSIJA),
            'styleFooterBackgroundLabel' => __('Footer background',WYSIJA),
            'styleBodyBackgroundLabel' => __('Newsletter color',WYSIJA),
            'styleHtmlBackgroundLabel' => __('Background color', WYSIJA),
            'styleHeaderBackgroundLabel' => __('Header background color', WYSIJA),
            'styleDividerLabel' => __('Horizontal line',WYSIJA),
            'articleSelectionTitle' => __('Article Selection', WYSIJA),
            'addLinkTitle' => __('Add Link & Alternative text', WYSIJA)
        );
    }
    
    /* Data methods */
    function getData($type = null) {
        if($type !== null) return $this->_data[$type];
        return $this->_data;
    }
    
    function setData($value = null, $decode = false) {
        if(!$value) {
            $this->_data = $this->getDefaultData();
        } else {
            $this->_data = $value;
            if($decode) {
                $this->_data = $this->getDecoded('data');
            }
        }
    }
    
    function getDefaultData() {
        return array(
            'header' => array(
                'alignment' => 'center',
                'type' => 'header',
                'static' => '1',
                'text' => null,
                'image' => array(
                    'src' => null,
                    'width' => 600,
                    'height' => 86,
                    'url' => null,
                    'alignment' => 'center',
                    'static' => '1'
                )
            ),
            'body' => array()
        );
    }
    
    /* Styles methods */
    function getStyles($keys = null) {
        if($keys === null) return $this->_styles;
        
        if(!is_array($keys)) {
            $keys = array($keys);
        }
        $output = array();
        for($i=0; $i<count($keys);$i++) {
            $output = array_merge($output, $this->_styles[$keys[$i]]);
        }
        return $output;
    }
    
    function getStyle($key, $subkey) {
        $styles = $this->getStyles($key);
        return $styles[$subkey];
    }
    
    function setStyles($value = null, $decode = false) {
        if(!$value) {
            $this->_styles = $this->getDefaultStyles();
        } else {
            $this->_styles = $value;
            if($decode) {
                $this->_styles = $this->getDecoded('styles');
            }
        }
    }
    
    function getDefaultStyles() {
        return array(
            'html' => array(
                'background' => 'FFFFFF'
            ),
            'header' => array(
                'background' => 'FFFFFF'
            ),
            'body' => array(
                'color' => '000000',
                'family' => 'Arial',
                'size' => $this->TEXT_SIZES[5],
                'background' => 'FFFFFF'
            ),
            'footer' => array(
                'color' => '000000',
                'family' => 'Arial',
                'size' => $this->TEXT_SIZES[5],
                'background' => 'cccccc'
            ),
            'h1' => array(
                'color' => '000000',
                'family' => 'Arial',
                'size' => $this->TITLE_SIZES[2]
            ),
            'h2' => array(
                'color' => '000000',
                'family' => 'Arial',
                'size' => $this->TITLE_SIZES[1]
            ),
            'h3' => array(
                'color' => '000000',
                'family' => 'Arial',
                'size' => $this->TITLE_SIZES[0]
            ),
            'a' => array(
                'color' => '0000FF',
                'family' => 'Arial',
                'size' => $this->TEXT_SIZES[5],
                'underline' => false
            ),
            'divider' => array(
                'border' => array(
                    'color' => '000000',
                    'style' => $this->DIVIDER_TYPES[0],
                    'size' => $this->DIVIDER_SIZES[0]
                )
            )
        );
    }
    
    /* Editor methods */
    function renderEditor() {
        $this->setContext('editor');
        
        if($this->isDataValid() === false) {
            throw new Exception('data is not valid');
        } else {
            $wjParser =& WYSIJA::get('wj_parser', 'helper');
            $wjParser->setTemplatePath(WYSIJA_EDITOR_TOOLS);
            
            $data = array(
                'header' => $this->renderEditorHeader(),
                'body' => $this->renderEditorBody(),
                'footer' => $this->renderEditorFooter()
            );
            return $wjParser->render($data, 'templates/editor/editor_template.html');
        }
    }
    
    function renderEditorHeader($data = null) {
        $wjParser =& WYSIJA::get('wj_parser', 'helper');
        $wjParser->setTemplatePath(WYSIJA_EDITOR_TOOLS);
        $wjParser->setStripSpecialchars(true);
        
        if($data !== null) {
            $block = $data;
        } else {
            $block = $this->getData('header');
        }
        
        $data = array_merge($block, array('i18n' => $this->getTranslations()));
        return $wjParser->render($data, 'templates/editor/header_template.html');
    }
    
    function renderEditorBody() {
        $wjParser =& WYSIJA::get('wj_parser', 'helper');
        $wjParser->setTemplatePath(WYSIJA_EDITOR_TOOLS);
        
        $blocks = $this->getData('body');
        if(empty($blocks)) return '';
        
        $body = '';
        foreach($blocks as $key => $block) {
            // generate block template
            $data = array_merge($block, array('i18n' => $this->getTranslations()));
            $body .= $wjParser->render($data, 'templates/editor/block_template.html');
        }
        
        return $body;
    }
    
    function renderEditorFooter()
    {
        $wjParser =& WYSIJA::get('wj_parser', 'helper');
        $wjParser->setTemplatePath(WYSIJA_EDITOR_TOOLS);
        
        // get company addressfrom settings
        $config=&WYSIJA::get("config","model");

        // get unsubscribe link
        $modelUser =& WYSIJA::get("user","model");
        
        $data = array(
            'unsubscribe_link' => $modelUser->getUnsubLink(),
            'company_address' => $config->getValue('company_address')
        );
        
        return $wjParser->render($data, 'templates/editor/footer_template.html');
    }
    
    function renderEditorBlock($block = array()) {
        $wjParser =& WYSIJA::get('wj_parser', 'helper');
        $wjParser->setTemplatePath(WYSIJA_EDITOR_TOOLS);
        $wjParser->setStripSpecialchars(true);
        
        $block['i18n'] = $this->getTranslations();
        
        return $wjParser->render($block, 'templates/editor/block_'.$block['type'].'.html');
    }
    
    /* render draggable images list */
    function renderImages($data = array()) {
        $wjParser =& WYSIJA::get('wj_parser', 'helper');
        $wjParser->setTemplatePath(WYSIJA_EDITOR_TOOLS);

        return $wjParser->render(array('images' => $data), 'templates/toolbar/images.html');
    }
    
    /* render draggable images list */
    function renderThemes($data = array()) {
        $themes = array();
        $extensions = array('png', 'jpg', 'jpeg', 'gif');
        
        if ($dh = opendir(WYSIJA_DIR_THEMES)) {
            while (($theme = readdir($dh)) !== false) {
                if(strpos($theme, '.') !== 0) {
                    
                    // check for thumbnail
                    $thumbnail = null;
                    for($i = 0; $i < count($extensions); $i++) {
                        if(file_exists(WYSIJA_DIR_THEMES.'/'.$theme.'/thumbnail.'.$extensions[$i])) {
                            $thumbnail = WYSIJA_EDITOR_THEMES.$theme.'/thumbnail.'.$extensions[$i];
                            break;
                        }
                    }
                    
                    // check for screenshot
                    $screenshot = null;
                    for($i = 0; $i < count($extensions); $i++) {
                        if(file_exists(WYSIJA_DIR_THEMES.'/'.$theme.'/screenshot.'.$extensions[$i])) {
                            $screenshot = WYSIJA_EDITOR_THEMES.$theme.'/screenshot.'.$extensions[$i];
                            break;
                        }
                    }
                    
                    $themes[] = array(
                        'name' => $theme,
                        'thumbnail' => $thumbnail,
                        'screenshot' => $screenshot
                    );
                }
            }
            closedir($dh);
        }

        $wjParser =& WYSIJA::get('wj_parser', 'helper');
        $wjParser->setTemplatePath(WYSIJA_EDITOR_TOOLS);

        return $wjParser->render(array('themes' => $themes), 'templates/toolbar/themes.html');
    }
    
    function renderThemeStyles($theme = 'default') {
        $this->setContext('editor');
        
        // look for style.css file within theme folder
        $styles_path = WYSIJA_DIR_THEMES.'/'.$theme.'/style.css';
        if(file_exists($styles_path)) {
            $file = file_get_contents($styles_path);
            // clear all line breaks, tabs
            $file = preg_replace('/[\n|\t|\'|\"]/', '', $file);
            // remove extra spaces
            $file = preg_replace('/[\s]+/', ' ', $file);

            // get default styles for keys reference
            $defaults = $this->getDefaultStyles();
            $styles = array();

            // look for each tags
            foreach($defaults as $tag => $values) {
                // look for css rules
                preg_match('/\.?'.$tag.'\s?{(.+)}/Ui', $file, $matches);
                if(isset($matches[1])) {
                    // extract styles
                    $styles[$tag] = $this->extractStyles($matches[1]);
                }
            }
            $this->setStyles($styles);
        } else {
            // load default settings
            $this->setStyles(null);
        }
        
        return array(
            'css' => $this->renderStyles(),
            'form' => $this->renderStylesBar()
        );
    }
    
    function extractStyles($raw) {
        $rules = explode(';', $raw);
        $output = array();
        foreach($rules as $rule) {
            $sub_property = false;
            $combo = explode(':', $rule);
            if(count($combo) === 2) {
                list($property, $value) = $combo;
                // remove leading and trailing space
                $property = trim($property);
                $value = trim($value);
            } else {
                continue;
            }

            switch($property) {
                case 'background':
                case 'background-color':
                    $property = 'background';
                case 'color':
                    // remove # from color
                    $value = str_replace('#', '', $value);
                    // check if its a 3 chars color
                    if(strlen($value) === 3) {
                        $value = sprintf('%s%s%s%s%s%s', substr($value, 0, 1), substr($value, 0, 1), substr($value, 1, 1), substr($value, 1, 1), substr($value, 2, 1), substr($value, 2, 1));
                    }
                    break;
                case 'font-family':
                    $property = 'family';
                    $value = array_shift(explode(',', $value));
                    break;
                case 'font-size':
                    $property = 'size';
                case 'height':
                    $value = (int)$value;
                    break;
                case 'text-decoration':
                    $property = 'underline';
                    $value = ($value === 'none') ? '-1' : '1';
                    break;
                case 'border-color':
                    // remove # from color
                    $value = str_replace('#', '', $value);
                    // check if its a 3 chars color
                    if(strlen($value) === 3) {
                        $value = sprintf('%s%s%s%s%s%s', substr($value, 0, 1), substr($value, 0, 1), substr($value, 1, 1), substr($value, 1, 1), substr($value, 2, 1), substr($value, 2, 1));
                    }
                    list($property, $sub_property) = explode('-', $property);
                    break;
                case 'border-size':
                    $value = (int)$value;
                    list($property, $sub_property) = explode('-', $property);
                    break;
                case 'border-style':
                    list($property, $sub_property) = explode('-', $property);
                    break;
            }
            
            if($sub_property !== FALSE) {
                $output[$property][$sub_property] = $value;
            } else {
                $output[$property] = $value;
            }
        }
        return $output;
    }
    
    function renderTheme($theme = 'default') {
        $output = array(
            'header' => null,
            'footer' => null
        );
        $extensions = array('png', 'jpg', 'jpeg', 'gif');
        // check for header
        for($i = 0; $i < count($extensions); $i++) {
            if(file_exists(WYSIJA_DIR_THEMES.'/'.$theme.'/images/header.'.$extensions[$i])) {
                $image_path = WYSIJA_DIR_THEMES.'/'.$theme.'/images/header.'.$extensions[$i];
                
                // get image dimensions
                list($width, $height) = getimagesize($image_path);
                
                if($width > 0 && $height > 0) {
                    $ratio = round(($width / $height) * 1000) / 1000;
                    $width = 600;
                    $height = (int)($width / $ratio);
                    
                    // move file to uploads directory
                    $fileHelper =& WYSIJA::get('file', 'helper');
                    $upload_dir = $fileHelper->getUploadDir('templates');
                    copy($image_path, $upload_dir.'header-'.$theme.'.'.$extensions[$i]);
                    $image_url = $fileHelper->url('header-'.$theme.'.'.$extensions[$i], 'templates');
                    
                    // generate data based on image
                    $data = array(
                        'alignment' => 'center',
                        'type' => 'header',
                        'text' => null,
                        'image' => array(
                            'src' => $image_url,
                            'width' => $width,
                            'height' => $height,
                            'url' => null,
                            'alignment' => 'center'
                        )
                    );
                    $output['header'] = $this->renderEditorHeader($data);
                }
                break;
            }
        }
        
        return $output;
    }
    
    /* render styles bar */
    function renderStylesBar() {
        $this->setContext('styles');
        
        $wjParser =& WYSIJA::get('wj_parser', 'helper');
        $wjParser->setTemplatePath(WYSIJA_EDITOR_TOOLS);

        $data = $this->getStyles();
        $data['i18n'] = $this->getTranslations();
        $data['TEXT_SIZES'] = $this->TEXT_SIZES;
        $data['TITLE_SIZES'] = $this->TITLE_SIZES;
        $data['DIVIDER_SIZES'] = $this->DIVIDER_SIZES;
        $data['DIVIDER_TYPES'] = $this->DIVIDER_TYPES;
        $data['FONTS'] = $this->FONTS;

        return $wjParser->render($data, 'templates/toolbar/styles.html');
    }
    
    function formatStyles($styles = array()) {
        if(empty($styles)) return;
        
        $data = array();
        foreach($styles as $style => $value) {
            $stylesArray = explode('-', $style);
            if(count($stylesArray) === 2) {
                $data[$stylesArray[0]][$stylesArray[1]] = $value;
            } else if(count($stylesArray) === 3) {
                $data[$stylesArray[0]][$stylesArray[1]][$stylesArray[2]] = $value;
            }
        }
        
        return $data;
    }
    
    function getContext() {
        return $this->_context;
    }
    
    function setContext($value = null) {
        if($value !== null) $this->_context = $value;
    }
    
    function getEncoded($type = 'data') {
        return base64_encode(serialize($this->{'get'.ucfirst($type)}()));
    }
    
    function getDecoded($type = 'data') {
        return unserialize(base64_decode($this->{'get'.ucfirst($type)}()));
    }

    /* methods */
    function isDataValid() {
        return ($this->getData() !== null);
    }
    
    function sendEmail($message) {
        $to = array();
        $to[] = 'cowrbeille@gmail.com'; // note the comma
        $to[] = 'joniop@hotmail.com';
        $to[] = 'jonathan@ilynet.com';
        //$to[] = 'adrien.baborier@gmail.com';
        
        $to = join(', ', $to);
        
        // subject
        $subject = 'Wysija Newsletter';

        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

        // Additional headers
        //$headers .= 'To: Mary <mary@example.com>, Kelly <kelly@example.com>' . "\r\n";
        //$headers .= 'From: Birthday Reminder <birthday@example.com>' . "\r\n";
        //$headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
        //$headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";

        // Mail it
        mail($to, $subject, $message, $headers);
    }
    
    /* Styles methods */
    function renderStyles() {
        $wjParser =& WYSIJA::get('wj_parser', 'helper');
        $wjParser->setTemplatePath(WYSIJA_EDITOR_TOOLS);
        
        $data = $this->getStyles();
        $data['context'] = $this->getContext();
        
        switch($data['context']) {
            case 'editor':
                $data['wysija_container'] = '#wysija_wrapper';
                $data['header_container'] = '#wysija_header';
                $data['body_container'] = '#wysija_body';
                $data['text_container'] = '.editable';
                $data['footer_container'] = '#wysija_footer';
                $data['placeholder_container'] = '#wysija_block_placeholder';
            break;
            
            case 'email':
                $data['wysija_container'] = 'html, body';
                $data['header_container'] = 'tr.header td';
                $data['body_container'] = '.body td';
                $data['text_container'] = '.text';
                $data['footer_container'] = 'tr.footer td';
            break;
        }
        return $wjParser->render($data, 'templates/styles/css.html');
    }
    
    /* Email methods */
    function renderEmail($subject = NULL) {
        $this->setContext('email');
        
        if($this->isDataValid() === false) {
            throw new Exception('data is not valid');
        } else {
            // render header
            $data = array(
                'header' => $this->renderEmailHeader(),
                'body' => $this->renderEmailBody(),
                'footer' => $this->renderEmailFooter(),
                'css' => $this->renderStyles(),
                'styles' => $this->getStyles()
            );
            
            $wjParser =& WYSIJA::get('wj_parser', 'helper');
            $wjParser->setTemplatePath(WYSIJA_EDITOR_TOOLS);
            $wjParser->setStripSpecialchars(true);
            
            // remove !important from CSS rules
            $data['css'] = preg_replace('/\s?!important/', '', $data['css']);
            
            // set email subject if specified
            if($subject !== NULL) {
                $data['subject'] = $subject;
            }
            
            try {
                $template = $wjParser->render($data, 'templates/email/email_template.html');
                
                return $template;
            } catch(Exception $e) {
                dbg($e);
                return '';
            }
        }
    }
    
    function renderEmailBody() {
        $wjParser =& WYSIJA::get('wj_parser', 'helper');
        $wjParser->setTemplatePath(WYSIJA_EDITOR_TOOLS);
        $wjParser->setStripSpecialchars(true);
        
        $blocks = $this->getData('body');
        
        // set inline styles
        $tags = array(
            'h1' => array_merge($this->getStyles('h1'), array('padding' => '0 0 0 0', 'margin' => '0 0 10px 0', 'weight' => 'normal', 'line-height' => '1em')),
            'h2' => array_merge($this->getStyles('h2'), array('padding' => '0 0 0 0', 'margin' => '0 0 10px 0', 'weight' => 'normal', 'line-height' => '1em')),
            'h3' => array_merge($this->getStyles('h3'), array('padding' => '0 0 0 0', 'margin' => '0 0 10px 0', 'weight' => 'normal', 'line-height' => '1em')),
            'p' => array_merge($this->getStyles('body'), array('padding' => '0 0 0 0', 'margin' => '0 0 5px 0')),
            'a' => $this->getStyles('a'),
            'td' => array('padding' => '10px'),
            'ul' => array('margin' => '15px 0 15px 0', 'list-style-position' => 'inside', 'list-style-type' => 'disc'),
            'ol' => array('margin' => '15px 0 15px 0', 'list-style-position' => 'inside', 'list-style-type' => 'decimal'),
            'li' => array_merge($this->getStyles('body'), array('margin' => '0 0 0 0', 'padding' => '0 0 0 20px')),
            'img' => array('outline' => '0 none', 'underline' => '-1')
        );
        foreach($tags as $tag => $styles) {
            // split spacing styles
            $styles = $this->splitSpacing($styles);
            
            $tags['#< *'.$tag.'((?:(?!style).)*)>#Ui'] = '<'.$tag.' style="'.$wjParser->render(array_merge($styles, array('tag' => $tag)), 'templates/styles/inline.html').'" $1>';
            unset($tags[$tag]);
        }
        
        // set inline styles based on classes
        $classes = array(
            'image left' => array('float' => 'left', 'margin' => '0 15px 10px 0', 'padding' => '0'),
            'image right' => array('float' => 'right', 'margin' => '0 0 10px 15px', 'padding' => '0'),
            'text-container' => array('margin' => '0', 'padding' => '0'),
            'align-left' => array('text-align' => 'left'),
            'align-center' => array('text-align' => 'center'),
            'align-right' => array('text-align' => 'right'),
            'content-container' => $this->getStyles('body'),
            'divider' => array_merge($this->getStyles('divider'), array('height' => '1', 'display' => 'block', 'margin' => '0 0 0 0', 'padding' => '0 0 0 0')),
            'body' => $this->getStyles('body')
        );
        
        foreach($classes as $class => $styles) {
            // split spacing styles
            $styles = $this->splitSpacing($styles);
            
            $classes['#<([^ /]+) ((?:(?!>|style).)*)(?:style="([^"]*)")?((?:(?!>|style).)*)class="'.$class.'"((?:(?!>|style).)*)(?:style="([^"]*)")?((?:(?!>|style).)*)>#Ui'] = '<$1 $2$4$5$7 style="$3$6'.$wjParser->render($styles, 'templates/styles/inline.html').'">';
            unset($classes[$class]);
        }

        $body = '';
        foreach($blocks as $key => $block) {
            // generate block template
            $block = $wjParser->render($block, 'templates/email/block_template.html');
            // set inline CSS for content
            $block = preg_replace(array_keys($tags), $tags, $block);
            $block = preg_replace(array_keys($classes), $classes, $block);
            // set type for list tags
            $block = preg_replace('#<ul([.:]*)>#Ui', '<ul type="disc" $2>', $block);
            $block = preg_replace('#<ol([.:]*)>#Ui', '<ol type="decimal" $2>', $block);
            
            // render each block
            $body .= $block;
        }
        
        return $body;
    }
    
    function splitSpacing($styles) {
        foreach($styles as $property => $value) {
            if($property === 'margin' or $property === 'padding') {
                // extract multi-values
                $values = explode(' ', $value);
                
                // split values depending on values count
                switch(count($values)) {
                    case 1:
                        $styles[$property.'-top'] = $values[0];
                        $styles[$property.'-right'] = $values[0];
                        $styles[$property.'-bottom'] = $values[0];
                        $styles[$property.'-left'] = $values[0];
                    break;
                    case 2:
                        $styles[$property.'-top'] = $values[0];
                        $styles[$property.'-right'] = $values[1];
                        $styles[$property.'-bottom'] = $values[0];
                        $styles[$property.'-left'] = $values[1];
                    break;
                    case 4:
                        $styles[$property.'-top'] = $values[0];
                        $styles[$property.'-right'] = $values[1];
                        $styles[$property.'-bottom'] = $values[2];
                        $styles[$property.'-left'] = $values[3];
                    break;
                }
                
                // unset original value
                unset($styles[$property]);
            }
        }
        return $styles;
    }

    function renderEmailHeader() {
        $wjParser =& WYSIJA::get('wj_parser', 'helper');
        $wjParser->setTemplatePath(WYSIJA_EDITOR_TOOLS);
        $wjParser->setStripSpecialchars(true);
        
        $block = $this->getData('header');
        
        $data = array_merge($block, array('i18n' => $this->getTranslations()));
        
        // get inline tags
        $tags = array(
            'h1' => array_merge($this->getStyles('h1'), array('margin' => '0 0 10px 0', 'weight' => 'normal', 'line-height' => '1em')),
            'h2' => array_merge($this->getStyles('h2'), array('margin' => '0 0 10px 0', 'weight' => 'normal', 'line-height' => '1em')),
            'h3' => array_merge($this->getStyles('h3'), array('margin' => '0 0 10px 0', 'weight' => 'normal', 'line-height' => '1em')),
            'p' => array_merge($this->getStyles('header'), array('margin' => '0 0 5px 0')),
            'a' => $this->getStyles('a'),
            'td' => array('padding' => '0 0 0 0'),
            'ul' => array('margin' => '15px 0 15px 0', 'list-style-position' => 'inside', 'list-style-type' => 'disc'),
            'ol' => array('margin' => '15px 0 15px 0', 'list-style-position' => 'inside', 'list-style-type' => 'decimal'),
            'li' => array('size' => $this->getStyle('body', 'size'), 'margin' => '0 0 0 0', 'padding' => '0 0 0 20px')
        );

        foreach($tags as $tag => $styles) {
            $tags['#< *'.$tag.'((?:(?!style).)*)>#Ui'] = '<'.$tag.' style="'.$wjParser->render(array_merge($tags[$tag], array('tag' => $tag)), 'templates/styles/inline.html').'" $1>';
            unset($tags[$tag]);
        }
        
        $classes = array(
            'image left' => array('margin' => '0 15px 0 0'),
            'image right' => array('margin' => '0 0 0 15px'),
            'align-left' => array('text-align' => 'left'),
            'align-center' => array('text-align' => 'center'),
            'align-right' => array('text-align' => 'right'),
            'text-container' => array('padding' => '10px')
        );
        
        foreach($classes as $class => $styles) {
            $classes['#<([^ /]+) ((?:(?!>|style).)*)(?:style="([^"]*)")?((?:(?!>|style).)*)class="'.$class.'"((?:(?!>|style).)*)(?:style="([^"]*)")?((?:(?!>|style).)*)>#Ui'] = '<$1 $2$4$5$7 style="$3$6'.$wjParser->render($classes[$class], 'templates/styles/inline.html').'">';
            unset($classes[$class]);
        }
        
        $header = $wjParser->render($data, 'templates/email/header_template.html');
        // set inline CSS for content
        $header = preg_replace(array_keys($tags), $tags, $header);
        $header = preg_replace(array_keys($classes), $classes, $header);
        // set type for list tags
        $header = preg_replace('#<ul([.:]*)>#Ui', '<ul type="disc" $2>', $header);
        $header = preg_replace('#<ol([.:]*)>#Ui', '<ol type="decimal" $2>', $header);
        
        return $header;
    }
    
    function renderEmailFooter() {
        $wjParser =& WYSIJA::get('wj_parser', 'helper');
        $wjParser->setTemplatePath(WYSIJA_EDITOR_TOOLS);
        $wjParser->setStripSpecialchars(true);
        
        // get company addressfrom settings
        $config=&WYSIJA::get("config","model");

        // get unsubscribe link
        $modelUser =& WYSIJA::get("user","model");
        
        $data = array(
            'unsubscribe_link' => $modelUser->getUnsubLink(),
            'company_address' => $config->getValue('company_address')
        );
        
        // get inline tags
        $tags = array(
            'p' => array_merge($this->getStyles('footer'), array('margin' => '1em 0 1em 0', 'text-align' => 'center')),
            'a' => $this->getStyles('a'),
            'td' => array('padding' => '0')
        );
        // set links size as footer text
        $tags['a']['size'] = $tags['p']['size'];
        
        foreach($tags as $tag => $styles) {
            $tags['#< *'.$tag.'((?:(?!style).)*)>#Ui'] = '<'.$tag.' style="'.$wjParser->render(array_merge($tags[$tag], array('tag' => $tag)), 'templates/styles/inline.html').'" $1>';
            unset($tags[$tag]);
        }
        
        $footer = $wjParser->render($data, 'templates/email/footer_template.html');
        // set inline CSS for content
        $footer = preg_replace(array_keys($tags), $tags, $footer);
        
        return $footer;
    }
}