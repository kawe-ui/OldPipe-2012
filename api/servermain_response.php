<?php
// == JSON response for DEBUGGING purposes and for other sites == //
include("servermain.php");
header("Content-Type: application/json");
?>
{
    "Server": {
        "Directory": {
            "Full": "<?php echo $PHP_Self ?>",
            "Short": "<?php echo $PHP_Self_PathInfo["dirname"] ?>"
        },
        "File": {
            "Name":{
                "Extension": "<?php echo $PHP_Self_PathInfo["extension"] ?>",
                "FileName": "<?php echo $FileName ?>",
                "Full": "<?php echo $PHP_Self_PathInfo["basename"] ?>",
                "ScriptName": "<?php echo $ScriptName ?>",
                "Short": "<?php echo $PHP_Self_PathInfo["filename"] ?>"
            }
        },
        "HTTP": {
            "Protocol": "<?php echo $ServerHTTP?>",
            "Version": {
                "Short": "<?php echo trim($ServerProtocol, "HTTP/, HTTPS/") ?>",
                "Full": "<?php echo $ServerProtocol ?>"
            }
        },
        "Name": {
            "Full": "<?php echo $HTTP_Host_Full ?>",
            "Short": "<?php echo $HTTP_Host ?>"
        },
        "Port": "<?php echo $ServerPort ?>",
        "Request": {
            "Method": "<?php echo $ServerRequestMethod ?>",
            "Time": {
                "date": "<?php echo $ServerRequestTimeConverted ?>",
                "float": "<?php echo $ServerRequestTimeFloat ?>",
                "int": "<?php echo $ServerRequestTime ?>"
            }
        },
        "Signature": "<?php echo trim($ServerSignature) ?>",
        "Software": "<?php echo $ServerSoftware ?>"
    },
    "Api": {
        "Full": "<?php echo $ApiUrl_Full ?>",
        "Short": "<?php echo $ApiUrl ?>"
    }
}