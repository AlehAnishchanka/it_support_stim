<?php
namespace local\stim\it_support_stim\classes;
class ItSupportSection extends DataElementBitrixList
{
    const BITRIX_LIST_SUPPORT_SECTION_IT = 146;
    public function __construct()
    {
        parent::__construct( self::BITRIX_LIST_SUPPORT_SECTION_IT );
    }

    public function getDataForSelectVue()
    {
        $dataFromNameLists = $this->getArrRecordsList ();
        $dataForSelectVue = [];
        foreach ( $this->arrFromIblockList as $recordId=>$recordValue ) 
        {
            //print_r($recordValue);
            $dataForSelectVue[] = [ 'value'=>$recordValue['ID'], "text"=>$dataFromNameLists[$recordId]['NAME'], 'text_pl'=>$recordValue['NAZVANIE_PL']['VALUE'], 
                                    "list_available_types"=>$recordValue["VALIDE_TIPES_APPEAL"]["VALUE"],];
        }
        return $dataForSelectVue;
    }

    public function getNameSectionByRecordId( $recordId )
    {
        $arrFilteredNames = array_filter( $this->getDataForSelectVue(), function ( $name ) use ( $recordId ) {
            //print_r($name['value']==$recordId );
            return $name['value'] == $recordId;
        });
        return [ 'NAME'=>$arrFilteredNames[0]['text'], 'NAME_PL'=>$arrFilteredNames[0]['text_pl'] ];
    }

    public function getSectionAndSubjectForSelecVue ( ItSupportSubject $subjectObj )
    {
        $arrDataSubject = $subjectObj->getDataForSelectVue();
        $arrDataSection = $this->getDataForSelectVue();
        $arrDataSectionAndSubject = [];
        foreach( $arrDataSection as $section ) {
            $arrDataSectionAndSubject[] = $section;
            $arrFilteredSubjectsBySectionId = array_filter( $arrDataSubject , function ( $subject ) use ( $section ) {
                return $subject['section_id'] == $section['value'];
            });
            foreach( $arrFilteredSubjectsBySectionId as $subject )
            $arrDataSectionAndSubject[] = $subject;
        }
        return $arrDataSectionAndSubject;
    }

    public function ifRecordExists( $recordId )
    {
        $arrFilteredRecords = array_filter( $this->getDataForSelectVue(), function ( $name ) use ( $recordId ) {
            //print_r($name['value']==$recordId );
            return $name['value'] == $recordId;
        });
        return count( $arrFilteredRecords )>0;
    }

    public function getSubjectByRecordId( $recordId )
    {
        $arrFilteredNames = array_filter( $this->getDataForSelectVue(), function ( $name ) use ( $recordId ) {
            //print_r($name['value']==$recordId );
            return $name['value'] == $recordId;
        });
        $firstKey = array_key_first( $arrFilteredNames );
        return [ 'TEKST_RU'=>$arrFilteredNames[$firstKey]['text'], 'TEKST_PL'=>$arrFilteredNames[$firstKey]['text_pl'] ];
    }

    public function ifBpexists( $subjectId ) {
        $data = $this->getData()[ $subjectId ];
        return $data['ID_BP_COORDINATION']['VALUE'] && $data['ID_TEMPLATE_BP']['VALUE'] && $data['APPROVING_USER']['VALUE'];
    }

    public function getBpId( $subjectId ) 
    {
        $data = $this->getData()[ $subjectId ];
        return $data['ID_BP_COORDINATION']['VALUE'];
    }

    public function getBpTemplateId( $subjectId ) 
    {
        $data = $this->getData()[ $subjectId ];
        return $data['ID_TEMPLATE_BP']['VALUE'];
    }

    public function getApprovingUserIdId( $subjectId ) 
    {
        $data = $this->getData()[ $subjectId ];
        return $data['APPROVING_USER']['VALUE'];
    }

}