<?php
include "./Query_Engine.class.php";
$db = new PDO("mysql:host=127.0.0.1;dbname=queryengine;charset=utf8","root","");
if(isset($_GET["ID"])){
$article_q=$db->query("SELECT * FROM article_tbl WHERE ID_ARTICLE = ".$_GET["ID"]);
$art=$article_q->fetchAll();
echo $art[0]["CONTENT_ARTICLE"];
}
$sqe=new Query_Engine($db,[
    "CONTENT"=>"CONTENT_ARTICLE",
    "TITRE"=>"TITRE_ARTICLE",
    "RESUME"=>"RESUME_ARTICLE"
]);
/*Ici je change la requête par défaut pour quelle coïncide avec mon schéma
 * les variables {:query:} correspondront à la valeur passée dans ->query (cf. ligne 30)
 */
$sqe->setQuery("SELECT * FROM article_tbl WHERE CONTENT_ARTICLE LIKE \"%{:query:}%\" OR TITRE_ARTICLE LIKE \"%{:query:}%\"");
/*Coef*/
$sqe->setCoef("chapitre", 0.03);
$sqe->setCoef("tagcontent", 0.7);
$sqe->setCoef("title", 6);
$sqe->setCoef("resume", 0.06);
$sqe->setCoef("tag", 0.01);
/*Ajouter un critère*/
$sqe->Pertinence_criteria("NB_CHAP", "/\<h2/is");
$sqe->Pertinence_criteria("TAG", "/<[^>]+>/is");
$sqe->Pertinence_criteria("COUNT_RELIEF", "/<[^>]+>(assurance)\<[^>]+>/is");

echo "<h5>Test avec le mot assurance</h5>";
$result=$sqe->query("assurance");
foreach($result as $art){
   echo "<a href='index.php?ID=".$art["ID_ARTICLE"]."'>".$art["TITRE_ARTICLE"]."</a> | ".$art["PTS"]."<br>";
}
$sqe->Pertinence_criteria("COUNT_RELIEF", "/<[^>]+>(Forfait)\<[^>]+>/is");
echo "<h5>Test avec le mot Forfait</h5>";
$result=$sqe->query("Forfait");
foreach($result as $art){
    echo "<a href='index.php?ID=".$art["ID_ARTICLE"]."'>".$art["TITRE_ARTICLE"]."</a> | ".$art["PTS"]."<br>";
}