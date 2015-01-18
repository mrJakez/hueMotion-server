<?php
if(is_array($content)) {
    echo json_encode($content);
}else{
    echo $content;
}
?>