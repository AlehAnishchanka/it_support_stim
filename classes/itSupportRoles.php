<?php
namespace local\stim\it_support_stim\classes;
class itSupportRoles extends DataElementBitrixList
{
    const BITRIX_LIST_SUPPORT_IT_ROLES = 154;
    public function __construct()
    {
        parent::__construct( self::BITRIX_LIST_SUPPORT_IT_ROLES );
    }

    public function getDataForSelectVue()
    {
        $dataFromNameLists = $this->getArrRecordsList ();
        $dataForSelectVue = [];
        foreach ( $this->arrFromIblockList as $recordId=>$recordValue ) 
        {
            //print_r($recordValue);
            $dataForSelectVue[] = [ 'value'=>$recordId, "text"=>$dataFromNameLists[ $recordId ]['NAME'], "test_pl"=>$recordValue['ROLE_PL']['VALUE'], "role_groups"=>$recordValue['GRUP_ROLES']['VALUE'] ];
        }
        return $dataForSelectVue;
    }

    public function getSubjectByRecordId( $recordId )
    {
        $arrFilteredNames = array_filter( $this->getDataForSelectVue(), function ( $name ) use ( $recordId ) {
            return $name['value'] == $recordId;
        });
        $firstKey = array_key_first( $arrFilteredNames );
        return [ 'ROLE_RU'=>$arrFilteredNames[$firstKey]['text'], 'ROLE_PL'=>$arrFilteredNames[$firstKey]['test_pl'] ];
    }

}