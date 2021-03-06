<?php
    require_once 'vendor/autoload.php';
    require_once "./random_string.php";

    use MicrosoftAzure\Storage\Blob\BlobRestProxy;
    use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
    use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
    use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
    use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

    $connectionString = 'DefaultEndpointsProtocol=http;AccountName=blobff;AccountKey=9SkV9J8qyevowLNw6rXH1eOSbfKnRYohlvOhEwWUHJZMiZP4AiD24smx/xkLRyLBg3+8c5PdjYzcemAP6Pf1EQ==';
    $containerName = "images";

    // Create blob client.
    $blobClient = BlobRestProxy::createBlobService($connectionString);
    $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
    $listBlobsOptions = new ListBlobsOptions();
    $listBlobsOptions->setPrefix("");
    $size = sizeof($result->getBlobs());
  
    if (isset($_POST['submit'])) {
        $fileToUpload = strtolower($_FILES["photo"]["name"]);
        $content = fopen($_FILES["photo"]["tmp_name"], "r");
        if ($size!= 0){
            do{ 
            foreach ($result->getBlobs() as $blob2){
                $name = $blob2->getName();
                if($name != $fileToUpload){
                    $blobClient->deleteBlob($containerName, $name);
                }
            } $listBlobsOptions->setContinuationToken($result->getContinuationToken());
            } while($result->getContinuationToken());   
        }
        $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
        header("Location: index.php");
    }
    else{
        $url="https://www.dicoding.com/blog/wp-content/uploads/2014/12/dicoding-header-logo.png";
    }
  
    if ($size != 0){
        do{
        foreach ($result->getBlobs() as $blob){
            $url = $blob->getUrl();
        } $listBlobsOptions->setContinuationToken($result->getContinuationToken());
        } while($result->getContinuationToken());	
    }
?>


<!DOCTYPE html>
<html>
<head>
    <title>Eftu Indonesia (F2)</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
</head>
<body style="background-color:powderblue">


<script type="text/javascript">
    window.onload = function processImage() {
       
        var subscriptionKey = "ef9e7d8394e24d87a1a08487ce5eca5b";
 
        var uriBase =
            "https://computer-visionn.cognitiveservices.azure.com/vision/v2.0/analyze";
 
        var params = {
            "visualFeatures": "Description"
        };
 
        var sourceImageUrl = document.getElementById("inputImage").value;
        document.querySelector("#sourceImage").src = sourceImageUrl;
 
        // Make the REST API call.
        $.ajax({
            url: uriBase + "?" + $.param(params),
 
            // Request headers.
            beforeSend: function(xhrObj){
                xhrObj.setRequestHeader("Content-Type","application/json");
                xhrObj.setRequestHeader(
                    "Ocp-Apim-Subscription-Key", subscriptionKey);
            },
 
            type: "POST",
 
            // Request body.
            data: '{"url": ' + '"' + sourceImageUrl + '"}',
        })
 
        .done(function(data) {
            // Show formatted JSON on webpage.
            $("#responseTextArea").val(JSON.stringify(data, null, 2));
        })
 
        .fail(function(jqXHR, textStatus, errorThrown) {
            // Display error message.
            var errorString = (errorThrown === "") ? "Error. " :
                errorThrown + " (" + jqXHR.status + "): ";
            errorString += (jqXHR.responseText === "") ? "" :
                jQuery.parseJSON(jqXHR.responseText).message;
            alert(errorString);
        });
    };
</script>

<div align="center">
    <div>
        <form action="index.php" method="post" enctype="multipart/form-data">
            <font face="verdana"> 1. Klik "Choose File" untuk memilih fotoMU. </font> <br>
            <input style="width:250px;height:70px" type="file" name="photo" accept=".jpeg,.jpg,.png" required="" /> <br>
            <font face="verdana"> 2. Klik "MAGIC" mengetahui informasi fotoMU. </font> <br>
            <input style="width:90px;height:30px" type="submit" name="submit" value="MAGIC" />
        </form> 
        <input type="hidden" name="inputImage" id="inputImage" value="<?php echo $url ?>" /><br>
    </div>
    <div>
		<img id="sourceImage" style="max-width:100%;max-height:200px" />
    </div>
    <div>
        <textarea id="responseTextArea" style="resize:none;min-width:100%;min-height:200px;font-size:20px"></textarea>
    </div>
</div>
</body>
</html>