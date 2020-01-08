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
    <meta name="viewport" content="width=device-width, initial-scale=1">
<style>
* {
  box-sizing: border-box;
}

/* Create two equal columns that floats next to each other */
.column {
  float: left;
  width: 50%;
  padding: 10px;
  height: 300px; /* Should be removed. Only for demonstration */
}

/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}
</style>
</head>
<body>


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

<div class="row" align="center" display:table>
    <div class="column" align="left">
        <form action="index.php" method="post" enctype="multipart/form-data">
            <font face="verdana"> 1. Klik "Choose File" untuk pilih foto yang diinginkan. </font> <br>
            <input style="width:500px;height:70px" type="file" name="photo" accept=".jpeg,.jpg,.png" required="" /><br>
            <font face="verdana"> 2. Klik "MAGIC" untuk mengetahui informasi dari fotoMU. </font> <br>
            <input style="width:100px;height:70px" type="submit" name="submit" value="MAGIC" />
        </form> 
        <input type="hidden" name="inputImage" id="inputImage" value="<?php echo $url ?>" /><br>
    </div>
    <div class="column" display:table-cell>
		<img id="sourceImage" style="max-width:100%;max-height:200px" />
    </div>
    <div>
        <textarea id="responseTextArea" style="width:300px;min-height:350px"></textarea>
    </div>
</div>
</body>
</html>