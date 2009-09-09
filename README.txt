.. -*- mode: rst -*-

============================================
 reST-wordpress: Combining two great tastes
============================================

Website: https://launchpad.net/rest-wordpress


Why combine WordPress and reStructuredText?
===========================================

`WordPress <http://wordpress.org/>`__ is practically a de facto
standard of Open Source blogging tools.  It's a mature platform backed
by an intelligent and active community.

`reStructuredText <http://docutils.sourceforge.net/rst.html>`__ (or
*reST* for short) is practically a de facto standard of the Python
document-writing community.  It's a mature text markup format backed
by an intelligent and active community.


Prerequisites
=============

* `WordPress <http://wordpress.org/>`__, version 2.3 series.

* `Docutils <http://docutils.sourceforge.net/>`__, version 0.4 or
  higher.


Installation
============

1. **Copy the plugin.**

   Copy the ``rest.php`` file to the ``wp-content/plugins`` directory
   underneath the root if your WordPress installation.

2. **Configure plugin options.**

   Edit the ``rest.php`` file.

   Change the ``$prefix``, ``$rst2html``, ``$cachedir``,
   ``$usepipes``, and ``$tmpdir`` paths as necessary based on the
   in-line comments documenting each option.

   Save the file.

3. **Configure WordPress options.**

   Navigate to **Options**, then **Writing**.

   Turn **off** the option titled **WordPress should correct invalidly
   nested XHTML automatically**.

   Click on **Update Options** to save this change.

   Navigate to **Users**, then **Your Profile**.

   Turn **off** the option titled **Use the visual editor when
   writing**.

4. **Activate the plugin.**

   Log into your WordPress ``wp-admin`` URL.

   Navigate to **Plugins**.

   Click **Activate** next to the **reStructuredText** plugin.


Using reStructuredText
======================

To write a post or page using reStructuredText, simply include this
*mode line* somewhere in your body text, usually as the first line,
then publish as normal::

  .. -*- mode: rst -*-

The mode line is interpreted in the following ways:

* The *reST-wordpress* plugin, as well as many text editors, see the
  ``-*- mode: rst -*-`` part as an indicator that the text is in
  reStructuredText format.

* The reStructuredText format treats lines beginning with ``..`` as
  comment lines.

"More" links
------------

Add a *more* link to your post by adding this to your text::

  .. <!--more-->

The plugin goes through a few hackish hoops to get this to work, but
the end result is that you get to comment it out in the
reStructuredText source, and have it play along with WordPress.


Limitations
===========

* You cannot post comments in reStructuredText format.

* Does not play nice with other markup plugins.  Please disable all
  other markup plugins if you've installed any.

* One-time configuration is stored in the plugin itself, rather than
  via a page accessed via ``wp-admin``.

* The WordPress visual editor does not support reStructuredText.  On
  the other hand, you are probably installing this plugin because you
  would prefer to use plain text. :)

* *More* links look less than ideal in some templates, mainly the
  index page template of the default WordPress template.


Contributions
=============

- Pupeno provided improvements in making the plugin template-agnostic
  around 2007-01-17 (`post
  <http://pupeno.com/2007/01/17/rst-on-wp/>`__).

- Bob Ippolito provided improvements to the caching system around
  2004-12-29 (`archived post
  <http://web.archive.org/web/20050311223030/http://pythonmac.org/bob/archives/2004/12/29/restructuredtext-and-wordpress/>`__).

- Matthew Scott converted the concept to a WordPress plugin around
  2004-12-23 (`archived post
  <http://web.archive.org/web/20050426215304/http://goldenspud.com/webrog/archives/2004/12/23/rest-easy-with-wordpress/>`__).

- Inspired by a 2004-11-23 post (`archived
  <http://web.archive.org/web/20060129233318/http://fantasy.geographic.net/~jspade/archives/restructuredtext-on-drupal/>`__)
  about using reStructuredText with `Drupal <http://drupal.org>`__.
