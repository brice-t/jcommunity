<?php
/**
* @package    jelix
* @subpackage dao
* @author      Laurent Jouanneau
* @copyright   2005-2010 Laurent Jouanneau
* Idea of this class was get originally from the Copix project
* (CopixDaoCompiler, Copix 2.3dev20050901, http://www.copix.org)
* no more line of code are copyrighted by CopixTeam
*
* @link        http://www.jelix.org
* @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 *
 */
require(JELIX_LIB_PATH.'dao/jDaoParser.class.php');
require(JELIX_LIB_PATH.'dao/jDaoProperty.class.php');
require(JELIX_LIB_PATH.'dao/jDaoMethod.class.php');
require(JELIX_LIB_PATH.'dao/jDaoGenerator.class.php');

/**
 * The compiler for the DAO xml files. it is used by jIncluder
 * It produces some php classes
 * @package  jelix
 * @subpackage dao
 */
class jDaoCompiler  implements jISimpleCompiler {

    /**
    * compile the given class id.
    */
    public function compile ($selector) {

        $daoPath = $selector->getPath();

        // chargement du fichier XML
        $doc = new DOMDocument();

        if(!$doc->load($daoPath)){
            throw new jException('jelix~daoxml.file.unknown', $daoPath);
        }

        if($doc->documentElement->namespaceURI != JELIX_NAMESPACE_BASE.'dao/1.0'){
            throw new jException('jelix~daoxml.namespace.wrong',array($daoPath, $doc->namespaceURI));
        }

        global $gJConfig;
        $tools = jApp::loadPlugin($selector->driver, 'db', '.dbtools.php', $selector->driver.'DbTools');
        if(is_null($tools))
            throw new jException('jelix~db.error.driver.notfound', $selector->driver);

        $parser = new jDaoParser ($selector);
        $parser->parse(simplexml_import_dom($doc), $tools);

        require_once($gJConfig->_pluginsPathList_db[$selector->driver].$selector->driver.'.daobuilder.php');
        $class = $selector->driver.'DaoBuilder';
        $generator = new $class ($selector, $tools, $parser);

        // génération des classes PHP correspondant à la définition de la DAO
        $compiled = '<?php '.$generator->buildClasses ()."\n?>";
        jFile::write ($selector->getCompiledFilePath(), $compiled);
        return true;
    }
}

/**
 * Exception for Dao compiler
 * @package  jelix
 * @subpackage dao
 */
class jDaoXmlException extends jException {

    /**
     * @param jSelectorDao $selector
     * @param string $localekey a locale key
     * @param array $localeParams parameters for the message (for sprintf)
     */
    public function __construct($selector, $localekey, $localeParams=array()) {
        $localekey= 'jelix~daoxml.'.$localekey;
        $arg=array($selector->toString(), $selector->getPath());
        if(is_array($localeParams)){
            $arg=array_merge($arg, $localeParams);
        }else{
            $arg[]=$localeParams;
        }
        parent::__construct($localekey, $arg);
    }
}
