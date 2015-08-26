<?php 

/*

  SEO Plugin for Serendipity
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

class serendipity_event_seo extends serendipity_event
{
  function introspect(&$propbag)
  {
    global $serendipity;

    $propbag->add('name',         PLUGIN_EVENT_SEO_NAME);
    $propbag->add('description',  PLUGIN_EVENT_SEO_DESC);
    $propbag->add('stackable',    false);
    $propbag->add('groups',       array('FRONTEND_VIEWS'));
    $propbag->add('author',       'E Camden Fisher <fishnix@gmail.com>');
    $propbag->add('version',      '0.0.3');
    $propbag->add('requirements', array(
        'serendipity' => '1.5.0',
        'smarty'      => '2.6.7',
        'php'         => '5.1.0'
    ));

    $propbag->add('event_hooks',   array(
      'frontend_header' => true,
    ));

    $conf_array[] = 'enable_og_metadata';
    $conf_array[] = 'fb_publisher';
    $conf_array[] = 'fb_admins';
    $conf_array[] = 'enable_tw_metadata';
    $conf_array[] = 'twitter_domain';
    $conf_array[] = 'twitter_creator';
    $conf_array[] = 'enable_gp_metadata';
    $conf_array[] = 'google_publisher';
    $conf_array[] = 'enable_pe_metadata';
    $conf_array[] = 'enable_misc_metadata';

    $propbag->add('configuration', $conf_array);
  }

  function generate_content(&$title) {
    $title = $this->title;
  }

  function introspect_config_item($name, &$propbag) {
    switch($name) {
      case 'enable_og_metadata':
        $propbag->add('name',           PLUGIN_EVENT_SEO_OG_META_ON);
        $propbag->add('description',    PLUGIN_EVENT_SEO_OG_META_ON_DESC);
        $propbag->add('default',        true);
        $propbag->add('type',           'boolean');
        break;
      case 'fb_publisher':
        $propbag->add('name',           PLUGIN_EVENT_SEO_OG_PUBLISHER);
        $propbag->add('description',    PLUGIN_EVENT_SEO_OG_PUBLISHER_DESC);
        $propbag->add('default',        '');
        $propbag->add('type',           'string');
        break;
      case 'fb_admins':
        $propbag->add('name',           PLUGIN_EVENT_SEO_OG_ADMINS);
        $propbag->add('description',    PLUGIN_EVENT_SEO_OG_ADMINS_DESC);
        $propbag->add('default',        '');
        $propbag->add('type',           'string');
        break;
      case 'enable_tw_metadata':
        $propbag->add('name',           PLUGIN_EVENT_SEO_TW_META_ON);
        $propbag->add('description',    PLUGIN_EVENT_SEO_TW_META_ON_DESC);
        $propbag->add('default',        true);
        $propbag->add('type',           'boolean');
        break;
      case 'twitter_domain':
        $propbag->add('name',           PLUGIN_EVENT_SEO_TW_DOMAIN);
        $propbag->add('description',    PLUGIN_EVENT_SEO_TW_DOMAIN_DESC);
        $propbag->add('default',        '');
        $propbag->add('type',           'string');
        break;
      case 'twitter_creator':
        $propbag->add('name',           PLUGIN_EVENT_SEO_TW_CREATOR);
        $propbag->add('description',    PLUGIN_EVENT_SEO_TW_CREATOR_DESC);
        $propbag->add('default',        '');
        $propbag->add('type',           'string');
        break;
      case 'enable_gp_metadata':
        $propbag->add('name',           PLUGIN_EVENT_SEO_GP_META_ON);
        $propbag->add('description',    PLUGIN_EVENT_SEO_GP_META_ON_DESC);
        $propbag->add('default',        true);
        $propbag->add('type',           'boolean');
        break;
      case 'google_publisher':
        $propbag->add('name',           PLUGIN_EVENT_SEO_GP_PUBLISHER);
        $propbag->add('description',    PLUGIN_EVENT_SEO_GP_PUBLISHER_DESC);
        $propbag->add('default',        '');
        $propbag->add('type',           'string');
        break;
      case 'enable_pe_metadata':
        $propbag->add('name',           PLUGIN_EVENT_SEO_PE_META_ON);
        $propbag->add('description',    PLUGIN_EVENT_SEO_PE_META_ON_DESC);
        $propbag->add('default',        true);
        $propbag->add('type',           'boolean');
        break;
      case 'enable_misc_metadata':
        $propbag->add('name',           PLUGIN_EVENT_SEO_MISC_META_ON);
        $propbag->add('description',    PLUGIN_EVENT_SEO_MISC_META_ON_DESC);
        $propbag->add('default',        true);
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
//          echo '<!--';
//          echo print_r($GLOBALS);
//          echo '-->';
          if (!isset($GLOBALS['entry'][0])) {
            if ($this->get_config('enable_og_metadata')) {
              echo '<meta property="og:locale" content="en_US" />' . "\n";
              echo '<meta property="og:type" content="website" />' . "\n";
              echo '<meta property="og:title" content="' . $serendipity['blogTitle'] . '" />' . "\n";
              echo '<meta property="og:url" content="http' . ($_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']) . '" />' . "\n";
              echo '<meta property="og:site_name" content="' . $serendipity['blogTitle'] . '" />' . "\n";
              echo '<meta property="article:publisher" content="' . htmlspecialchars($this->get_config('fb_publisher')) . '" />' . "\n";
              echo '<meta property="fb:admins" content="' . $this->get_config('fb_admins') . '" />' . "\n";
            }

            if ($this->get_config('enable_tw_metadata')) {
              echo '<meta name="twitter:card" content="summary_large_image"/>' . "\n";
              echo '<meta name="twitter:title" content="' . $serendipity['blogTitle'] . '"/>' . "\n";
              echo '<meta name="twitter:site" content="' . $serendipity['blogTitle'] . '"/>' . "\n";
              echo '<meta name="twitter:description" content="' . $serendipity['blogDescription'] . '"/>' . "\n";
              echo '<meta name="twitter:domain" content="' . htmlspecialchars($this->get_config('twitter_domain')) . '"/>' . "\n";
              echo '<meta name="twitter:creator" content="' . htmlspecialchars($this->get_config('twitter_creator')) . '"/>' . "\n";
            }

            if ($this->get_config('enable_gp_metadata')) {
              echo '<link rel="canonical" href="http' . ($_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']) . '" />' . "\n";
              echo '<link rel="publisher" href="' . htmlspecialchars($this->get_config('google_publisher')) . '"/>' . "\n";
            }

            if ($this->get_config('enable_misc_metadata')) {
              /* NA */
            }

            return true;
          }

          echo '<!-- serendipity_event_seo -->' . "\n";
          if ($this->get_config('enable_og_metadata')) {
            // Borrowed from serendipity_event_facebook
            // Taken from: http://developers.facebook.com/docs/opengraph/
            $title = htmlspecialchars(trim(strip_tags($GLOBALS['entry'][0]['title'])));
            $desc = str_replace("\n", " ", trim(substr(strip_tags($GLOBALS['entry'][0]['body']), 0, 200) . '...'));
            echo '<meta property="og:locale" content="en_US" />' . "\n";
            echo '<meta property="og:title" content="' . $title . '" />' . "\n";
            echo '<meta property="og:description" content="' . $desc . '" />' . "\n";
            echo '<meta property="article:publisher" content="' . htmlspecialchars($this->get_config('fb_publisher')) . '" />' . "\n";

            echo '<meta property="og:type" content="article" />' . "\n";
            echo '<meta property="og:site_name" content="' . $serendipity['blogTitle'] . '" />' . "\n";

            echo '<meta property="og:url" content="http' . ($_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']) . '" />' . "\n";

            if (preg_match('@<img.*src=["\'](.+)["\']@imsU', $GLOBALS['entry'][0]['body'] . $GLOBALS['entry'][0]['extended'], $im)) {
                echo '<meta property="og:image" content="http' . ($_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $im[1] . '" />' . "\n";
            }

            echo '<meta property="fb:admins" content="' . $this->get_config('fb_admins') . '" />' . "\n";
            echo '<meta property="og:updated_time" content="' . date('c',$GLOBALS['entry'][0]['last_modified']) .'" />' . "\n";
            /* TODO: <meta property="article:section" content="SECTION NAME" /> */
          }

          if ($this->get_config('enable_tw_metadata')) {
            $title = htmlspecialchars(trim(strip_tags($GLOBALS['entry'][0]['title'])));
            $desc = str_replace("\n", " ", trim(substr(strip_tags($GLOBALS['entry'][0]['body']), 0, 200) . '...'));
            echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
            echo '<meta name="twitter:title" content="' . $title . '" />' . "\n";
            echo '<meta name="twitter:description" content="' . $desc . '" />' . "\n";
            echo '<meta name="twitter:site"' . $serendipity['blogTitle'] . '" />' . "\n";
            echo '<meta name="twitter:domain" content="' . htmlspecialchars($this->get_config('twitter_domain')) . '"/>' . "\n";
            echo '<meta name="twitter:creator" content="' . htmlspecialchars($this->get_config('twitter_creator')) . '"/>' . "\n";

            if (preg_match('@<img.*src=["\'](.+)["\']@imsU', $GLOBALS['entry'][0]['body'] . $GLOBALS['entry'][0]['extended'], $im)) {
              echo '<meta name="twitter:image:src" content="http' . ($_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $im[1] . '" />' . "\n";
            }
          }

          if ($this->get_config('enable_gp_metadata')) {
            echo '<link rel="canonical" href="http' . ($_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER['REQUEST_URI']) . '" />' . "\n";
            echo '<link rel="publisher" href="' . htmlspecialchars($this->get_config('google_publisher')) . '" />' . "\n";

          }

          if ($this->get_config('enable_pe_metadata')) {
            $title = htmlspecialchars(trim(strip_tags($GLOBALS['entry'][0]['title'])));
            $desc = str_replace("\n", " ", trim(substr(strip_tags($GLOBALS['entry'][0]['body']), 0, 200) . '...'));
            echo '<meta name="pubexchange:headline" content="' . $title . '" />' . "\n";
            echo '<meta name="pubexchange:description" content="' . $desc . '" />' . "\n";
            if (preg_match('@<img.*src=["\'](.+)["\']@imsU', $GLOBALS['entry'][0]['body'] . $GLOBALS['entry'][0]['extended'], $im)) {
              $imagefile = pathinfo($im[1]);
              $thumbnail = $imagefile['dirname'] . '/' . $imagefile['filename'] . '.serendipityThumb' . '.' . $imagefile['extension'];
              echo '<meta name="pubexchange:image" content="http' . ($_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $thumbnail . '" />' . "\n";
            }
          }

          if ($this->get_config('enable_misc_metadata')) {
            echo '<meta property="article:published_time" content="' . date('c', $GLOBALS['entry'][0]['timestamp']) . '" />' . "\n";
            echo '<meta property="article:modified_time" content="' . date('c', $GLOBALS['entry'][0]['last_modified']) . '" />' . "\n";
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