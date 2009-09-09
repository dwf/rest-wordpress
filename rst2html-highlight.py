#!/home/dwf/sw/bin/python
"""
A front end to docutils, producing HTML with syntax colouring using pygments

Generates (X)HTML documents from standalone reStructuredText sources. Uses
`pygments` to parse and mark up the content of `.. code-block::` directives.
Needs an adapted stylesheet
"""

try:
    import locale
    locale.setlocale(locale.LC_ALL, '')
except:
    pass

from docutils.core import publish_cmdline, default_description

import pygments_code_block_directive

description = __doc__ + default_description
publish_cmdline(writer_name='html', description=description)


