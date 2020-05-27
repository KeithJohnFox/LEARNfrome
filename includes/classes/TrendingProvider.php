<?php
class TrendingProvider {

    //Private Variables
    private $con, $userLoggedInObj;

    //Constructor
    public function __construct($con, $userLoggedInObj) {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    //Retreive Trending Videos
    public function getVideos() {
        $videos = array();

        //Trending Query - Selects videos uploaded last 7 days ordered by most views with a limit of 15 videos
        $query = $this->con->prepare("SELECT * FROM videos WHERE uploadDate >= now() - INTERVAL 7 DAY 
                                        ORDER BY views DESC LIMIT 15"); //now() gets the current time and date
        $query->execute();
        $result = $query->get_result();

        while($row = mysqli_fetch_assoc($result)) {
            $video = new Video($this->con, $row, $this->userLoggedInObj);
            array_push($videos, $video);
        }

        return $videos;
    }
}
?>