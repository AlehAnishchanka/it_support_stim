<?php
namespace local\stim\it_support_stim\classes;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
spl_autoload_register( function ( $className ) {
    $className = str_replace( "\\", "/", $className);
    $file = $_SERVER['DOCUMENT_ROOT'] . "/{$className}.php";
    if( file_exists( $file ) )
        require_once $file;
});

use local\stim\libraries\library_departments_tree\{ DepartmentTree, AbstractTree, ContainerTree, DataProvider, Department, ContainerContent };
use local\stim\it_support_stim\classes\{ ItSupportUsersWithRights };

class ItSupportUsers
{
    const ARR_NOT_INCLUDED_LOGINS = [ "windchilltest", "tv_udr", "tle_agr", "erp_agr", "bitrix", "acc_agr", "admin"];
    const DEP_TREE_SQL_RTEQUEST = "select ID, NAME, IBLOCK_SECTION_ID from b_iblock_section where IBLOCK_ID=5";
    private $arrUsers = [];

    public function __construct()
    {
        $this->getArrUsers();
    }

    public function getArrUsers ()
    {
        $arrUser = [];
        $dbUser = \CUser::GetList( $by="id", $order="asc", ["active"=>"Y"], ["ID", "LAST_NAME", "NAME", "LOGIN", "EMAIL"]);
        while( $user = $dbUser->Fetch() )
            if( stripos( $user["LOGIN"], "bot_") === false && $user["LOGIN"] != $user["EMAIL"] && stripos( $user["LOGIN"], "imconnector") === false &&
                 ! in_array( $user["LOGIN"], self::ARR_NOT_INCLUDED_LOGINS ) )
                   $arrUser[] = [ "value"=>$user["ID"], "text"=>$user["LAST_NAME"] . ( $user["LAST_NAME"] ? " " : "") .$user["NAME"],];
        usort( $arrUser, function ( $a, $b )
        {
            if ($a["text"] == $b["text"]) {
                return 0;
            }
            return ($a["text"] < $b["text"]) ? -1 : 1;
        });
        $this->arrUsers = $arrUser;
        return $arrUser;
    }

    public function getDataForSelectVue()
    {
        return $this->getArrUsers();
    }

    public function getUserNameById( $userId )
    {
        $selectedUser =  array_filter( $this->arrUsers, function( $user ) use( $userId ) {
            return $user['value'] == $userId;
        });
        return $selectedUser[ array_key_first($selectedUser) ]['text'];
    }

    public function getArrUsersByDepartamentId( $depId )
    {
        $depTree = new DepartmentTree( self::DEP_TREE_SQL_RTEQUEST );
        $arrUsersIdWorkkInDep = $depTree->getArrUsersFromDepartmentPoint( $depId );
        $arrUsersFilteredByDep = [];
        $arrUsersFilteredByDep = array_filter( $this->arrUsers, function( $user ) use( $arrUsersIdWorkkInDep ) {
            return in_array( $user["value"], $arrUsersIdWorkkInDep );
        });
        return array_values($arrUsersFilteredByDep);
    }

    public function getArrUsersByDepartamentIdForSelectVue()
    {
        $objSupportUserWithRights = new ItSupportUsersWithRights();
        $arrDepIdWithUsers = [];
        foreach( $objSupportUserWithRights->getFiltersdByDepUsersForSelectVue() as $depWithUsers )
            foreach( $depWithUsers as $userId )
                $arrDepIdWithUsers[ $depWithUsers['id_department'] ][] = $userId;
        return $arrDepIdWithUsers;
    }

}