<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;


class HomeController extends Controller  {

    /*
     * show index page site
     */
    private $key = '02954fbf747984c907566c9320533511';

    public function showPage()
    {
        $lead_id  = (string)$this->addLead()->result->recorddetail->FL[0];
        $message = ((string)$this->addTaskLead($lead_id)->result->message=="Record(s) added successfully") ? "Task added successfully" : "Error adding task: ".(string)$this->addTaskLead($lead_id)->error->message;
        return view('index', compact("message"));
    }

    private function getData($url,$param)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $param,
        ));
        $response = curl_exec($curl);

        curl_close($curl);

        return simplexml_load_string($response);
    }

    public function addLead($scope = 'crmapi')
    {

        $xml = '<Leads>
<row no="1">
<FL val="Lead Source">Web Download</FL>
<FL val="Company">Your Company</FL>
<FL val="First Name">Hannah</FL>
<FL val="Last Name">Smith</FL>
<FL val="Email">testing@testing.com</FL>
<FL val="Title">Manager</FL>
<FL val="Phone">1234567890</FL>
<FL val="Home Phone">0987654321</FL>
<FL val="Other Phone">1212211212</FL>
<FL val="Fax">02927272626</FL>
<FL val="Mobile">292827622</FL>
</row>
</Leads>';
        $url = 'https://crm.zoho.com/crm/private/xml/Leads/insertRecords';
        $param= "authtoken=".$this->key."&scope=".$scope."&newFormat=1&xmlData=".$xml;

        return $this->getData($url,$param);

    }
    public function addTaskLead($lead_id,$scope = 'crmapi') {

        $xml = '<Tasks>
<row no="1">
<FL val="Task Owner">atom21@yandex.ru</FL>
<FL val="Subject">Demo Call</FL>
<FL val="Due Date">11/23/2018</FL>
<FL val="SEID">'.$lead_id.'</FL>
<FL val="SEMODULE">Leads</FL>
<FL val="Status">In Progress</FL>
<FL val="Priority">Highest</FL>
<FL val="Send Notification Email">false</FL>
<FL val="Description">Sample Desc</FL>
</row>
</Tasks>';
        $url = 'https://crm.zoho.com/crm/private/xml/Tasks/insertRecords';
        $param= "authtoken=".$this->key."&scope=".$scope."&newFormat=1&xmlData=".$xml;

        return $this->getData($url,$param);

    }

}