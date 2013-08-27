<?php
class Inventory_Controller_Action_Helper_Json extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Suppress exit when sendJson() called
     * @var boolean
     */
    public $suppressExit = false;

    /**
     * Create JSON response
     *
     * Encodes and returns data to JSON. Content-Type header set to
     * 'application/json', and disables layouts and viewRenderer (if being
     * used).
     *
     * @param  mixed   $data
     * @param  boolean $keepLayouts
     * @param  boolean|array $keepLayouts
     * @param  boolean $encodeData Provided data is already JSON
     * NOTE:   if boolean, establish $keepLayouts to true|false
     *         if array, admit params for Zend_Json::encode as enableJsonExprFinder=>true|false
     *         if $keepLayouts and parmas for Zend_Json::encode are required
     *         then, the array can contains a 'keepLayout'=>true|false and/or 'encodeData'=>true|false
     *         that will not be passed to Zend_Json::encode method but will be passed
     *         to Zend_View_Helper_Json
     * @throws Zend_Controller_Action_Helper_Json
     * @return string
     */
    public function encodeJson($data, $keepLayouts = false, $encodeData = true)
    {
        /**
         * @see Zend_View_Helper_Json
         */
        require_once 'Zend/View/Helper/Json.php';
        $jsonHelper = new Zend_View_Helper_Json();
        $data = $jsonHelper->json($data, $keepLayouts, $encodeData);

        if (!$keepLayouts) {
            /**
             * @see Zend_Controller_Action_HelperBroker
             */
            require_once 'Zend/Controller/Action/HelperBroker.php';
            Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
        }

        return $data;
    }

    /**
     * Encode JSON response and immediately send
     *
     * @param  mixed   $data
     * @param  boolean|array $keepLayouts
     * @param  $encodeData Encode $data as JSON?
     * NOTE:   if boolean, establish $keepLayouts to true|false
     *         if array, admit params for Zend_Json::encode as enableJsonExprFinder=>true|false
     *         if $keepLayouts and parmas for Zend_Json::encode are required
     *         then, the array can contains a 'keepLayout'=>true|false and/or 'encodeData'=>true|false
     *         that will not be passed to Zend_Json::encode method but will be passed
     *         to Zend_View_Helper_Json
     * @return string|void
     */
    public function sendJson($data, $callback, $keepLayouts = false, $encodeData = true)
    {
        $data = $this->encodeJson($data, $keepLayouts, $encodeData);
        if($callback) {
            $data = sprintf('%s(%s)', $callback, $data);
        }
        $response = $this->getResponse();
        $response->setBody($data);

        if (!$this->suppressExit) {
            $response->sendResponse();
            exit;
        }

        return $data;
    }

    /**
     * Strategy pattern: call helper as helper broker method
     *
     * Allows encoding JSON. If $sendNow is true, immediately sends JSON
     * response.
     *
     * @param  mixed   $data
     * @param  string  $callback Jsonp callback
     * @param  boolean $sendNow
     * @param  boolean $keepLayouts
     * @param  boolean $encodeData Encode $data as JSON?
     * @return string|void
     */
    public function direct($data, $callback = null, $sendNow = true, $keepLayouts = false, $encodeData = true)
    {
        if ($sendNow) {
            return $this->sendJson($data, $callback, $keepLayouts, $encodeData);
        }
        return $this->encodeJson($data, $keepLayouts, $encodeData);
    }
}
