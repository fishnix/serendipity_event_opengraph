<?php 

/*

    Opengraph Plugin for Serendipity
    E. Camden Fisher <fishnix@gmail.com>
    
*/

if (IN_serendipity != true) {
    die ("Don't hack!"); 
}
    
// $time_start = microtime(true);

// Probe for a language include with constants. Still include defines later on, if some constants were missing
$probelang = dirname(__FILE__) . '/' . $serendipity['charset'] . 'lang_' . $serendipity['lang'] . '.inc.php';

if (file_exists($probelang)) {
    include $probelang;
}

include_once dirname(__FILE__) . '/lang_en.inc.php';

class serendipity_event_opengraph extends serendipity_event
{
    function introspect(&$propbag)
    {
        global $serendipity;

        $propbag->add('name',         PLUGIN_EVENT_OG_NAME);
        $propbag->add('description',  PLUGIN_EVENT_OG_DESC);
        $propbag->add('stackable',    false);
        $propbag->add('groups',       array('FRONTEND_VIEWS'));
        $propbag->add('author',       'E Camden Fisher <fish@fishnix.net>');
        $propbag->add('version',      '0.0.2');
        $propbag->add('requirements', array(
            'serendipity' => '1.5.0',
            'smarty'      => '2.6.7',
            'php'         => '5.2.0'
        ));
            
      $propbag->add('event_hooks',   array(
          'frontend_header' => true,
        ));

        $conf_array[] = 'enable_og_metadata';

        $propbag->add('configuration', $conf_array);
    }

    function generate_content(&$title) {
      $title = $this->title;
    }

    function introspect_config_item($name, &$propbag) {
      switch($name) {
        case 'enable_og_metadata':
          $propbag->add('name',           PLUGIN_EVENT_OG_PROP_META_ON);
          $propbag->add('description',    PLUGIN_EVENT_OG_PROP_META_ON_DESC);
          $propbag->add('default',        'true');
          $propbag->add('type',           'boolean');
        break;
        default:
          return false;
        break;
        
      }
      
      return true;
    }


    function event_hook($event, &$bag, &$eventData) {
        global $serendipity;
        
        $hooks = &$bag->get('event_hooks');
        
        if (isset($hooks[$event])) {
          switch($event) {
            case 'frontend_header':
              if (!isset($GLOBALS['entry'][0])) return true;

              echo '<!-- ECF ' . $this->get_config('enable_og_metadata') . '-->';
              // foreach ($this->get_config as $key => $value) {
              //   echo '<!-- ' . $key . ' : '. $value . ' -->' . "\n";
              // }

              if ($this->get_config('enable_og_metadata')) {
                // Borrowed from serendipity_event_facebook
                // Taken from: http://developers.facebook.com/docs/opengraph/
                echo '<!-- serendipity_event_opengraph -->' . "\n";
                echo '<meta property="og:title" content="' . htmlspecialchars($GLOBALS['entry'][0]['title']) . '" />' . "\n";
                echo '<meta property="og:description" content="' . substr(strip_tags($GLOBALS['entry'][0]['body']), 0, 200) . '..." />' . "\n";

                echo '<meta property="og:type" content="article" />' . "\n";
                echo '<meta property="og:site_name" content="' . $serendipity['blogTitle'] . '" />' . "\n";

                echo '<meta property="og:url" content="http' . ($_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']) . '" />' . "\n";
                
                if (preg_match('@<img.*src=["\'](.+)["\']@imsU', $GLOBALS['entry'][0]['body'] . $GLOBALS['entry'][0]['extended'], $im)) {
                    echo '<meta property="og:image" content="' . $im[1] . '" />' . "\n";
                }
              }
              return true;
            break;
            default:
              return false;
            } 
        } else {
        return false;
      }
    }
}

?>