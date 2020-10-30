<?php
namespace WHMCS\Module\Addon\Mailerlite\Views;

class Views {

    /**
     * Fetched content from the template
     *
     * @var string
     */
    protected $templateString;

    /**
     * Array of variables to inject into template string
     *
     * @var array
     */
    protected $params;
    
    /**
     * Page action
     *
     * @var string
     */
    protected $action;

    /**
     * Is error
     *
     * @var bool
     */
    protected $error;

    public function __construct($templateString, $params = [], $action = '', $error = false)
    {
        $this->templateString = $templateString;
        $this->params = $params;
        $this->action = $action;
        $this->error = $error;
    }

    /**
     * Main 
     *
     * @return void
     */
    public function render()
    {
        if($this->action != '') {
            $action = $this->action;
            $this->$action();

            return $this->templateString;
        }

        return '<p>Invalid action requested. Please go back and try again.</p>';
    }

    /**
     * Rendering index page
     *
     * @return void
     */
    private function index()
    {
        $keys = array_keys($this->params);
        $keyCount = count($keys);

        for ($i = 0; $i < $keyCount; $i++) {
            $this->templateString = str_replace('{{' . $keys[$i] . '}}', $this->params[$keys[$i]], $this->templateString);
        }

        $this->templateString = ($this->error) ? 
            str_replace('{{errormessage}}', $this->errorAlert($this->params['message']), $this->templateString) :
            str_replace('{{errormessage}}', '', $this->templateString);
    }

    /**
     * Rendering Primary Subscripption list page
     *
     * @return void
     */
    private function selectlist()
    {
        $keys = array_keys($this->params);
        $keyCount = count($keys);

        for ($i = 0; $i < $keyCount; $i++) {
            switch ($keys[$i]) {
                case 'select':
                    $this->templateString = str_replace('{{' . $keys[$i] . '}}', $this->prepareSelectHtml($this->params[$keys[$i]]), $this->templateString);
                    break;

                case 'subscriptionbtn':
                    $this->templateString = str_replace('{{' . $keys[$i] . '}}', $this->prepareButton($this->params[$keys[$i]]), $this->templateString);
                    break;

                case 'mailerliteapikey':
                    $this->templateString = str_replace('{{' . $keys[$i] . '}}', $this->params[$keys[$i]], $this->templateString);
                    break;
                
                default:
                    $this->templateString = str_replace('{{' . $keys[$i] . '}}', $this->params[$keys[$i]], $this->templateString);
                    break;
            }
            
        }
    }

    /**
     * Rendering Synchronized page
     *
     * @return void
     */
    private function synchronizedlist() {
        $keys = array_keys($this->params);
        $keyCount = count($keys);

        for ($i = 0; $i < $keyCount; $i++) {
            $this->templateString = str_replace('{{' . $keys[$i] . '}}', $this->params[$keys[$i]], $this->templateString);
        }

        $this->templateString = ($this->error) ? 
            str_replace('{{errormessage}}', $this->errorAlert($this->params['message']), $this->templateString) :
            str_replace('{{errormessage}}', '', $this->templateString);
    }

    /**
     * Method used to prepare html string for allert box
     *
     * @param string $message
     * @return string html string
     */
    private function errorAlert($message)
    {
        $viewString = file_get_contents(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'errorbox.tpl');

        $string = str_replace('{{apierror}}', $message, $viewString);

        return $string;
    }

    /**
     * Helper method which creates select html string with fetched mailerlite lists 
     *
     * @param array $lists fetched lists
     * @return string 
     */
    private function prepareSelectHtml($lists)
    {
        $listCount = count($lists);

        if ($listCount > 0) {
            $html = '<select name="primary_list" class="form-control" id="inputPrimaryList">';

            for ($i = 0; $i < $listCount; $i++) {
                $html .= '<option value="' . $lists[$i]['id'] . '_' . $lists[$i]['name'] . '">' . $lists[$i]['name'] . '</option>';
            }

            $html .= '</select>';
            
        } else {
            $message = "You don't have any Subscription lists. Please visit <a href='https://app.mailerlite.com/users/login/'>www.mailerlite.com</a> and create list.";
            $html = $this->errorAlert($message);
        }
        
        return $html;
    }

    /**
     * Method used to prepare html string for proper button action
     *
     * @param bool $subscriptionBtn 
     * @return string html
     */
    private function prepareButton($subscriptionBtn)
    {
        if ($subscriptionBtn) {
            $string = file_get_contents(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'continuebtn.tpl');
        } else {
            $string = file_get_contents(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'partials' . DIRECTORY_SEPARATOR . 'homebtn.tpl');
        }

        return $string;
    }
}
