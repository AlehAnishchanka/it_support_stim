<?php
namespace local\stim\it_support_stim\classes;

class ItSupportPositions extends DataElementBitrixList
{
    const BITRIX_LIST_SUPPORT_IT_POSITION = 155;
    public function __construct()
    {
        parent::__construct( self::BITRIX_LIST_SUPPORT_IT_POSITION );
    }
    public function getDataForSelectVue()
    {
        $dataFromNameLists = $this->getArrRecordsList ();
        $dataForSelectVue = [];
        foreach ( $this->arrFromIblockList as $recordId=>$recordValue )
        {
            $dataForSelectVue[] = [ 'value'=>$recordId, "text"=>$dataFromNameLists[ $recordId ]['NAME'], "test_pl"=>$recordValue['POSITION_PL']['VALUE'],];
        }
        return $dataForSelectVue;
    }

    public function getPositionByRecordId( $recordId )
    {
        $arrFilteredNames = array_filter( $this->getDataForSelectVue(), function ( $name ) use ( $recordId ) {
            return $name['value'] == $recordId;
        });
        $firstKey = array_key_first( $arrFilteredNames );
        return [ 'POSITION_RU'=>$arrFilteredNames[$firstKey]['text'], 'POSITION_PL'=>$arrFilteredNames[$firstKey]['test_pl'] ];
    }
}