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
    if (isset($_POST['submit'])) {
        $fileToUpload = strtolower($_FILES["photo"]["name"]);
        $content = fopen($_FILES["photo"]["tmp_name"], "r");
        // echo fread($content, filesize($fileToUpload));
        $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
        header("Location: index.php");
    }
    $listBlobsOptions = new ListBlobsOptions();
    $listBlobsOptions->setPrefix("");
   
     do{
     	$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
        foreach ($result->getBlobs() as $blob)
        {
            $url = $blob->getUrl();
        }
        $listBlobsOptions->setContinuationToken($result->getContinuationToken());
    } while($result->getContinuationToken());

?>



<!DOCTYPE html>
<html>
<head>
    <title>AGIT</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
</head>
<body>


<script type="text/javascript">
    function processImage() {
        
        var subscriptionKey = "ef9e7d8394e24d87a1a08487ce5eca5b";
 
        var uriBase =
            "https://computer-visionn.cognitiveservices.azure.com/vision/v2.0/analyze";
 
        var params = {
            "visualFeatures": "Categories,Description,Color",
            "details": "",
            "language": "en",
        };
 
        var sourceImageUrl = document.getElementById("inputImage").value;
        //document.querySelector("#sourceImage").src = sourceImageUrl;
 
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


    <h1>Cognitive Service x Blob Storage</h1>

    <form action="index.php" method="post" enctype="multipart/form-data">
        Image to analyze: <input type="file" name="photo" accept=".jpeg,.jpg,.png" />
        <input type="submit" name="submit" value="snap" />
    </form> 

    <br><br>


        <input type="hidden" name="inputImage" id="inputImage" value="<?php echo $url ?>" />
        <button id="analyze_btn" onclick="processImage()">Analyze image</button>

           <br><br>
    
    <div id="wrapper" style="width:1020px; display:table;">
        <div id="jsonOutput" style="width:600px; display:table-cell;">
            Response:
            <br><br>
            <textarea id="responseTextArea" style="width:580px; height:400px;"></textarea>
        </div>
    
        <div id="imageDiv" style="width:420px; display:table-cell;">
            Source image:
            <br><br>
            <img id="sourceImage" width="400" />
        </div>
    </div>
</body>
</html>