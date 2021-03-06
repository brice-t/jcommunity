<?php
/**
* @package      jcommunity
* @subpackage   
* @author       Laurent Jouanneau <laurent@xulfr.org>
* @contributor
* @copyright    2008 Laurent Jouanneau
* @link         http://jelix.org
* @licence      http://www.gnu.org/licenses/gpl.html GNU General Public Licence, see LICENCE file
*/


class statusZone extends jZone {

    protected $_tplname='status';

    protected function _prepareTpl(){

        if(jAuth::isConnected()) {
            $this->_tpl->assign('login',jAuth::getUserSession ()->login);
        }
    }
}

