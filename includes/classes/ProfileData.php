<?php
class ProfileData {
    
    private $con, $profileUserObj;

    public function __construct($con, $profileUsername) {
        $this->con = $con;
        $this->profileUserObj = new User($con, $profileUsername);
    }

    //User Profile Object
    public function getProfileUserObj() {
        return $this->profileUserObj;
    }

    //Username
    public function getProfileUsername() {
        return $this->profileUserObj->getUsername();
    }

    //DO they exist?
    public function userExists() {
        $stmt = $this->con->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $profileUsername);
        $profileUsername = $this->getProfileUsername();

        $stmt->execute();

        $stmt->store_result();
        $query = $stmt->num_rows;

        return $query != 0;
    }

    //Default Profile Banner Picture
    public function getCoverPhoto() {
        return "assets/images/backgrounds/cover-photo.jpg";
    }

    public function getProfileUserFullName() {
        return $this->profileUserObj->getFirstAndLastName();
    }

    public function getProfilePic() {
        return $this->profileUserObj->getProfilePic();
    }

    public function getFollowerCount() {
        return $this->profileUserObj->getFollowerCount();
    }

    public function getUsersVideos() {
        $query = $this->con->prepare("SELECT * FROM videos WHERE uploadedBy=? ORDER BY uploadDate DESC");
        $query->bind_param("s", $username);
        $username = $this->getProfileUsername();
        $query->execute();

        $videos = array();
        $result = $query->get_result();

        while($row = mysqli_fetch_assoc($result)) {
            $videos[] = new Video($this->con, $row, $this->profileUserObj->getUsername());
        }
        return $videos;
    }


    public function getTotalViews() {
        $query = $this->con->prepare("SELECT sum(views) FROM videos WHERE uploadedBy=?");
        $query->bind_param("s", $username);
        $username = $this->getProfileUsername();
        $query->execute();

        $result = $query->get_result();

        $data = mysqli_fetch_array($result);

        return $data["views"];
    }

    private function getSignUpDate() {
        $date = $this->profileUserObj->getSignUpDate();
        return date("F jS, Y", strtotime($date));
    }
}
?>