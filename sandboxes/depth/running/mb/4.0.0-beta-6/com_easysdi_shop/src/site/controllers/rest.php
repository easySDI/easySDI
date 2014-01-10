<?php

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Description of rest
 *
 * @author Marc Battaglia <marc.battaglia@depth.ch>
 */
class Easysdi_shopControllerRest extends Easysdi_shopController {

    /** @var string Possible values global or organism */
    private $userType = 'global';
    private $organism;

    /** @var JDatabaseDriver Description */
    private $db;

    function __construct() {
        parent::__construct();

        $this->db = JFactory::getDbo();
    }

    /**
     * Main wps method
     */
    public function wps() {
        if ($this->authentification()) {
            echo 'Authentification OK :'.$this->userType;
        }
    }

    private function getOrders() {
        if ($this->authentification()) {
            echo "getOrders";
            die();
        }
    }

    private function setOrder() {
        
    }

    /**
     * Authenticate user
     * 
     * @return boolean
     */
    private function authentification() {
        $success = FALSE;

        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Texte utilisé si le visiteur utilise le bouton d\'annulation';
            exit;
        } else {
            if ($this->isOrderAccount($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
                $success = TRUE;
            } elseif ($this->isOrganismAccount($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) {
                $success = TRUE;
                $this->userType = 'organism';
            } else {
                header('WWW-Authenticate: Basic realm="My Realm"');
                header('HTTP/1.0 401 Unauthorized');
                echo "Nom d'utilisateur ou mot de passe invalide.";
            }
        }

        return $success;
    }

    /**
     * Check if user is the orderaccount user
     * 
     * @param string $username
     * @param string $password
     * @return boolean
     */
    private function isOrderAccount($username, $password) {
        $globalUserId = JComponentHelper::getParams('com_easysdi_shop')->get('orderaccount');
        $orderaccount = JFactory::getUser($globalUserId);
        $passwordarray = explode(':', $orderaccount->password);

        $pwdCryp = JUserHelper::getCryptedPassword($password, $passwordarray[1]) . ':' . $passwordarray[1];

        if ($username == $orderaccount->username && $orderaccount->password == $pwdCryp) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Check if user is an organism
     * 
     * @param string $username
     * @param string $password
     * @return boolean
     */
    private function isOrganismAccount($username, $password) {
        $query = $this->db->getQuery(true);

        $query->select('o.username, o.password');
        $query->from('#__sdi_organism o');
        $query->where('o.username = \'' . $username.'\'');

        $this->db->setQuery($query);

        if ($organism = $this->db->loadObject()) {
            $passwordarray = explode(':', $organism->password);
            $pwdCryp = JUserHelper::getCryptedPassword($password, $passwordarray[1]) . ':' . $passwordarray[1];
            
            if($organism->password == $pwdCryp){
                $this->organism = $organism;
                return TRUE;
            }else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

}
