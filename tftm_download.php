<?php

    require_once("../../../wp-load.php");

    ob_end_clean();

    $file = 'TableOfTweets.'.$_GET['type_file'];
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.$file);
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    $data= tftm_settings_bd::tftm_select_all("ID","ASC");

if($_GET['type_file']=='csv'){
    $out = fopen("php://output" ,'w');
    foreach ($data as $fields) {
        fputcsv($out, $fields);
    }
    fclose($out);
}
else if($_GET['type_file']=='xml'){

    $doc = new DOMDocument('1.0');
        $doc->formatOutput = true;
        $TableOfTweets = $doc->createElement('TableOfTweets');
        $TableOfTweets = $doc->appendChild($TableOfTweets);

        foreach($data as $key=>$row)
        {
                $current_row= $doc->createElement('TweetContent');
                $TableOfTweets->appendChild($current_row);

            foreach($row as $key=>$value) {
                $em = $doc->createElement($key);
                $text = $doc->createTextNode($value);
                $em->appendChild($text);
                $current_row->appendChild($em);
            }

        }
       $doc->save("php://output");
}
?>