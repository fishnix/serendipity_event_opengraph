<?php 

/*

  SEO Plugin for Serendipity
  E. Camden Fisher <fishnix@gmail.com>

*/

if (IN_serendipity != true) {
  die ("Don't hack!");
}

@define('PLUGIN_EVENT_SEO_VERSION', '0.0.5');

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
    $propbag->add('version',      PLUGIN_EVENT_SEO_VERSION);
    $propbag->add('requirements', array(
        'serendipity' => '1.5.0',
        'smarty'      => '2.6.7',
        'php'         => '5.1.0'
    ));

    $propbag->add('event_hooks',   array(
      'frontend_header' => true,
    ));

    $conf_array[] = 'enable_og_metadata';
    $conf_array[] = 'fb_app_id';
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
      case 'fb_app_id':
        $propbag->add('name',           PLUGIN_EVENT_SEO_OG_APP_ID);
        $propbag->add('description',    PLUGIN_EVENT_SEO_OG_APP_ID_DESC);
        $propbag->add('default',        '');
        $propbag->add('type',           'string');
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

  function event_hook($event, &$bag, &$eventData)
  {
    global $serendipity;

    $hooks = &$bag->get('event_hooks');

    if (isset($hooks[$event])) {
      switch ($event) {
        case 'frontend_header':
          $time_start = microtime(true);

          echo '<!-- serendipity_event_seo ' . PLUGIN_EVENT_SEO_VERSION . ' -->' . "\n";

          $site = serendipity_specialchars($serendipity['blogTitle']);
          $url = serendipity_specialchars(rtrim($serendipity['baseURL'], '/') . $_SERVER['REQUEST_URI']);

          // If this is an individual entry....
          if (isset($GLOBALS['entry'][0])) {
            // set the title to the entry title
            $title = serendipity_specialchars(trim(strip_tags($GLOBALS['entry'][0]['title'])));

            $desc = $GLOBALS['entry'][0]['properties']['meta_description'];
            if (empty($desc)) {
              $desc = str_replace("\n", " ", trim(substr(strip_tags($GLOBALS['entry'][0]['body']), 0, 200) . '...'));
            }
            $desc = serendipity_specialchars($desc);

            if (preg_match('@<img.*src=["\'](.+)["\']@imsU', $GLOBALS['entry'][0]['body'] . $GLOBALS['entry'][0]['extended'], $im)) {
              $image = serendipity_specialchars(rtrim($serendipity['baseURL'], '/') . $im[1]);
            } else {
              $image = false;
            }
          } else {
            // set the title to the blog title
            $title = serendipity_specialchars($serendipity['blogTitle']);
            $desc = serendipity_specialchars($serendipity['blogDescription']);
          }

          if ($this->get_config('enable_og_metadata')) {
            $this->generate_og_metadata($title, $desc, $site, $url, $image, isset($GLOBALS['entry'][0]));
          }

          if ($this->get_config('enable_tw_metadata')) {
            $this->generate_tw_metadata($title, $desc, $site, $image, isset($GLOBALS['entry'][0]));
          }

          if ($this->get_config('enable_gp_metadata')) {
            $this->generate_gp_metadata($url);
          }

          if ($this->get_config('enable_misc_metadata')) {
            $this->generate_misc_metadata(isset($GLOBALS['entry'][0]));
          }

          if ($this->get_config('enable_pe_metadata')) {
            $this->generate_pe_metadata($title, $desc, $image, isset($GLOBALS['entry'][0]));
          }

          $time_end = microtime(true);
          $time = $time_end - $time_start;

          echo '<!-- serendipity_event_seo :::TIMING::: ' . $time . ' seconds -->' . "\n";

          return true;
          break;
        default:
          return false;
      }
    } else {
      return false;
    }
  }

  function generate_og_metadata(&$title, &$desc, &$site, &$url, &$image, $entry_page = false) {
    // Content borrowed from serendipity_event_facebook,
    // http://developers.facebook.com/docs/opengraph/
    echo '<meta property="fb:app_id" content="' . $this->get_config('fb_app_id') . '" />' . "\n";
    echo '<meta property="fb:admins" content="' . $this->get_config('fb_admins') . '" />' . "\n";
    echo '<meta property="og:locale" content="en_US" />' . "\n";
    echo '<meta property="og:title" content="' . $title . '" />' . "\n";
    echo '<meta property="og:description" content="' . $desc . '" />' . "\n";
    echo '<meta property="og:url" content="' . $url . '" />' . "\n";
    echo '<meta property="article:publisher" content="' . serendipity_specialchars($this->get_config('fb_publisher')) . '" />' . "\n";
    echo '<meta property="og:site_name" content="' . $site . '" />' . "\n";

    if ($entry_page) {
      echo '<meta property="og:type" content="article" />' . "\n";
      echo '<meta property="og:updated_time" content="' . date('c', $GLOBALS['entry'][0]['last_modified']) . '" />' . "\n";
      /* TODO: <meta property="article:section" content="SECTION NAME" /> */

      if ($image) {
        echo '<meta property="og:image" content="' . $image . '" />' . "\n";
      }
    } else {
      echo '<meta property="og:type" content="website" />' . "\n";
    }
  }

  function generate_tw_metadata(&$title, &$desc, &$site, $entry_page = false) {
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . $title . '" />' . "\n";
    echo '<meta name="twitter:description" content="' . $desc . '" />' . "\n";
    echo '<meta name="twitter:site"' . $site . '" />' . "\n";
    echo '<meta name="twitter:domain" content="' . serendipity_specialchars($this->get_config('twitter_domain')) . '"/>' . "\n";
    echo '<meta name="twitter:creator" content="' . serendipity_specialchars($this->get_config('twitter_creator')) . '"/>' . "\n";

    if ($image) {
      echo '<meta property="twitter:image:src" content="' . $image . '" />' . "\n";
    }
  }

  function generate_gp_metadata($url) {
    echo '<link rel="canonical" href="' . $url . '" />' . "\n";
    echo '<link rel="publisher" href="' . serendipity_specialchars($this->get_config('google_publisher')) . '" />' . "\n";
  }

  function generate_misc_metadata($entry_page = false) {
    if ($entry_page) {
      echo '<meta property="article:published_time" content="' . date('c', $GLOBALS['entry'][0]['timestamp']) . '" />' . "\n";
      echo '<meta property="article:modified_time" content="' . date('c', $GLOBALS['entry'][0]['last_modified']) . '" />' . "\n";
    } else {
      /* NA */
    }
  }

  function generate_pe_metadata(&$title, &$desc, &$image, $entry_page = false) {
    if ($entry_page) {
      echo '<meta name="pubexchange:headline" content="' . $title . '" />' . "\n";
      echo '<meta name="pubexchange:description" content="' . $desc . '" />' . "\n";
      if ($image) {
        $imagefile = pathinfo($image);
        $thumbnail = $imagefile['dirname'] . '/' . $imagefile['filename'] . '.serendipityThumb' . '.' . $imagefile['extension'];
        echo '<meta name="pubexchange:image" content="' . $thumbnail . '" />' . "\n";
      }
    } else {
      /* NA */
    }
  }
}
?>
