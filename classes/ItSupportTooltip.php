<?php
namespace local\stim\it_support_stim\classes;
class ItSupportTooltip extends DataElementBitrixList
{
    const BITRIX_LIST_SUPPORT_IT_APPEAL_TYPE_TOOLTIP = 150;
    public function __construct()
    {
        parent::__construct( self::BITRIX_LIST_SUPPORT_IT_APPEAL_TYPE_TOOLTIP );
    }

    public function getDataForSelectVue()
    {
        $dataFromNameLists = $this->getArrRecordsList ();
        $dataForSelectVue = [];
        foreach ( $this->arrFromIblockList as $recordId=>$recordValue ) 
        {
            $dataForSelectVue[] = [ 'value'=>$recordValue['ID'], "text"=>$dataFromNameLists[$recordId]['NAME'], "text_pl"=>$recordValue['PODSKAZKA_PL']['VALUE'], 
                                    "appeal_rype_id"=>$recordValue['TIP_OBRASHCHENIYA']['VALUE'] ];
        }
        return $dataForSelectVue;
    }

    public function getSubjectByRecordId( $recordId )
    {
        $arrFilteredNames = array_filter( $this->getDataForSelectVue(), function ( $name ) use ( $recordId ) {
            return $name['value'] == $recordId;
        });
        $firstKey = array_key_first( $arrFilteredNames );
        return [ 'TEKST_RU'=>$arrFilteredNames[$firstKey]['text'], 'TEKST_PL'=>$arrFilteredNames[$firstKey]['text_pl'] ];
    }

}