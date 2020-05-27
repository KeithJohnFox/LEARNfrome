<?php
class VideoDetailsFormProvider
{

    private $con;

//Constructor for connection
    public function __construct($con)
    {
        $this->con = $con;
    }

    public function createUploadForm()
    {
        //All variables for each input box
        $fileInput = $this->createFileInput();
        $titleInput = $this->createTitleInput(null);
        $descriptionInput = $this->createDescriptionInput(null);
        $categoriesInput = $this->createCategoriesInput(null);
        $privacyInput = $this->createPrivacyInput(null);
        $uploadButton = $this->createUploadButton();

        //returns variable data from each input box
        return "<form action='processing.php' method='POST' enctype='multipart/form-data'>
                $fileInput
                $titleInput
                $descriptionInput
                $privacyInput
                $categoriesInput
                $uploadButton
            </form>";
    }

    //EDIT TUTORIAL PAGE, CHANGE DETAILS ON TUTORIAL
    public function createEditDetailsForm($video) {
        $titleInput = $this->createTitleInput($video->getTitle());
        $descriptionInput = $this->createDescriptionInput($video->getDescription());
        $privacyInput = $this->createPrivacyInput($video->getPrivacy());
        $categoriesInput = $this->createCategoriesInput($video->getCategory());
        $saveButton = $this->createSaveButton();
        return "<form method='POST'>
                    $titleInput
                    $descriptionInput
                    $privacyInput
                    $categoriesInput
                    $saveButton
                </form>";
    }

    //Create file input function for Video
    private function createFileInput() {
        //return bootstrap upload button div
        return "<div class='form-group'>
                    <label for='exampleFormControlFile1'>Add Your Tutorial Video File</label>
                    <input type='file' class='form-control-file' id='exampleFormControlFile1' name='fileInput' required> <!--required they have to submit a video to submit form -->
                </div>";
    }

    private function createDescriptionInput($value) {
        if($value == null) $value = "";
        return "<div class='form-group'>
                    <textarea class='form-control' placeholder='Description' name='descriptionInput' rows='3'>$value</textarea>
                </div>";
    }

    //Tutorial title input
    private function createTitleInput($value) {
        if($value == null) $value = "";
        return "<div class='form-group'>
                    <input class='form-control' type='text' placeholder='Title' name='titleInput' value='$value'>
                </div>";
    }

    //Tutorial Categorie Input
    private function createCategoriesInput($value) {
        if($value == null) $value = "";

        $html = "<div class='form-group'>
                    <select class='form-control' name='categoryInput'>";

        //query category's
        $query = mysqli_query($this->con, "Select * FROM categories");

        //Loops through category's table by name and id
        while($row = mysqli_fetch_array($query)){
            $category = $row["name"];
            $id = $row["id"];
            $selected = ($id == $value) ? "selected='selected'" : "";

            $html .= "<option $selected value='$id'>$category</option>";

        }

        $html .= "</select>
                </div>";

        return $html;

    }

    //Set privacy on tutorial will be displayed to public or not
    private function createPrivacyInput($value) {
        if($value == null) $value = "";

        $privateSelected = ($value == 0) ? "selected='selected'" : "";
        $publicSelected = ($value == 1) ? "selected='selected'" : "";
        return "<div class='form-group'>
                   <!-- section box, 2 options private or public -->
                    <select class='form-control' name='privacyInput'>
                        <option value='0' $privateSelected>Private</option>
                        <option value='1' $publicSelected>Public</option>
                    </select>
                </div>";
    }

    //Upload tutorial button
    private function createUploadButton() {
        return "<button type='submit' class='btn btn-primary' name='uploadButton'>Upload Tutorial</button>";
    }
    

    // Save button
    private function createSaveButton() {
        return "<button type='submit' class='btn btn-primary' name='saveButton'>Save</button>";
    }

}
?>



