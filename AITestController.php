<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class APITestController extends FrontendController
{
    
    private function triggerAPI(String $urlEndPoint){
        // EndPoint Documentation:  https://www.openproject.org/docs/api/endpoints/
        // OpenProject server IP
        $url = "http://192.168.1.2:8081/";
        
        // String concatination
        $url .= $urlEndPoint;
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // Basic Auth (username:password/API Key) [OpenPorject Admin User]
        // "apikey: 9717809fbe46c8378ed4da240c730a745965d29477881c2fecf61b77eab8cf1b"
        //
        $headers = array(
           "Authorization: Basic YXBpa2V5Ojk3MTc4MDlmYmU0NmM4Mzc4ZWQ0ZGEyNDBjNzMwYTc0NTk2NWQyOTQ3Nzg4MWMyZmVjZjYxYjc3ZWFiOGNmMWI=",
        );
        
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $resp = curl_exec($curl);
        curl_close($curl);
        
        // var_dump($resp);
        
        return json_decode($resp) ; 
        // return $resp ; 
    }
    
    /**
     * @Template()
     */    
    public function defaultAction(Request $request){
        
        $allProjects = $this->triggerAPI("/api/v3/projects");
        // [Sample] http://192.168.1.2:8081//api/v3/projects

        $allProjectInfo = [];
        foreach($allProjects->_embedded->elements as $project){
            
            $projectInfo = $this->triggerAPI($project->_links->self->href);
            // [Sample] http://192.168.1.2:8081//api/v3/projects/6
            
            // Get project members data
            $projectMemberships = $this->triggerAPI($projectInfo->_links->memberships->href);
            // [Sample] http://192.168.1.2:8081//api/v3/memberships?filters=%5B%7B%22project%22%3A%7B%22operator%22%3A%22%3D%22%2C%22values%22%3A%5B%226%22%5D%7D%7D%5D
            
            // Get recent project task data
            $projectWorkPackages = $this->triggerAPI( $projectInfo->_links->workPackages->href .'?filters=[]&pageSize=100');
            // [Sample] http://192.168.1.2:8081//api/v3/projects/6/work_packages?filters=[]&pageSize=100
            
            // $projectWorkPackages = $this->triggerAPI( $projectInfo->_links->workPackages->href);
            // [Sample] http://192.168.1.2:8081//api/v3/projects/6/work_packages



            // echo $project; 
            // echo $projectInfo->_links->workPackages->href .'?filters=[]';
            // echo json_encode($projectWorkPackages); 
            // echo"<br>";
            
            // Get project members name and their role
            $memberInfo = [];
            foreach($projectMemberships->_embedded->elements as $member){
                if ($member->_links->self->title <> 'System'){
                    array_push($memberInfo,[
                        "memberName" => $member->_links->self->title,
                        "memberRole" => array_values($member->_links->roles)[0]->title,
                    ]);
                }
            }
            
            // Get important project's task details
            $taskInfo = [];
            foreach($projectWorkPackages->_embedded->elements as $task){
                if(property_exists($task,"date")){
                    $date = [
                        "date"=> $task->date,
                    ]; 
                }
                else{
                    $date = [
                            "startDate" => $task->startDate,
                            "dueDate" =>  $task->dueDate,
                        ];
                }
                array_push($taskInfo,[
                        "taskName" => $task->subject,
                        "description" =>  $task->description->raw,
                        "createdAt" => $task -> createdAt, 
                        "author" =>  $task->_links->author->title,
                        "type" =>  $task->_links->type->title,
                        "version" =>  $task->_links->version->title,
                        "status" =>  $task->_links->status->title,
                        "priority" =>  $task->_links->priority->title,
                        "date" => $date,
                        "estimatedTime" => $task->estimatedTime,
                    ]);
            }
            // The count below should match the totalTask inside info
            // echo count($taskInfo);   
            // echo "<br>";
            
            $info =[
                "name" => $projectInfo->name,
                "status" => $projectInfo->_links->status->title,
                "active" => $projectInfo->active,
                "createdAt" => $projectInfo -> createdAt, 
                "updatedAt" => $projectInfo->updatedAt,
                "projectDescription" => $projectInfo->description->raw,
                "statusDescription" => $projectInfo->statusExplanation->raw,
                "memberInfo" => $memberInfo, 
                "taskInfo" => $taskInfo,
                "totalTask" => $projectWorkPackages->total,
                ];

            array_push($allProjectInfo, $info);
        }
        
        foreach($allProjectInfo as $projectInfo){
            echo(json_encode($projectInfo));
            echo "<br>";
            echo "<br>";
        }
        // echo json_encode($allProjectInfo);
        
        
        return $this->render('APITest.html.twig',[
            
            ]);
    }
    
    

}AITestController.php
