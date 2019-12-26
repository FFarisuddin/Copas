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
        $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
        header("Location: index.php");
    }
    $listBlobsOptions = new ListBlobsOptions();
    $listBlobsOptions->setPrefix("");
   	

    do{
     	$result = $blobClient->listBlobs($containerName, $listBlobsOptions);
     	//$size = sizeof($result->getBlobs();
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
    <title>Eftu Indonesia (F2)</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
</head>
<body>


<script type="text/javascript">
    window.onload = function processImage() {
        
        var subscriptionKey = "ef9e7d8394e24d87a1a08487ce5eca5b";
 
        var uriBase =
            "https://computer-visionn.cognitiveservices.azure.com/vision/v2.0/analyze";
 
        var params = {
            "visualFeatures": "Description",
          
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

<div style="max-width:100%;max-height:100%" align="center" display:table>
 <div>
    <form action="index.php" method="post" enctype="multipart/form-data">
        <input type="file" name="photo" accept=".jpeg,.jpg,.png" />
        <input type="submit" name="submit" value="MAGIC" />
    </form> 
        <input type="hidden" name="inputImage" id="inputImage" value="<?php echo $url ?>" />
        <br>
</div>

<div display:table-cell>
		<img id="sourceImage" style="max-width:50%;max-height:45%" />
</div>
<div>
		<textarea id="responseTextArea" style="min-width:400px;min-height: 310px"></textarea>
</div>
</div>


</body>
</html>