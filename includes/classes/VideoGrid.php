<?php
require_once("includes/classes/Video.php"); 
//Video Layout Class
class VideoGrid {

    //Private Variables used
    private $con, $userLoggedInObj;
    private $largeMode = false;
    private $gridClass = "videoGrid";

    //Constructor
    public function __construct($con, $userLoggedInObj) {
        $this->con = $con;
        $this->userLoggedInObj = $userLoggedInObj;
    }

    public function create($videos, $title, $showFilter) {

        //If tutorials are specified eg (genre / follower videos) run this, speciefied by $videos
        if($videos == null) {
            $gridItems = $this->generateItems();
        }
        //No tutorial specified run this ($videos returns null)
        else {
            $gridItems = $this->generateItemsFromVideos($videos);
        }

        $header = "";

        //If title is null then tutorials are displayed in suggestions box in watch page
        if($title != null) {
            $header = $this->createGridHeader($title, $showFilter);
        }

        //Returns tutorials in grid Items
        return "$header
                <div class='$this->gridClass'>
                    $gridItems
                </div>";
    }

    

    //Output Coding Videos
    public function codeCategory($videos, $title, $showFilter) {

        //If tutorials are specified eg (genre / follower videos) run this, speciefied by $videos
        if($videos == null) {
            $gridItems = $this->generateCodeItems();
        }
        //No tutorial specified run this ($videos returns null)
        else {
            $gridItems = $this->generateItemsFromVideos($videos);
        }

        $header = "";

        //If title is null then tutorials are displayed in suggestions box in watch page
        if($title != null) {
            $header = $this->createGridHeader($title, $showFilter);
        }

        //Returns tutorials in grid Items
        return "$header
                <div class='$this->gridClass'>
                    $gridItems
                </div>";
    }

    //Output Design Videos
    public function designCategory($videos, $title, $showFilter) {

        //If tutorials are specified eg (genre / follower videos) run this, speciefied by $videos
        if($videos == null) {
            $gridItems = $this->generateDesignItems();
        }
        //No tutorial specified run this ($videos returns null)
        else {
            $gridItems = $this->generateItemsFromVideos($videos);
        }

        $header = "";

        //If title is null then tutorials are displayed in suggestions box in watch page
        if($title != null) {
            $header = $this->createGridHeader($title, $showFilter);
        }

        //Returns tutorials in grid Items
        return "$header
                <div class='$this->gridClass'>
                    $gridItems
                </div>";
    }

    //Output Development Videos
    public function developmentCategory($videos, $title, $showFilter) {

        //If tutorials are specified eg (genre / follower videos) run this, speciefied by $videos
        if($videos == null) {
            $gridItems = $this->generateDevelopmentItems();
        }
        //No tutorial specified run this ($videos returns null)
        else {
            $gridItems = $this->generateItemsFromVideos($videos);
        }

        $header = "";

        //If title is null then tutorials are displayed in suggestions box in watch page
        if($title != null) {
            $header = $this->createGridHeader($title, $showFilter);
        }

        //Returns tutorials in grid Items
        return "$header
                <div class='$this->gridClass'>
                    $gridItems
                </div>";
    }

    //Output Cooking Videos
    public function cookingCategory($videos, $title, $showFilter) {

        //If tutorials are specified eg (genre / follower videos) run this, speciefied by $videos
        if($videos == null) {
            $gridItems = $this->generateCookingItems();
        }
        //No tutorial specified run this ($videos returns null)
        else {
            $gridItems = $this->generateItemsFromVideos($videos);
        }

        $header = "";

        //If title is null then tutorials are displayed in suggestions box in watch page
        if($title != null) {
            $header = $this->createGridHeader($title, $showFilter);
        }

        //Returns tutorials in grid Items
        return "$header
                <div class='$this->gridClass'>
                    $gridItems
                </div>";
    }

    //Output Business Videos
    public function businessCategory($videos, $title, $showFilter) {

        //If tutorials are specified eg (genre / follower videos) run this, speciefied by $videos
        if($videos == null) {
            $gridItems = $this->generateBusinessItems();
        }
        //No tutorial specified run this ($videos returns null)
        else {
            $gridItems = $this->generateItemsFromVideos($videos);
        }

        $header = "";

        //If title is null then tutorials are displayed in suggestions box in watch page
        if($title != null) {
            $header = $this->createGridHeader($title, $showFilter);
        }

        //Returns tutorials in grid Items
        return "$header
                <div class='$this->gridClass'>
                    $gridItems
                </div>";
    }

    public function generateCodeItems() {
        $query = $this->con->prepare("SELECT * FROM videos WHERE category = 1 ORDER BY views DESC");
        $query->execute();
        $result = $query->get_result();
        
        $elementsHtml = "";
        while($row = mysqli_fetch_assoc($result)) {

            $video = new Video($this->con, $row, $this->userLoggedInObj);
            $item = new VideoGridItem($video, $this->largeMode);
            $elementsHtml .= $item->create();
        }

        return $elementsHtml;
    }

