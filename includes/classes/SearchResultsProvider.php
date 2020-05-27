<?php
class SearchResultsProvider {

    private $con, $userLoggedInObj;

    public function __construct($con, $userLoggedInObj) {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
    }


    public function getVideos($term, $orderBy) {
        //SEARCH QUERY
        // echo $orderBy;

        // $term = strip_tags($term);
        // $term = preg_replace('/[^\p{L}\p{N}\s]/u', '', $term); //Replace these symbols with nothing
        // $term = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $term); //Replace javascript tags and symbols with nothing

        $query = $this->con->prepare("SELECT * FROM videos WHERE title LIKE CONCAT('%', ?, '%')
                                        OR uploadedBy LIKE CONCAT('%', ?, '%') ORDER BY $orderBy DESC");
        $query->bind_param("ss", $term, $orderBy);
        $query->execute();

        $result = $query->get_result();

        $videos = array();
        while($row = mysqli_fetch_assoc($result)) {
            $video = new Video($this->con, $row, $this->userLoggedInObj);
            array_push($videos, $video);
        }

        return $videos;
        

    }

}
?>