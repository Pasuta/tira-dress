<?php
function writeXml($loc){

    $file = BASE_DIR."/sitemap.xml";

    $xml = new DOMDocument();
    $xml->load($file);
    $nodes = $xml->getElementsByTagName('urlset');
    if ($nodes->length > 0) {
        $xml_url = $xml->createElement("url");

        $xml_loc = $xml->createElement("loc");
        $xml_loc->nodeValue = $loc;
        $xml_url->appendChild($xml_loc);

        $xml_loc = $xml->createElement("lastmod");
        $xml_loc->nodeValue = date('c');
        $xml_url->appendChild($xml_loc);

        $xml_loc = $xml->createElement("changefreq");
        $xml_loc->nodeValue = 'daily';
        $xml_url->appendChild($xml_loc);

        $xml_loc = $xml->createElement("priority");
        $xml_loc->nodeValue = '0.5';
        $xml_url->appendChild($xml_loc);

        $nodes->item(0)->appendChild($xml_url);
    }
    $xml->save($file);
}
?>