    public function generateBusinessItems() {
        $query = $this->con->prepare("SELECT * FROM videos WHERE category = 3 ORDER BY views DESC");
        $query->execute();
        $result = $query->get_result();
        
        $elementsHtml = "";
        while($row = mysqli_fetch_assoc($result)) {

            $video = new Video($this->con, $row, $this->userLoggedInObj);
            $item = new VideoGridItem($video, $this->largeMode);
            $elementsHtml .= $item->create();
        }

        return $elementsHtml;
    }

    public function generateDesignItems() {
        $query = $this->con->prepare("SELECT * FROM videos WHERE category = 4 ORDER BY views DESC");
        $query->execute();
        $result = $query->get_result();
        
        $elementsHtml = "";
        while($row = mysqli_fetch_assoc($result)) {

            $video = new Video($this->con, $row, $this->userLoggedInObj);
            $item = new VideoGridItem($video, $this->largeMode);
            $elementsHtml .= $item->create();
        }

        return $elementsHtml;
    }

    public function generateDevelopmentItems() {
        $query = $this->con->prepare("SELECT * FROM videos WHERE category = 5 ORDER BY views DESC");
        $query->execute();
        $result = $query->get_result();
        
        $elementsHtml = "";
        while($row = mysqli_fetch_assoc($result)) {

            $video = new Video($this->con, $row, $this->userLoggedInObj);
            $item = new VideoGridItem($video, $this->largeMode);
            $elementsHtml .= $item->create();
        }

        return $elementsHtml;
    }

    public function generateCookingItems() {
        $query = $this->con->prepare("SELECT * FROM videos WHERE category = 2 ORDER BY views DESC");
        $query->execute();
        $result = $query->get_result();
        
        $elementsHtml = "";
        while($row = mysqli_fetch_assoc($result)) {

            $video = new Video($this->con, $row, $this->userLoggedInObj);
            $item = new VideoGridItem($video, $this->largeMode);
            $elementsHtml .= $item->create();
        }

        return $elementsHtml;
    }

    //Genereates tutorial listing
    public function generateItems() {
        $query = $this->con->prepare("SELECT * FROM videos ORDER BY RAND() LIMIT 15");
        $query->execute();
        $result = $query->get_result();
        
        $elementsHtml = "";
        while($row = mysqli_fetch_assoc($result)) {

            $video = new Video($this->con, $row, $this->userLoggedInObj);
            $item = new VideoGridItem($video, $this->largeMode);
            $elementsHtml .= $item->create();
        }

        return $elementsHtml;
    }

    public function generateItemsFromVideos($videos) {
        $elementsHtml = "";

        foreach($videos as $video) {
            $item = new VideoGridItem($video, $this->largeMode);
            $elementsHtml .= $item->create();
        }

        return $elementsHtml;
    }
    
    //Execute for Tutorials page
    public function createGridHeader($title, $showFilter) {
        //Filter Tutorials
        $filter = "";

        if($showFilter) {
            $link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            
            $urlArray = parse_url($link);
            $query = $urlArray["query"];

            parse_str($query, $params);

            unset($params["orderBy"]);
            
            $newQuery = http_build_query($params);

            $newUrl = basename($_SERVER["PHP_SELF"]) . "?" . $newQuery;
           
            $filter = "<div class='right'>
                            <span>Order by:</span>
                            <a href='$newUrl&orderBy=uploadDate'>Upload date</a>
                            <a href='$newUrl&orderBy=views'>Most viewed</a>
                        </div>";
        }

        return "<div class='videoGridHeader'>
                        <div class='left'>
                            $title
                        </div>
                        $filter
                    </div>";
    }

    public function createLarge($videos, $title, $showFilter) {
        $this->gridClass .= " large";
        $this->largeMode = true;
        return $this->create($videos, $title, $showFilter);
    }

    public function getCategoryTutorials($category, $videos, $title, $showFilter) {
        
        if($videos == null) {
            $gridItems = $this->generateCategoryItems($category);
        }

        $header = "";

        //If title is null then tutorials are displayed in suggestions box in watch page
        if($title != null) {
            $header = $this->createGridHeader($title, $showFilter);
        }
            //Returns tutorials in grid Items
            return "$header
            <div class='$this->gridClass'>
                $gridItems
            </div>"; 
    }


    public function generateCategoryItems($category) {
        $cat = $category;
        $query = $this->con->prepare("SELECT * FROM videos WHERE category=? ORDER BY views DESC");
        $query->bind_param("i", $cat);
        $query->execute();
        $result = $query->get_result();
        
        $elementsHtml = "";
        while($row = mysqli_fetch_assoc($result)) {

            $video = new Video($this->con, $row, $this->userLoggedInObj);
            $item = new VideoGridItem($video, $this->largeMode);
            $elementsHtml .= $item->create();
        }

        return $elementsHtml;
    }

}

?>