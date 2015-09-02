<?php
/*
* @package      AJAX Infinity
* @copyright    Copyright (C) 2014-2015 Emir Sakic, http://www.sakic.net. All rights reserved.
* @license      GNU/GPL, see LICENSE.TXT
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
* 
* This header must not be removed. Additional contributions/changes
* may be added to this header as long as no information is deleted.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin');

class plgSystemAJAXInfinity extends JPlugin
{
    function onBeforeRender() {
        
        $app = JFactory::getApplication();

        if ($app->isAdmin()) {
            return;
        }

        $option = $app->input->get('option');
        $view = $app->input->get('view');
        $layout = $app->input->get('layout');
        $ajax = $app->input->get('ajax');

        $blog_selector = $this->params->get('blog_selector', '.blog');
        $featured_selector = $this->params->get('featured_selector', '.blog-featured');

        if ($option=='com_content' && $view=='category' && $layout=='blog') {
            $this->_prepareOutput($blog_selector, $ajax);
            if (!$ajax) {
                $this->_infiniteScroll($blog_selector);
            }
        } else if ($option=='com_content' && $view=='featured') {
            $this->_prepareOutput($featured_selector, $ajax);
            if (!$ajax) {
                $this->_infiniteScroll($featured_selector);
            }
        }
    }

    private function _infiniteScroll($container_selector) {

        $padding = (int) $this->params->get('padding', 0);

        $autotrigger = $this->params->get('autotrigger', 1) ? 'true' : 'false';
        $autotriggeruntil = $autotrigger && (int) $this->params->get('autotriggeruntil') > 0 ? (int) $this->params->get('autotriggeruntil') : 'false';

        JHtml::_('jquery.framework');
        JHTML::script('plugins/system/ajaxinfinity/jquery.query-object.min.js');
        JHTML::script('plugins/system/ajaxinfinity/script.js');

        $document = JFactory::getDocument();
        $document->addScriptDeclaration('
            jQuery(document).ready(function($) {
                $("'.$container_selector.'").hashRedirect({
                    start: $.query.get("start"),
                    containerSelector: "'.$container_selector.'",
                    loadingHtml: \'<div class="ai-loading clearfix"><img src="plugins/system/ajaxinfinity/loading.gif" /></div>\'
                });
                var aligned = false;
                $("'.$container_selector.'").jscroll({
                    padding: '.$padding.',
                    nextSelector: "a.ai-next",
                    loadingHtml: \'<div class="ai-loading clearfix"><img src="plugins/system/ajaxinfinity/loading.gif" /></div>\',
                    autoTrigger: '.$autotrigger.',
                    autoTriggerUntil: '.$autotriggeruntil.',
                    callback: function() {
                        if (!aligned) {
                            //$(this).goToHash();
                            aligned = true;
                        }
                        var text = $(".ai-results").text();
                        if (text) {
                            var matches = text.match(/ ([0-9]+) - /);
                            if (matches) {
                                var start = parseInt(matches[1]);
                                var cnt = $(".items-row").length;
                                var to = start + cnt - 1;
                                text = text.replace(/ - [0-9]+ /, " - " + to + " ");
                                $(".ai-results").text(text);
                            }
                        }
                    }
                });
            });
        ');

        $document->addStyleDeclaration('
            .ai-loading {text-align: center}
            .ai-next {display: block; width: 100%; text-align: center}
        ');
    }

    private function _prepareOutput($container_selector, $ajax) {
        $doc = JFactory::getDocument();
        $buffer = $doc->getBuffer('component');

        if ($buffer) {
            $dom = new DOMDocument();

            libxml_use_internal_errors(true);
            $dom->loadHTML(mb_convert_encoding($buffer, 'HTML-ENTITIES', 'UTF-8'));
            libxml_use_internal_errors(false);
            $xpath = new DOMXPath($dom);

            $items = null;
            if (stristr($container_selector, '#')) {
                $selector = str_replace('#', '', $container_selector);
                $items = $xpath->query("//*[@id='" . $selector . "']");
            } else {
                $selector = str_replace('.', '', $container_selector);
                //$items = $xpath->query("//*[@class='" . $selector . "']");
                $items = $xpath->query("//*[contains(@class,'" . $selector . "')]");
            }
            $container = is_object( $items ) ? $items->item(0) : null;

            if ($container) {

                if ($ajax) {
                    // remove page-header
                    $items = $xpath->query("//*[@class='page-header']");
                    $el = $items->item(0);
                    //echo '<pre>'; print_r($el); echo '</pre>'; die;
                    if ($el) {
                        $container->removeChild($el);
                    }

                    // remove category title
                    $items = $xpath->query("//*[@class='subheading-category']");
                    $el = $items->item(0);
                    if ($el) {
                        $container->removeChild($el);
                    }

                    // remove category image
                    $items = $xpath->query("//*[@class='category-image']");
                    $el = $items->item(0);
                    if ($el) {
                        $container->removeChild($el);
                    }

                    // remove category description
                    $items = $xpath->query("//*[@class='category-desc clearfix']");
                    $el = $items->item(0);
                    if ($el) {
                        $container->removeChild($el);
                    }

                    // remove pagination results
                    $items = $xpath->query("//*[@class='ai-results']");
                    $el = $items->item(0);
                    if ($el) {
                        $container->removeChild($el);
                    }
                }

                // remove pagination
                $pagination_selector = $this->params->get('pagination_selector', '.pagination');
                $items = null;
                if (stristr($pagination_selector, '#')) {
                    $selector = str_replace('#', '', $pagination_selector);
                    $items = $xpath->query("//*[@id='" . $selector . "']");
                } else {
                    $selector = str_replace('.', '', $pagination_selector);
                    //$items = $xpath->query("//*[@class='" . $selector . "']");
                    $items = $xpath->query("//*[contains(@class,'" . $selector . "')]");
                }
                $pagination = is_object( $items ) ? $items->item(0) : null;
                if ($pagination) {
                    $container->removeChild($pagination);

                    // append Next link
                    $next_title = JText::_($this->params->get('next_title', 'JNEXT'));

                    $items = $pagination->getElementsByTagName('a');
                    $url = '';
                    foreach ($items as $item) {
                        //echo '<pre>'; print_r($item); echo '</pre>'; die;
                        $title = $item->getAttribute('title');
                        if ($title==JText::_('JNEXT')) {
                            $url = $item->getAttribute('href');
                        }
                    }

                    if ($url) {
                        $next = $dom->createElement('a', $next_title);
                        $href = $dom->createAttribute('href');
                        if (!strpos($url, '?')) {
                            $url .= '?ajax=1';
                        } else {
                            $url .= '&amp;ajax=1';
                        }
                        $href->value = $url;

                        $next->appendChild($href);
                        $class = $dom->createAttribute('class');
                        $class->value = 'ai-next';
                        $next->appendChild($class);

                        $container->appendChild($next);
                    }
                }

                // load only what needed
                if ($ajax) {
                    $buffer = $this->get_inner_html($container);

                    $buffer = $this->sef($buffer);

                    echo $buffer;
                    exit;
                } else {
                    $buffer = $dom->saveHTML($container);
                    $doc->setBuffer($buffer, 'component');
                }
            }
        }
    }

    private function get_inner_html( $node ) {
        $innerHTML= '';
        $children = $node->childNodes;
        foreach ($children as $child) {
            $innerHTML .= $child->ownerDocument->saveXML( $child );
        }

        return $innerHTML;
    }

    private function sef( $buffer ) {

        $base   = JUri::base(true) . '/';

        $regex  = '#href="index.php\?([^"]*)#m';
        $buffer = preg_replace_callback($regex, array('plgSystemAJAXInfinity', 'route'), $buffer);
        $this->checkBuffer($buffer);

        // Check for all unknown protocals (a protocol must contain at least one alpahnumeric character followed by a ":").
        $protocols = '[a-zA-Z0-9]+:';
        $regex     = '#(src|href|poster)="(?!/|' . $protocols . '|\#|\')([^"]*)"#m';
        $buffer    = preg_replace($regex, "$1=\"$base\$2\"", $buffer);
        $this->checkBuffer($buffer);

        $regex  = '#(onclick="window.open\(\')(?!/|' . $protocols . '|\#)([^/]+[^\']*?\')#m';
        $buffer = preg_replace($regex, '$1' . $base . '$2', $buffer);
        $this->checkBuffer($buffer);

        // ONMOUSEOVER / ONMOUSEOUT
        $regex  = '#(onmouseover|onmouseout)="this.src=([\']+)(?!/|' . $protocols . '|\#|\')([^"]+)"#m';
        $buffer = preg_replace($regex, '$1="this.src=$2' . $base . '$3$4"', $buffer);
        $this->checkBuffer($buffer);

        // Background image.
        $regex  = '#style\s*=\s*[\'\"](.*):\s*url\s*\([\'\"]?(?!/|' . $protocols . '|\#)([^\)\'\"]+)[\'\"]?\)#m';
        $buffer = preg_replace($regex, 'style="$1: url(\'' . $base . '$2$3\')', $buffer);
        $this->checkBuffer($buffer);

        // OBJECT <param name="xx", value="yy"> -- fix it only inside the <param> tag.
        $regex  = '#(<param\s+)name\s*=\s*"(movie|src|url)"[^>]\s*value\s*=\s*"(?!/|' . $protocols . '|\#|\')([^"]*)"#m';
        $buffer = preg_replace($regex, '$1name="$2" value="' . $base . '$3"', $buffer);
        $this->checkBuffer($buffer);

        // OBJECT <param value="xx", name="yy"> -- fix it only inside the <param> tag.
        $regex  = '#(<param\s+[^>]*)value\s*=\s*"(?!/|' . $protocols . '|\#|\')([^"]*)"\s*name\s*=\s*"(movie|src|url)"#m';
        $buffer = preg_replace($regex, '<param value="' . $base . '$2" name="$3"', $buffer);
        $this->checkBuffer($buffer);

        // OBJECT data="xx" attribute -- fix it only in the object tag.
        $regex  = '#(<object\s+[^>]*)data\s*=\s*"(?!/|' . $protocols . '|\#|\')([^"]*)"#m';
        $buffer = preg_replace($regex, '$1data="' . $base . '$2"$3', $buffer);
        $this->checkBuffer($buffer);


        return $buffer;
    }

    private function checkBuffer($buffer)
    {
        if ($buffer === null)
        {
            switch (preg_last_error())
            {
                case PREG_BACKTRACK_LIMIT_ERROR:
                    $message = "PHP regular expression limit reached (pcre.backtrack_limit)";
                    break;
                case PREG_RECURSION_LIMIT_ERROR:
                    $message = "PHP regular expression limit reached (pcre.recursion_limit)";
                    break;
                case PREG_BAD_UTF8_ERROR:
                    $message = "Bad UTF8 passed to PCRE function";
                    break;
                default:
                    $message = "Unknown PCRE error calling PCRE function";
            }

            throw new RuntimeException($message);
        }
    }

    protected static function route(&$matches)
    {
        $url   = $matches[1];
        $url   = str_replace('&amp;', '&', $url);
        $route = JRoute::_('index.php?' . $url);

        return 'href="' . $route;
    }
}