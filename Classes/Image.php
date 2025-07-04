
<?php

class Image
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function UploadImage($image, $imageDatabaseName, $uploadPath)
    {
        if (isset($image) && !empty($image)) {
            $target_dir = $uploadPath;
            $imageName = basename($image["name"]);
            $newImageName = $imageDatabaseName;
            $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));;
            $valid_ext = ["jpg", "jpeg", "png"];
            $target_file = $target_dir . $newImageName;
            if (in_array($imageExtension, $valid_ext)) {
                if (!move_uploaded_file($image["tmp_name"], $target_file)) {
                    return [
                        "status" => "error",
                        "message" => "Failed to upload image"
                    ];
                } else {
                    return [
                        "status" => "success",
                        "message" => "Image Uploaded"
                    ];
                }
            } else {
                return [
                    "status" => "error",
                    "message" => "Invalid Image Type"
                ];
            }
        } else {
            return [
                "status" => "error",
                "message" => "Product Image is Required"
            ];
        }
    }

    public function UpdateImage($previousImage, $newImage, $imageDatabaseName, $uploadPath)
    {
        if (isset($newImage) && !empty($newImage["tmp_name"])) {
            $target_dir = $uploadPath;
            $imageName = basename($newImage["name"]);
            $newImageName = $imageDatabaseName;
            $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));;
            $valid_ext = ["jpg", "jpeg", "png"];
            $target_file = $target_dir . $newImageName;
            $previousImagePath = $target_dir . $previousImage;
            if (in_array($imageExtension, $valid_ext)) {
                if (!unlink($previousImagePath)) {
                    return [
                        "status" => "error",
                        "message" => "Failed to delete previous image"
                    ];
                }
                if (!move_uploaded_file($newImage["tmp_name"], $target_file)) {
                    return [
                        "status" => "error",
                        "message" => "Failed to upload new image"
                    ];
                }
                return [
                    "status" => "success",
                    "message" => "Image updated"
                ];
            } else {
                return [
                    "status" => "error",
                    "message" => "Invalid Image Type"
                ];
            }
        }
    }
}
