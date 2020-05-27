<?php
class VideoProcessor {

    private $con;
    private $sizeLimit = 500000000; //5 million bytes or 500 mb
    //All file types supported
    private $allowedTypes = array("mp4", "flv", "webm", "mkv", "vob", "ogv", "ogg", "avi", "wmv", "mov", "mpeg", "mpg");
    private $ffprobePath = "ffmpeg/bin/ffprobe"; // ffprobe file directory
    private $ffmpegPath = "ffmpeg/bin/ffmpeg"; // ffmpeg file directory

    public function __construct($con) {
        $this->con = $con;
    }

    //Upload Tutorial Function
    public function upload($videoUploadData) {

//!!!!!!!!!!!!!!!!!!-UPLOAD-FILE-PATH-FOR-VIDEOS-!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $targetDir = "uploads/videos/";
        //get videoDataArray from videoUploadData Class
        $videoData = $videoUploadData->videoDataArray;

        //TempFile path used to stored videos before the video format is changed to .mp4
        //tempFilePath = "uploads/videos/", 5a678e658f9c, "name of video"
        $tempFilePath = $targetDir . uniqid() . basename($videoData["name"]);
        //get rid of any spaces to underscore in file path
        $tempFilePath = str_replace(" ", "_", $tempFilePath);

        $isValidData = $this->processData($videoData, $tempFilePath);

        //If validData has any error meaning it's false not true or = 1, then return false,
        if(!$isValidData) {
            return false;
        }

        //************If data is error free then we execute this code to move video file into the Video Folder to be stored***************
        //tempFilePath is the path file where the video will be stored
        if(move_uploaded_file($videoData["tmp_name"], $tempFilePath)) {
            //FinalFilePath will only have .mp4 converted videos to suit all web browsers
            $finalFilePath = $targetDir . uniqid() . ".mp4";

            //If its failed output error
            if(!$this->insertVideoData($videoUploadData, $finalFilePath)) {
                echo "Insert query failed\n";
                return false;
            }

            //Upload failed Message due to convert video format
            if(!$this->convertVideoToMp4($tempFilePath, $finalFilePath)) {
                echo "Upload failed\n";
                return false;
            }

            if(!$this->generateThumbnails($finalFilePath)) {
                echo "Upload failed: thumbnails could not be generated\n";
                return false;
            }
            
            //delete file error
            if(!$this->deleteFile($tempFilePath)) {
                echo "Upload failed\n";
                return false;
            }

            return true;
        }
    }

    //Processing video type from upload
    private function processData($videoData, $filePath) {
        $videoType = pathInfo($filePath, PATHINFO_EXTENSION);

        //ERROR HANDLING
        if(!$this->isValidSize($videoData)) {
            echo "File too large. Max Size 500MB";
            return false;
        }
        else if(!$this->isValidType($videoType)) {
            echo "Invalid file type";
            return false;
        }

        //If 1 is returned meaning it has an error then output error message
        else if($this->hasError($videoData)) {
            echo "Error code: " . $videoData["error"];
            return false;
        }
        //If theres no errors then return true
        return true;
    }

    //Check if the video upload size isnt over 500MB
    private function isValidSize($data) {
        return $data["size"] <= $this->sizeLimit;
    }

    private function isValidType($type) {
        $lowercased = strtolower($type);
        return in_array($lowercased, $this->allowedTypes);
    }

    //Check if error returns 1 then theres an error, if theres no error should = 0
    private function hasError($data) {
        return $data["error"] != 0;
    }

