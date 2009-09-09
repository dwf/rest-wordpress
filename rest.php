<?php
/*
Plugin Name: reStructuredText
Version: 1.2
Plugin URI: https://launchpad.net/rest-wordpress
Description: This is a simple wrapper for <a href="http://docutils.sf.net/">Docutils' reStructuredText</a>, also known as reST. If you use this plugin you should disable all other markup plugins. Be sure to turn off the "WordPress should correct invalidly nested XHTML automatically" option in your Writing options.
Author: Matthew Scott
Author URI: http://goldenspud.com/
*/

// Set this to the prefix of your docutils installation.
$prefix = "/home/dwf/sw";

// Set this to the path of rst2html.py
$rst2html = "$prefix/bin/rst2html-highlight.py";

// Set this to a directory writeable by your web server to store cache
// versions of rendered reStructuredText.
$cachedir = "/tmp/dwf_rest_cache";

// Set this to true to use pipes instead of temporary files.
$usepipes = false;

// Set this to the location to store temporary files if $usepipes is
// not set to true.
$tmpdir = "/tmp/rest_render";

// Change this if you'd rather use a non-standard mode-line for recognizing
// reStructuredText formatting.  (Not recommended).
$modeline = "-*- mode: rst -*-";

// Change these to the options you prefer based on your personal preferences.
// See the rst2html man page for detailed information on available options.
//   Example: These are the options used at http://goldenspud.com/:
$rst2html_options = ''
    . '--no-toc-backlinks '
    . '--no-doc-title '
    . '--no-generator '
    . '--no-source-link '
    . '--no-footnote-backlinks '
    . '--initial-header-level=2 '
    ;
//   Example: These are the options used at http://pupeno.com/:
/*
$rst2html_options = ''
    . '--no-generator '
    . '--no-source-link '
    . '--rfc-references '
    . '--no-doc-title '
    . '--initial-header-level=2 '
    . '--footnote-references="superscript" '
    ;
*/


/**
 * Create a directory structure recursively
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.0
 * @param       string   $pathname    The directory structure to create
 * @return      bool     Returns TRUE on success, FALSE on failure
 */

function mkdirr($pathname, $mode = null)
{
  // Check if directory already exists
  if (is_dir($pathname) || empty($pathname)) {
    return true;
  }

  // Ensure a file does not already exist with the same name
  if (is_file($pathname)) {
    trigger_error('mkdirr() File exists', E_USER_WARNING);
    return false;
  }

  // Crawl up the directory tree
  $next_pathname = substr($pathname, 0, strrpos($pathname, DIRECTORY_SEPARATOR));
  if (mkdirr($next_pathname, $mode)) {
    if (!file_exists($pathname)) {
      return mkdir($pathname, $mode);
    }
  }

  return false;
}


/**
 * Convert reStructuredText markup to HTML.
 *
 * @param   string  $text   The text in reStructuredText format.
 * @return  string  Returns reStructuredText rendered as HTML.
 */
function reST($text) {
  global $rst2html;
  global $cachedir;
  global $usepipes;
  global $tmpdir;

  // Look for cached rendering and use it if available.
  $hash = md5($text);
  mkdirr($cachedir, 0700);
  $cachefile = "$cachedir/$hash.html";
  if (file_exists($cachefile)) {
    $cached = fopen($cachefile, "r");
    if ($cached) {
        $rval = '';
        while (!feof($cached)) {
          $rval .= fgets($cached);
        }
        fclose($cached);
        return $rval;
    }
  }

  // Look for mode-line.  If it exists, use reST.  Otherwise, use wpautop.
  $pos = strpos($text, "-*- mode: rst -*-");
  if ($pos === false) {
    // No modeline.
    $rval = wpautop($text);
  } else {
    // rst modeline found.

    // Scan text for more tags and turn them into reST.
    $pattern = '<a href="(.*)">(.*)</a>';
    $replacement = "\n\n`\\2 <\\1>`__\n\n";
    $text = ereg_replace($pattern, $replacement, $text);

    // Scan text for more target tags and comment them out.
    $pattern = '<a id="more-([0123456789]+)"></a>';
    $replacement = '.. <a id="more-\1"></a>';
    $text = ereg_replace($pattern, $replacement, $text);

    // Scan text for more target tags and comment them out.
    $pattern = '<span id="more-([0123456789]+)"></span>';
    $replacement = '.. <span id="more-\1"></span>';
    $text = ereg_replace($pattern, $replacement, $text);

    // TODO: Handle nextpage tags.

    if ($usepipes === true) {
      $descriptorspec = array(0 => array("pipe", "r"),
                              1 => array("pipe", "w"),
                              );
      $execstr = $rst2html . ' ' . $rst2html_options;
     	putenv("LD_LIBRARY_PATH=/home/dwf/sw/lib"); 
	$process = proc_open($execstr, $descriptorspec, $pipes);
      if (!is_resource($process)) {
        return "ERROR";
      }
      $txtfile = $pipes[0];
      fwrite($txtfile, $text);
      fflush($txtfile);
      fclose($txtfile);
      $rst = '';
      while (!feof($pipes[1])) {
        $rst .= fgets($pipes[1]);
      }
      fclose($pipes[1]);
      $rval = proc_close($process);
    } else {
      mkdirr($tmpdir, 0700);
      $filename = $tmpdir . '/' . rand(1,16384) . '-rest.txt';
      $txtfile = fopen($filename, 'w');
      fwrite($txtfile, $text);
      fclose($txtfile);
      putenv("LD_LIBRARY_PATH=/home/dwf/sw/lib");
	$execstr = $rst2html . ' ' . $rst2html_options . ' ' . $filename;
      $rst = shell_exec($execstr);
      unlink($filename);
    }

    // Get rid of wrapping body tags.
    $rststart = strpos($rst, '<body>') + strlen('<body>');
    $rststop = strpos($rst, '</body>');
    $rst = substr($rst, $rststart, $rststop - $rststart);

    // TODO: Only use <div id="content" ...> node?

    // Uncomment more target tags.
    $pattern = '<!-- <a id="more-([0123456789]+)"></a> -->';
    $replacement = '<a id="more-\1"></a>';
    $rst = ereg_replace($pattern, $replacement, $rst);

    // Uncomment more target tags.
    $pattern = '<!-- <span id="more-([0123456789]+)"></span> -->';
    $replacement = '<span id="more-\1"></span>';
    $rst = ereg_replace($pattern, $replacement, $rst);

    $rval = $rst;
  }

  // Save rendered HTML into a cache file to speed up future access.
  $cached = fopen($cachefile, "w");
  if ($cached) {
    fwrite($cached, $rval);
    fflush($cached);
    fclose($cached);
  }
  return $rval;
}


// Modify filter chains to use reST.

remove_filter('the_content', 'wpautop');
remove_filter('the_excerpt', 'wpautop');

remove_filter('the_content', 'wptexturize');
remove_filter('the_excerpt', 'wptexturize');

add_filter('the_content', 'reST');
add_filter('the_excerpt', 'reST');

?>
