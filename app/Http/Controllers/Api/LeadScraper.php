<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LeadScraper extends Controller
{
    //
    /**
     * Lead Scrap From Facebook
     * @param Request $request
     * @return
     */
    public function dataScraper(Request $request){

        try {

            //$accessToken  =  "EAAKu5ZBYpE2cBAMOvGwtJwDv96WezHbxpdBbcFnBaqgmiMUNqMCNPAZApA0YuInf5AxHSiThsAIWETluZCPM7Bk4aLnBnAlrZBUChP9Lhoy9IFMbi9xSce8qCaJcaxUOln0lrKZAsjitJZAPTzJs7ay2T3XYhubnb7GNL0geO6DHkWSaZCIwE5EJZB54VxFruxgTr0KbFIGf7AZDZD";

            $accessToken  = "EAAIfArqorGcBAMU8HzLfJ0KNrdHNWqOUZC1hSc2gcTd7ELcnDagLSlBuAInAtUFgPw5VB3PIMSZBIbPTswY9lQQ0K84XaJ5h2Pod01Y2lp1rJmXNamaQW3wFR006U3dGDRB31GfZCWZBxKZATGWVWVZAblnLPpY3XIZCw3EUoLmXbAmDlB9TGfmNXCZBO7z9HVIlzR4G7l0RLQZDZD";
            $url = "https://graph.facebook.com/v15.0/me?fields=id,name,adaccounts{campaigns.limit(10000){name,start_time,stop_time,status,ads{leads.limit(10000)}},business_name}&access_token=".$accessToken;
            $dataArray = json_decode(file_get_contents($url), true);
            $campaignData = [];
            $leads = [];
            $leadFrom = 'fb';

            if($dataArray!="" && count($dataArray)>0){
                if(isset($dataArray['adaccounts']['data']) && count($dataArray['adaccounts']['data'])>0){
                    //dd($dataArray['adaccounts']['data']);
                    $leadDetails = [];
                    foreach ($dataArray['adaccounts']['data'] as $dataCampaign){
                        if(isset($dataCampaign['campaigns']) && count($dataCampaign['campaigns'])>0){ //Start of data scraping
                            $campaignData['account'][$dataCampaign['id']]['business_id'] = $dataCampaign['id'];
                            $campaignData['account'][$dataCampaign['id']]['business_name'] = $dataCampaign['business_name'];
                            if(isset($dataCampaign['campaigns']['data']) && count($dataCampaign['campaigns']['data'])>0){
                                $campaignDetails = [];
                                foreach ($dataCampaign['campaigns']['data'] as $campaign){
                                    if(isset($campaign['ads']['data']) && count($campaign['ads']['data'])>0){
                                        foreach ($campaign['ads']['data'] as $adsData){
                                            if(isset($adsData['leads']['data']) && count($adsData['leads']['data'])>0){
                                               $leadDetailsInfo = [];
                                               $tempArray = [];
                                               foreach ($adsData['leads']['data'] as $lead){

                                                   if(isset($lead['field_data']) && count($lead['field_data'])>0){

                                                       foreach ($lead['field_data'] as $fieldData){
                                                           $tempArray['lead'][$lead['id']]['name'][]=$fieldData['name'];
                                                           $tempArray['lead'][$lead['id']]['data'][]=$fieldData;
                                                       }

                                                       if(!in_array('inbox_url', $tempArray['lead'][$lead['id']]['name'])){
                                                           if(count($tempArray['lead'][$lead['id']]['data'])>0){
                                                               foreach ($tempArray['lead'][$lead['id']]['data'] as $fieldValue){
                                                                   if (strlen(stristr($fieldValue['name'],"live_in"))>0) {

                                                                       $leadDetailsInfo['lead'][$lead['id']]['work_location']= $fieldValue['values'][0];
                                                                   }
                                                                   if (strlen(stristr($fieldValue['name'],"qualification_are_you"))>0) {

                                                                       $courseCodeArray = explode('_', $this->_cleanString($fieldValue['values'][0]));
                                                                       $leadDetailsInfo['lead'][$lead['id']]['course_title']= $this->_cleanString(str_replace("_", "",$fieldValue['values'][0]));
                                                                       foreach ($courseCodeArray as $courseCodeString){
                                                                           $isThereNumber = false;
                                                                           for ($i = 0; $i < strlen($courseCodeString); $i++) {
                                                                               if ( ctype_digit($courseCodeString[$i]) ) {
                                                                                   $isThereNumber = true;
                                                                                   break;
                                                                               }
                                                                           }
                                                                           if($isThereNumber){
                                                                               $leadDetailsInfo['lead'][$lead['id']]['course_code']= trim(rtrim($courseCodeString, '-'));
                                                                           }
                                                                       }
                                                                   }
                                                                   // User Info
                                                                   if (strlen(stristr($fieldValue['name'],"full_name"))>0) {
                                                                       $leadDetailsInfo['lead'][$lead['id']]['full_name']= $fieldValue['values'][0];
                                                                   }
                                                                   if (strlen(stristr($fieldValue['name'],"phone_number"))>0) {
                                                                       $leadDetailsInfo['lead'][$lead['id']]['phone_number']= $fieldValue['values'][0];
                                                                   }
                                                                   if (strlen(stristr($fieldValue['name'],"email"))>0) {
                                                                       $leadDetailsInfo['lead'][$lead['id']]['email']= $fieldValue['values'][0];
                                                                   }
                                                               }

                                                           }
                                                           $leadDetailsInfo['lead'][$lead['id']]['lead_id']=$lead['id'];
                                                           $leadDetailsInfo['lead'][$lead['id']]['lead_apply_date']=$lead['created_time'];
                                                           $leadDetailsInfo['lead'][$lead['id']]['form_data']= $lead['field_data'];
                                                       }
                                                   }
                                               }
                                                $campaignDetails['campaign_name'] = $campaign['name'];
                                                $campaignDetails['campaign_id'] = $campaign['id'];
                                                $campaignDetails['start_time'] = $campaign['start_time'];
                                                $campaignDetails['stop_time'] = isset($campaign['stop_time'])?$campaign['stop_time']:$campaign['start_time'];
                                                $campaignDetails['campaign_status'] = $campaign['status'];
                                                $leadDetails['campaign'][$campaign['id']]['campaign_details'] = $campaignDetails;
                                                $leadDetails['campaign'][$campaign['id']]['campaign_leads'] = $leadDetailsInfo;
                                                $leadDetails['lead_from'] = $leadFrom;
                                            }
                                        }
                                    }
                                    $leads['account'][$dataCampaign['id']]=$leadDetails;
                                }
                            }
                        } // EOF data scraping
                    }
                }
            }

//            $response = Http::post('http://leadapp.crm.com/api/lead/create', [
//                'name' => 'Steve',
//                'role' => 'Network Administrator',
//            ]);
//            dd(json_decode($response->body(), true) );

           // dd($leads);
            return response()->json([
                'status' => true,
                'message' => 'Data Scrap Successfully',
                'data' => $leads
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }

    }

    private function _cleanString($text) {
        $utf8 = array(
            '/[áàâãªä]/u'   =>   'a',
            '/[ÁÀÂÃÄ]/u'    =>   'A',
            '/[ÍÌÎÏ]/u'     =>   'I',
            '/[íìîï]/u'     =>   'i',
            '/[éèêë]/u'     =>   'e',
            '/[ÉÈÊË]/u'     =>   'E',
            '/[óòôõºö]/u'   =>   'o',
            '/[ÓÒÔÕÖ]/u'    =>   'O',
            '/[úùûü]/u'     =>   'u',
            '/[ÚÙÛÜ]/u'     =>   'U',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N',
            '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
            '/[“”«»„]/u'    =>   ' ', // Double quote
            '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
            '/¬/'             => ''
        );
        return preg_replace(array_keys($utf8), array_values($utf8), $text);
    }
}
