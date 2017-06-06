# Moteur-de-recherche
C'est à dessein un outil qui maintienne un équilibre entre la performance et l'ergonomie (retrouver une information et la r
etrouver vite).
Pour ce faire j'ai créé un moteur de recherche qui cherche à émuler celui de google 
et trie les articles par pertinence. 
Le moteur fonctionne grâce à  PDO:
$db = new PDO("mysql:host=127.0.0.1;dbname=queryengine;charset=utf8","root","");

L'instanciation attend 1 paramètre obligatoire mais vous devrez obligatoirement vous servir du second
$sqe=new Query_Engine($db,[
    "CONTENT"=&gt;"CONTENT_ARTICLE",
  "TITRE"=&gt;"TITRE_ARTICLE",
    "RESUME"=&gt;"RESUME_ARTICLE"
]);
Le premier c'est le PDO et le second assigne les champs sur lesquels le moteur devra faire les recherches.
CONTENT =&gt; Correspond au contenu de l'article
TITRE =&gt; Titre de l'article
RESUME =&gt; Résumé de l'article

Requête SQL
Vous devez paramétrer la requête SQL permettant de récupérer les articles
$sqe-&gt;
setQuery("SELECT * FROM article_tbl WHERE CONTENT_ARTICLE LIKE
 "%{:query:}%" OR TITRE_ARTICLE LIKE "%{:query:}%"");
 {:query:} sera substitué par le critère de recherche appelé par la fonction -&gt;query()

Paramétrez les coeficients 
$sqe-&gt;setCoef("chapitre", 0.03);
$sqe-&gt;setCoef("tagcontent", 0.7);
$sqe
-&gt;setCoef("title", 6);
$sqe-&gt;setCoef("resume", 0.06);
$sqe-&gt;setCoef("ta
g", 0.01);
"chapitre" correspond au nombre de chapitres dans l'article
"tagcontent" correspond au nombre de fois où la recherche se trouve entre des balises
"title" détermine quand la recherche correspond au titre
"resume" correspond au nombre de fois où la recherche se trouve dans le resumé
"tag" correspond au nombre de tags

Ajoutez des critères supplémentaires
$query="assurance";
$sqe-&gt;Pertinence_criteria("NB_CHAP", "/&lt;h2/is");
$sqe-&gt;Pertinence_criteria("TAG", "/&lt;[^&gt;]+&gt;/is");
$sqe-&gt;Pertinence_criteria("COUNT_RELIEF", "/&lt;[^&gt;]+&gt;($query)&lt;[^&gt;]+&gt;/is");

Pour finir, exécuter la requête
$result=$sqe-&gt;query($query);

Vous constaterez que quelque soit la requête saisie, le premier article sera toujours le plus pertinent.Le ZIP 
contient l'exemple décrit plus haut ainsi que la base de données utilisée que vous pouvez importer.
