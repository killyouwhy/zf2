<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Form\Decorator;

/**
 * Zend_Form_Decorator_Image
 *
 * Accepts the options:
 * - separator: separator to use between image and content (defaults to PHP_EOL)
 * - placement: whether to append or prepend label to content (defaults to append)
 * - tag: if set, used to wrap the label in an additional HTML tag
 *
 * Any other options passed will be used as HTML attributes of the image tag.
 *
 * @uses       \Zend\Form\Decorator\AbstractDecorator
 * @uses       \Zend\Form\Decorator\HtmlTag;
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Image extends AbstractDecorator
{
    /**
     * Attributes that should not be passed to helper
     * @var array
     */
    protected $_attribBlacklist = array('helper', 'placement', 'separator', 'tag');

    /**
     * Default placement: append
     * @var string
     */
    protected $_placement = 'APPEND';

    /**
     * HTML tag with which to surround image
     * @var string
     */
    protected $_tag;

    /**
     * Set HTML tag with which to surround label
     *
     * @param  string $tag
     * @return \Zend\Form\Decorator\Image
     */
    public function setTag($tag)
    {
        $this->_tag = (string) $tag;
        return $this;
    }

    /**
     * Get HTML tag, if any, with which to surround label
     *
     * @return void
     */
    public function getTag()
    {
        if (null === $this->_tag) {
            $tag = $this->getOption('tag');
            if (null !== $tag) {
                $this->removeOption('tag');
                $this->setTag($tag);
            }
            return $tag;
        }

        return $this->_tag;
    }

    /**
     * Get attributes to pass to image helper
     *
     * @return array
     */
    public function getAttribs()
    {
        $attribs = $this->getOptions();

        if (null !== ($element = $this->getElement())) {
            $attribs['alt'] = $element->getLabel();
            $attribs = array_merge($attribs, $element->getAttribs());
        }

        foreach ($this->_attribBlacklist as $key) {
            if (array_key_exists($key, $attribs)) {
                unset($attribs[$key]);
            }
        }

        return $attribs;
    }

    /**
     * Render a form image
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $tag           = $this->getTag();
        $placement     = $this->getPlacement();
        $separator     = $this->getSeparator();
        $name          = $element->getFullyQualifiedName();
        $attribs       = $this->getAttribs();
        $attribs['id'] = $element->getId();

        $image = $view->formImage($name, $element->getImageValue(), $attribs);

        if (null !== $tag) {
            $decorator = new HtmlTag();
            $decorator->setOptions(array('tag' => $tag));
            $image = $decorator->render($image);
        }

        switch ($placement) {
            case self::PREPEND:
                return $image . $separator . $content;
            case self::APPEND:
            default:
                return $content . $separator . $image;
        }
    }
}
