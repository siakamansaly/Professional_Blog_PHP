<?php
namespace Blog\Controllers;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class PhpAdditionalTwigExtension
 * Adds features to Twig Views
 */
class PhpAdditionalExtension extends AbstractExtension
{
     /**
     * @var
     */
    private $alert = null;

       /**
     * PamExtension constructor.
     */
    public function __construct()
    {
        $this->session  = filter_var_array($_SESSION);
        if (isset($this->session['user'])) {
            $this->user = $this->session['user'];
        }
    }

    /**
     * Adds functions to Twig Views
     * @return array|TwigFunction[]
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('url', array($this, 'url')),
            new TwigFunction('getCookieArray',  array($this, 'getCookieArray')),
            new TwigFunction('hasAlert',        array($this, 'hasAlert')),
            new TwigFunction('readAlert',       array($this, 'readAlert')),
            new TwigFunction('getSessionArray', array($this, 'getSessionArray')),
            new TwigFunction('isLogged',        array($this, 'isLogged')),
            new TwigFunction('getUserVar',      array($this, 'getUserVar'))
        );
    }

    /**
     * Returns the Page URL
     * @param string $page
     * @param array $params
     * @return string
     */
    public function url(string $page, array $params = [])
    {
        $params['access'] = $page;
        return 'index.php?' . http_build_query($params);
    }

        /**
     * @return mixed
     */
    public function getCookieArray()
    {
        return $this->cookie;
    }

    /**
     * @return bool
     */
    public function hasAlert()
    {
        return empty($this->alert) == false;
    }

    public function readAlert()
    {
        if (isset($this->alert)) {
            echo filter_var($this->alert, FILTER_SANITIZE_SPECIAL_CHARS);

            if ($this->alert !== null) {
                setcookie('alert', '', time() - 3600, '/');
            }
        }
    }

    /**
     * @return array|mixed
     */
    public function getSessionArray()
    {
        return $this->session;
    }

    /**
     * @return bool
     */
    public function isLogged()
    {
        if (array_key_exists('user', $this->session)) {

            if (!empty($this->session['user'])) {

                return true;
            }
        }
        return false;
    }

    /**
     * @param $var
     * @return mixed
     */
    public function getUserVar($var)
    {
        if ($this->isLogged() === false) {
            $this->user[$var] = null;
        }

        return $this->user[$var];
    }

    
}