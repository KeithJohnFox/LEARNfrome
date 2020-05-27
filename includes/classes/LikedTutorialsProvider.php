<?php
class LikedTutorialsProvider {

    private $con, $userLoggedInObj;

    public function __construct($con, $userLoggedInObj) {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function getVideos() {
        $videos = array();

        $query = $this->con->prepare("SELECT videoId FROM video_likes WHERE username=? AND commentId=0
                                        ORDER BY id DESC");
        $query->bind_param("s", $username);
        $username = $this->userLoggedInObj->getUsername();
        $query->execute();
        $result = $query->get_result();

        while($row = mysqli_fetch_assoc($result)) {
            $videos[] = new Video($this->con, $row["videoId"], $this->userLoggedInObj);
        }

        return $videos;
    }
}
?>