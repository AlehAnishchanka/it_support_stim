<?php
namespace local\stim\it_support_stim\classes;
class ItSupportAppealType extends DataElementBitrixList
{
    const BITRIX_LIST_SUPPORT_IT_APPEAL_TYPE = 148;
    public function __construct()
    {
        parent::__construct( self::BITRIX_LIST_SUPPORT_IT_APPEAL_TYPE );
    }

    public function getDataForSelectVue()
    {
        $dataFromNameLists = $this->getArrRecordsList ();
        $dataForSelectVue = [];
        foreach ( $this->arrFromIblockList as $recordId=>$recordValue ) 
        {
            //print_r($recordValue);
            $dataForSelectVue[] = [ 'value'=>$recordValue['ID'], "text"=>$dataFromNameLists[$recordId]['NAME'], 'text_pl'=>$recordValue['NAZVANIE_PL']['VALUE'], ];
        }
        return $dataForSelectVue;
    }

    public function getSubjectByRecordId( $recordId )
    {
        $arrFilteredNames = array_filter( $this->getDataForSelectVue(), function ( $name ) use ( $recordId ) {
            //print_r($name['value']==$recordId );
            return $name['value'] == $recordId;
        });
        $firstKey = array_key_first( $arrFilteredNames );
        return [ 'TEKST_RU'=>$arrFilteredNames[$firstKey]['text'], 'TEKST_PL'=>$arrFilteredNames[$firstKey]['text_pl'], 'LIST_AVAILABLE_TYPES'=>$arrFilteredNames[$firstKey]['list_available_types'] ];
    }

}