    private function insertVideoData($uploadData, $filePath) {
        $views = 0;

        //TUTORIAL UPLOAD SANITIZATION
        $uploadData->title = strip_tags($uploadData->title);  //removes any html tags
        $uploadData->title  = mysqli_real_escape_string($this->con, $uploadData->title);

        $uploadData->description = strip_tags($uploadData->description);  //removes any html tags
        $uploadData->description = mysqli_real_escape_string($this->con, $uploadData->description);


        $query = $this->con->prepare("INSERT INTO videos (uploadedBy, title, description, privacy, filePath, category)
                                        VALUES(?, ?, ?, ?, ?, ?)");
        $query->bind_param("ssssss", $uploadData->uploadedBy, $uploadData->title, $uploadData->description, $uploadData->privacy, $filePath,  $uploadData->category);
        $result = $query->execute();

        if(!$result) {
            echo "Error: " . mysqli_error($this->con);
        }
        return $result;
    }

    //Function converts any video format that's not MP4 to it 
    public function convertVideoToMp4($tempFilePath, $finalFilePath) {
        $cmd = "$this->ffmpegPath -i $tempFilePath $finalFilePath 2>&1"; // 2>&1 adds errors to screen if there are any

        $outputLog = array();
        exec($cmd, $outputLog, $returnCode);

        //If there is an error by returning 1
        if($returnCode != 0) {
            //Command failed, output error message log
            foreach($outputLog as $line) {
                echo $line . "<br>";
            }
            return false;
        }

        return true;
    }

    //This deletes the temporary file before being converted
    private function deleteFile($filePath) {
        if(!unlink($filePath)) {    //unlink checks if its deleted or not 
            //error message 
            echo "Could not delete file\n";
            return false;
        }

        return true;
    }

    public function generateThumbnails($filePath) {
        //thumbnailSize 
        $thumbnailSize = "210x118";
        //3 thumbnail options
        $numThumbnails = 3;
        //*******-Thumbnail File Path Directory-**************
        $pathToThumbnail = "uploads/videos/thumbnails";
        
        //Script is created to generate duration
        $duration = $this->getVideoDuration($filePath);

        //Here we can return the ID of the video row in the database, through lastInsertId Function
        $videoId = $this->con->insert_id;
        $this->updateDuration($duration, $videoId);
        
        for($num = 1; $num <= $numThumbnails; $num++) {
            $imageName = uniqid() . ".jpg";
            //this is the position of the video to generate thumbnail from, takes 3 evenly spaced times in video
            $interval = ($duration * 0.8) / $numThumbnails * $num; //I didnt take start middle and end of video due to start and end might have credits or us

            //Main path to thumbnail 
            $fullThumbnailPath = "$pathToThumbnail/$videoId-$imageName";
            
            //ffmpeg Script to create thumbnail, by grabbing image frame of the 3 times created by interval var
            $cmd = "$this->ffmpegPath -i $filePath -ss $interval -s $thumbnailSize -vframes 1 $fullThumbnailPath 2>&1";

            $outputLog = array();
            exec($cmd, $outputLog, $returnCode);

            if($returnCode != 0) {
                //Command failed
                foreach($outputLog as $line) {
                    echo $line . "<br>";
                }
            }


            $query = $this->con->prepare("INSERT INTO thumbnails(videoId, filePath, selected)
                                        VALUES(?, ?, ?)");

            $query->bind_param("iss", $videoId, $fullThumbnailPath, $selected);
            

            $selected = $num == 1 ? 1 : 0;

            $success = $query->execute();

            if(!$success) {
                echo "Error inserting thumbail\n";
                return false;
            }
        }

        return true;
    }

    private function getVideoDuration($filePath) {
        //Here i return the duration script using shell_exec
        //Script taking from https://trac.ffmpeg.org/wiki/FFprobeTips
        return (int)shell_exec("$this->ffprobePath -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $filePath");
    }

    //This function updates the duration of the video into the database by locating the video Id row 
    private function updateDuration($duration, $videoId) {
        // $duration = (int)$duration;
        //I use floor (Maths Operations) to round down decimal of the duration to cut out milli seconds
        $hours = floor($duration / 3600); //3600 is the number of seconds per hour
        $mins = floor(($duration - ($hours*3600)) / 60); //calcuate number of mins
        $secs = floor($duration % 60);  //calculate number of seconds

        //This sets how to duration is displayed, format (hours:minutes:seconds)
        $hours = ($hours < 1) ? "" : $hours . ":";
        $mins = ($mins < 10) ? "0" . $mins . ":" : $mins . ":";
        $secs = ($secs < 10) ? "0" . $secs : $secs;

        $duration = $hours.$mins.$secs;

        $query = $this->con->prepare("UPDATE videos SET duration=? WHERE id=?");
        $query->bind_param("ss", $duration, $videoId);
        
        $success = $query->execute();

        if(!$success) {
            echo "Error: " . mysqli_error($this->con);
            return false;
        }

    }
}
?>
