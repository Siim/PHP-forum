<?
/* Error reporting */
error_reporting(E_ALL);

/* Setup controllers locations */
$controllers = array(
    'forum' => 'controllers/Forum.php'
  , 'install' => 'controllers/Install.php'
  , 'user' => 'controllers/User.php'
);

/* Setup some global variables */
define('WWW_ROOT',$_SERVER['DOCUMENT_ROOT']);
$fpath = substr(getcwd(),strlen(WWW_ROOT),strlen(getcwd()));
define('PATH',$fpath . "/");
define('ROOT',WWW_ROOT . PATH);
define('VIEW_DIR', ROOT. '/view/');
define('VIEW_CACHE_DIR', ROOT. '/view/cache/');
define('LAYOUT_FILE', VIEW_DIR .'layout.haml');
define('ERROR_404_PAGE','error404.haml');


/* Setup mongo database */
$m = new Mongo();
$database = $m->cms;

