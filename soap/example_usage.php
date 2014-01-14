<?php 
$u = "YOUR USERNAME";
$p = "YOUR PASSWORD";
$text="Pfizer develops and produces medicines and vaccines for a wide range of conditions including in the areas of immunology and inflammation, oncology, cardiovascular and metabolic diseases, neuroscience and pain. Pfizer's products include Lipitor (atorvastatin, used to lower LDL blood cholesterol); Lyrica (pregabalin, for neuropathic pain/fibromyalgia); Diflucan (fluconazole, an oral antifungal medication); Zithromax (azithromycin, an antibiotic); Viagra (sildenafil, for erectile dysfunction); and Celebrex/Celebra (celecoxib, an anti-inflammatory drug).";

include_once("XMLtoArray.class.php"); 
include_once("LSapiSoap.class.php"); 
$class = new LSapiSoap($u,$p);
$parameters = array('NamedEntityRecognitionInput'=>array('text' => $text));
$test=$class->makeTheCall("ontology", "searchNer", $parameters);
$test=xml2ary($test);
print_r($test);
?>