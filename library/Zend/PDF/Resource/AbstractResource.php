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
 * @package    Zend_PDF
 * @package    Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\PDF\Resource;
use Zend\PDF\InternalType;
use Zend\PDF\ObjectFactory;

/**
 * PDF file Resource abstraction
 *
 * @uses       \Zend\PDF\ObjectFactory
 * @uses       \Zend\PDF\InternalType
 * @package    Zend_PDF
 * @package    Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractResource
{
    /**
     * Each PDF resource (fonts, images, ...) interacts with a PDF itself.
     * It creates appropriate PDF objects, structures and sometime embedded files.
     * Resources are referenced in content streams by names, which are stored in
     * a page resource dictionaries.
     *
     * Thus, resources must be attached to the PDF.
     *
     * Resource abstraction uses own PDF object factory to store all necessary information.
     * At the render time internal object factory is appended to the global PDF file
     * factory.
     *
     * Resource abstraction also cashes information about rendered PDF files and
     * doesn't duplicate resource description each time then Resource is rendered
     * (referenced).
     *
     * @var \Zend\PDF\ObjectFactory
     */
    protected $_objectFactory;

    /**
     * Main resource object
     *
     * @var \Zend\PDF\InternalType\IndirectObject
     */
    protected $_resource;

    /**
     * Object constructor.
     *
     * If resource is not a \Zend\PDF\InternalType\AbstractTypeObject object,
     * then stream object with specified value is generated.
     *
     * @param \Zend\PDF\InternalType\AbstractTypeObject|string $resource
     */
    public function __construct($resource)
    {
        $this->_objectFactory = ObjectFactory\ElementFactory::createFactory(1);
        if ($resource instanceof InternalType\AbstractTypeObject) {
            $this->_resource = $this->_objectFactory->newObject($resource);
        } else {
            $this->_resource = $this->_objectFactory->newStreamObject($resource);
        }
    }

    /**
     * Get resource.
     * Used to reference resource in an internal PDF data structures (resource dictionaries)
     *
     * @internal
     * @return \Zend\PDF\InternalType\IndirectObject
     */
    public function getResource()
    {
        return $this->_resource;
    }

    /**
     * Get factory.
     *
     * @internal
     * @return \Zend\PDF\ObjectFactory
     */
    public function getFactory()
    {
        return $this->_objectFactory;
    }
}
