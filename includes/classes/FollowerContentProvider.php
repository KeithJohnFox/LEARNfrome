<?php
//Retreives followers Content
class FollowerContentProvider {

    private $con, $userLoggedInObj;

    public function __construct($con, $userLoggedInObj) {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function getVideos() {
        $videos = array();
        
        $subscriptions = $this->userLoggedInObj->getFollowers();
        if(sizeof($subscriptions) > 0) {
            
            //HOW THE LOOP WORKS TO OUTPUT FOLLOWERS TUTORIALS
            // user1, user2, user3..
            // SELECT * FROM videos WHERE uploadedBy = ? OR uploadedBy = ? OR uploadedBy = ? 
            // $query->bindParam(sss..., "user1, user2, user3....");
            
            $condition = "";
            $i = 0;

            while($i < sizeof($subscriptions)) {
                
                if($i == 0) {
                    $condition .= "WHERE uploadedBy=?";
                }
                else {
                    $condition .= " OR uploadedBy=?";
                }
                $i++;
            }

            $videoSql = "SELECT * FROM videos $condition ORDER BY uploadDate DESC";
            $videoQuery = $this->con->prepare($videoSql);
            
            $usernamesArray = array();
            foreach($subscriptions as $sub) {
            $usernamesArray[] = $sub->getUsername();
            }
            
            $types = str_repeat('s', count($usernamesArray)); // repeat 's' for every element in array: ssssss
            $videoQuery->bind_param($types, ...$usernamesArray); // Unpack array of usernames into string
            
            $videoQuery->execute();


            $result = $videoQuery->get_result();
            while($row = mysqli_fetch_assoc($result)) {
                $video = new Video($this->con, $row, $this->userLoggedInObj);
                array_push($videos, $video);
            }

        }

        return $videos;

    }
    
}
?>
