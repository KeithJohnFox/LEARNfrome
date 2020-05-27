<?php
class NavigationMenuProvider {

    private $con, $userLoggedInObj;

    public function __construct($con, $userLoggedInObj) {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function create() {
        $menuHtml = $this->createFollowersSection();

        

        return "<div class='followerItems'>
                    $menuHtml
                </div>";
    }

    private function createFollowerItem($text, $icon, $link) {
        return "<div class='followerItem'>
                    <a href='$link'>
                        <img src='$icon'>
                        <span>$text</span>
                    </a>
                </div>";
    }


    private function createFollowersSection() {
        $followers = $this->userLoggedInObj->getFollowers();

        $html = "<span class='heading'></span>";
        foreach($followers as $follower) {
            $followerUsername = $follower->getUsername();
            $html .= $this->createFollowerItem($followerUsername, $follower->getProfilePic(), "$followerUsername");
        }
        return $html;
    }

}
?>