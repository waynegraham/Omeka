<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Controller_Plugin_HtmlPurifierTest extends Omeka_Test_AppTestCase
{
    protected function _getHtmlPurifierPlugin($allowedHtmlElements=null, $allowedHtmlAttributes=null)
    {
        $htmlPurifier =  $this->_getHtmlPurifier($allowedHtmlElements, $allowedHtmlAttributes);
        $htmlPurifierPlugin = new Omeka_Controller_Plugin_HtmlPurifier();
        return $htmlPurifierPlugin;
    }
    
    protected function _getHtmlPurifier($allowedHtmlElements=null, $allowedHtmlAttributes=null)
    {                   
        $htmlPurifier = Omeka_Filter_HtmlPurifier::createHtmlPurifier($allowedHtmlElements, $allowedHtmlAttributes);
        Omeka_Filter_HtmlPurifier::setHtmlPurifier($htmlPurifier);
        return $htmlPurifier;
    }
    
    public function testIsFormSubmission()
    {
        $formActions = array('add', 'edit', 'config');
        foreach($formActions as $formAction) {
            $request = new Zend_Controller_Request_HttpTestCase();
            $request->setActionName($formAction);
            $request->setMethod('POST');
    
            $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin();
            $this->assertTrue($htmlPurifierPlugin->isFormSubmission($request));
        }
    }
    
    public function testFilterCollectionsFormForUnallowedElementInDescription()
    {
        $dirtyHtml = '<p>Bob</p>';
        $cleanHtml = 'Bob';

        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();
        $post = array('description'=>$dirtyHtml);
        $request->setPost($post);

        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin(array(), array());
        $htmlPurifierPlugin->filterCollectionsForm($request);

        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['description']);
    }
    
    public function testFilterCollectionsFormForAllowedElementInDescription()
    {
        $dirtyHtml = '<p>Bob</p>';
        $cleanHtml = '<p>Bob</p>';
    
        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();
        $post = array('description'=>$dirtyHtml);
        $request->setPost($post);
    
        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin(array('p'), array());
        $htmlPurifierPlugin->filterCollectionsForm($request);
    
        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['description']);
    }
    
    public function testFilterCollectionsFormForAllowedElementAndUnallowedAttributeInDescription()
    {
        $dirtyHtml = '<p class="person">Bob</p>';
        $cleanHtml = '<p>Bob</p>';
    
        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();
        $post = array('description'=>$dirtyHtml);
        $request->setPost($post);
    
        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin(array('p'), array());
        $htmlPurifierPlugin->filterCollectionsForm($request);
    
        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['description']);
    }
    
    public function testFilterCollectionsFormForAllowedElementAndAllowedAttributeInDescription()
    {
        $dirtyHtml = '<p class="person">Bob</p>';
        $cleanHtml = '<p class="person">Bob</p>';
    
        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();
        $post = array('description'=>$dirtyHtml);
        $request->setPost($post);
    
        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin(array('p'), array('*.class'));
        $htmlPurifierPlugin->filterCollectionsForm($request);
    
        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['description']);
    }
    
    public function testFilterCollectionsFormForAllowedAndUnAllowedElementsInDescription()
    {
        $dirtyHtml = '<p><strong>Bob</strong> is <em>dead</em>.</p><br />';
        $cleanHtml = '<p>Bob is dead.</p><br />';
    
        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();
        $post = array('description'=>$dirtyHtml);
        $request->setPost($post);
    
        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin(array('p','br'), array());
        $htmlPurifierPlugin->filterCollectionsForm($request);
    
        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['description']);
    }
    
    public function testFilterItemsFormForUnallowedElement()
    {
        $dirtyHtml = '<p>Bob</p>';
        $cleanHtml = 'Bob';
    
        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();
    
        // post looks like Elements[element_id][index] = array([text], [html])
        $post = array(
            'Elements'=> array(
                array(
                    array('text' => $dirtyHtml, 'html' => true),
                    array('text' => $dirtyHtml, 'html' => false)
                ),
                array(
                    array('text' => $dirtyHtml, 'html' => false),
                    array('text' => $dirtyHtml, 'html' => true)
                )
            ),
            'tags' => 'foo, bar'
        );
    
        $request->setPost($post);
    
        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin(array(), array());
        $htmlPurifierPlugin->filterItemsForm($request);
    
        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['Elements'][0][0]['text']);
        $this->assertEquals($dirtyHtml, $post['Elements'][0][1]['text']);
        $this->assertEquals($dirtyHtml, $post['Elements'][1][0]['text']);
        $this->assertEquals($cleanHtml, $post['Elements'][1][1]['text']);
    }      
    
    
    public function testFilterItemsFormForAnAllowedElement()
    {
        $dirtyHtml = '<p>Bob</p>';
        $cleanHtml = '<p>Bob</p>';
    
        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();
    
        $post = array(
            'Elements'=> array(
                array(
                    array('text' => $dirtyHtml, 'html' => true),
                    array('text' => $dirtyHtml, 'html' => false)
                ),
                array(
                    array('text' => $dirtyHtml, 'html' => false),
                    array('text' => $dirtyHtml, 'html' => true)
                )
            ),
            'tags' => ''
        );
    
        $request->setPost($post);
    
        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin(array('p'), array());
        $htmlPurifierPlugin->filterItemsForm($request);
    
        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['Elements'][0][0]['text']);
        $this->assertEquals($dirtyHtml, $post['Elements'][0][1]['text']);
        $this->assertEquals($dirtyHtml, $post['Elements'][1][0]['text']);
        $this->assertEquals($cleanHtml, $post['Elements'][1][1]['text']);
    }
    
    public function testFilterItemsFormForAllowedAndUnallowedElements()
    {
        $dirtyHtml = '<p><strong>Bob</strong> is <em>dead</em>.</p><br />';
        $cleanHtml = '<p>Bob is dead.</p><br />';
    
        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();
    
        $post = array(
            'Elements'=> array(
                array(
                    array('text' => $dirtyHtml, 'html' => true),
                    array('text' => $dirtyHtml, 'html' => false)
                ),
                array(
                    array('text' => $dirtyHtml, 'html' => false),
                    array('text' => $dirtyHtml, 'html' => true)
                )
            ),
            'tags' => ''
        );
    
        $request->setPost($post);
    
        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin(array('p','br'), array());
        $htmlPurifierPlugin->filterItemsForm($request);
    
        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['Elements'][0][0]['text']);
        $this->assertEquals($dirtyHtml, $post['Elements'][0][1]['text']);
        $this->assertEquals($dirtyHtml, $post['Elements'][1][0]['text']);
        $this->assertEquals($cleanHtml, $post['Elements'][1][1]['text']);
    }
    
    public function testFilterThemesFormForAllowedAndUnallowedElements()
    {
        $dirtyHtml = '<p><strong>Bob</strong> is <em>dead</em>.</p><br />';
        $cleanHtml = '<p>Bob is dead.</p><br />';
    
        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();
    
        // post can be any nested array of strings
        $post = array(
            'whatever' => $dirtyHtml,
            'NestedArray'=> array(
                array(
                    array('text' => $dirtyHtml),
                    array('text' => $dirtyHtml)
                ),
                array(
                    array('text' => $dirtyHtml),
                    array('text' => $dirtyHtml)
                )
            ),
            'whatever2' => $dirtyHtml
        );
    
        $request->setPost($post);
    
        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin(array('p', 'br'), array());
        $htmlPurifierPlugin->filterThemesForm($request);
    
        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['whatever']);
        $this->assertEquals($cleanHtml, $post['NestedArray'][0][0]['text']);
        $this->assertEquals($cleanHtml, $post['NestedArray'][0][1]['text']);
        $this->assertEquals($cleanHtml, $post['NestedArray'][1][0]['text']);
        $this->assertEquals($cleanHtml, $post['NestedArray'][1][1]['text']);
        $this->assertEquals($cleanHtml, $post['whatever2']);
    }
    
    public function testFilterThemesFormForAllowedAndUnallowedElementsAndAttributes()
    {
        $dirtyHtml = '<p class="person" href="what"><strong>Bob</strong> is <em>dead</em>.</p><br />';
        $cleanHtml = '<p class="person">Bob is dead.</p><br />';
    
        // Create a request with dirty html for the collection description post variable
        $request = new Zend_Controller_Request_HttpTestCase();
    
        // post can be any nested array of strings
        $post = array(
            'whatever' => $dirtyHtml,
            'NestedArray'=> array(
                0 => array(
                        array('text' => $dirtyHtml),
                        array('text' => $dirtyHtml)
                 ),
                1 => array(
                        array('text' => $dirtyHtml),
                        array('text' => $dirtyHtml)
                 )
            ),
            'whatever2' => $dirtyHtml
        );
    
        $request->setPost($post);
    
        // Html purify the request
        $htmlPurifierPlugin = $this->_getHtmlPurifierPlugin(array('p', 'br'), array('*.class'));
        $htmlPurifierPlugin->filterThemesForm($request);
    
        // Make sure the description post variable is clean
        $post = $request->getPost();
        $this->assertEquals($cleanHtml, $post['whatever']);
        $this->assertEquals($cleanHtml, $post['NestedArray'][0][0]['text']);
        $this->assertEquals($cleanHtml, $post['NestedArray'][0][1]['text']);
        $this->assertEquals($cleanHtml, $post['NestedArray'][1][0]['text']);
        $this->assertEquals($cleanHtml, $post['NestedArray'][1][1]['text']);
        $this->assertEquals($cleanHtml, $post['whatever2']);
    }
}