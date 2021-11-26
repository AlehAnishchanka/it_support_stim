<?php
namespace local\stim\it_support_stim\classes;
class itSupportRoleGrpups extends DataElementBitrixList
{
    const BITRIX_LIST_SUPPORT_IT_ROLE_GROUPS = 153;
    public function __construct()
    {
        parent::__construct( self::BITRIX_LIST_SUPPORT_IT_ROLE_GROUPS );
    }

    public function getDataForSelectVue()
    {
        $dataFromNameLists = $this->getArrRecordsList ();
        $dataForSelectVue = [];
        foreach ( $this->arrFromIblockList as $recordId=>$recordValue ) 
        {
            //print_r($recordValue);
            $dataForSelectVue[] = [ 'value'=>$recordId, "text"=>$dataFromNameLists[ $recordId ]['NAME'], "test_pl"=>$recordValue['GRUPPA_ROLEY_PL']['VALUE']];
        }
        return $dataForSelectVue;
    }

    public function getSubjectByRecordId( $recordId )
    {
        $arrFilteredNames = array_filter( $this->getDataForSelectVue(), function ( $name ) use ( $recordId ) {
            return $name['value'] == $recordId;
        });
        $firstKey = array_key_first( $arrFilteredNames );
        return [ 'GROUP_NAME_RU'=>$arrFilteredNames[$firstKey]['text'], 'GROUP_NAME_PL'=>$arrFilteredNames[$firstKey]['test_pl'] ];
    }

}