<?php
class VideoUploadData {

    public $videoDataArray, $title, $description, $privacy, $category, $uploadedBy;

    //Connstructor taking in variables from VideoDetailsFormProvider Class
    public function __construct($videoDataArray, $title, $description, $privacy, $category, $uploadedBy) {
        $this->videoDataArray = $videoDataArray;
        $this->title = $title;
        $this->description = $description;
        $this->privacy = $privacy;
        $this->category = $category;
        $this->uploadedBy = $uploadedBy;
    }

    public function updateDetails($con, $videoId)
    {
        //Updating Tutorial details SANITIZATION
        $this->title = strip_tags($this->title);  //removes any html tags
        $this->title= mysqli_real_escape_string($this->con, $this->title );

        $this->description = strip_tags($this->description);  //removes any html tags
        $this->description = mysqli_real_escape_string($this->con, $this->description );


       $query = $con->prepare("UPDATE videos SET title= ?, description=?, privacy=?,
                              category=? WHERE id=?");

       $query->bind_param("ssiii", $this->title, $this->description, $this->privacy, $this->category, $videoId);
       
       return $query->execute();


       $title = $this->title;
       $description = $this->description;
       $privacy = $this->privacy;
       $category = $this->category;

       $result = $this->con->prepare("UPDATE videos set title = '$title', description = '$description', privacy = '$privacy', category = '$category' WHERE id='$videoId'");


       return $result;

    }
}
?>
