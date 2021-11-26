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

class ItSupportUsersWithRights extends DataElementBitrixList
{
    const BITRIX_LIST_SUPPORT_STIM_DEPARTMENTS_WITH_USERS_RULES = 152;
    const DEP_TREE_SQL_RTEQUEST = "select ID, NAME, IBLOCK_SECTION_ID from b_iblock_section where IBLOCK_ID=5";
    private $usersArray = [];
    public function __construct()
    {
        parent::__construct( self::BITRIX_LIST_SUPPORT_STIM_DEPARTMENTS_WITH_USERS_RULES );
        $this->usersArray = $this->getUsersArray();
    }

    public function getDataForSelectVue()
    {
        $dataFromNameLists = $this->getArrRecordsList ();
        $dataForSelectVue = [];
        foreach ( $this->arrFromIblockList as $recordId=>$recordValue ) 
        {
            $dataForSelectVue[] = [ 'value'=>$recordValue['ID'], "text"=>$dataFromNameLists[$recordId]['NAME'], "stim_company"=>$recordValue['STIM_COMPANY']['VALUE'], 
                                    "user_with_rights"=>$recordValue['USERS_WITH_RIGHTS']['VALUE'], "id_department"=>$recordValue['ID_DEPARTMENT_BITRIKS']['VALUE'] ];
            foreach ( $this->getArrUsersIdByDepId ( $dataForSelectVue[ array_key_last( $dataForSelectVue ) ]['id_department']  ) as $userId ) //print_r( $userId . "  ");
                $dataForSelectVue[ array_key_last( $dataForSelectVue ) ]['users'][] = [ "value"=>$userId, "text"=>$this->usersArray[ $userId ]["text"] ];
        }
        return $dataForSelectVue;
    }

    public function getSubjectByRecordId( $recordId )
    {
        $arrFilteredNames = array_filter( $this->getDataForSelectVue(), function ( $name ) use ( $recordId ) {
            return $name['value'] == $recordId;
        });
        $firstKey = array_key_first( $arrFilteredNames );
        return [ 'DEPARTMENT'=>$arrFilteredNames[$firstKey]['text'], 'COMPANY'=>$arrFilteredNames[$firstKey]['stim_company'], 'COMPANY'=>$arrFilteredNames[$firstKey]['stim_company'],
                "ARR_USERS_WITH_RIGHTS"=>$arrFilteredNames[$firstKey]['user_with_rights'], "DEPARTMENT_BITRIKS_ID"=>$arrFilteredNames[$firstKey]['id_department'] ];
    }

    public function getDepIdByRightUserId()
    {
        global $USER;
        $userId = $USER->GetID();
        $filteredArrDepsAndUswersRight = array_filter( $this->getDataForSelectVue(), function( $item ) use( $userId ) {
            return in_array( $userId, $item['user_with_rights'] );
        });
        return array_values( $filteredArrDepsAndUswersRight );
    }

    public function getFiltersdByDepUsersForSelectVue()
    {
        return $this->getDepIdByRightUserId();
    }

    public function getUsersArray()
    {
        $arrUser = [];
        $dbUser = \CUser::GetList( $by="id", $order="asc", ["active"=>"Y"], ["ID", "LAST_NAME", "NAME", "LOGIN", "EMAIL"]);
        while( $user = $dbUser->Fetch() ) $arrUser[ $user["ID"]] = [ "value"=>$user["ID"], "text"=>$user["LAST_NAME"] . ( $user["LAST_NAME"] ? " " : "") .$user["NAME"],];
//        usort( $arrUser, function ( $a, $b )
//        {
//            if ($a["text"] == $b["text"]) {
//                return 0;
//            }
//            return ($a["text"] < $b["text"]) ? -1 : 1;
//        });
        return $arrUser;
    }

    public function getArrUsersIdByDepId ( $depId )
    {
        $depTree = new DepartmentTree( self::DEP_TREE_SQL_RTEQUEST );
        return $depTree->getArrUsersFromDepartmentPoint( $depId );
    }

}