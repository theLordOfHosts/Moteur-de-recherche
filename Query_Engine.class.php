<?php

/**
 * Query_Engine est un moteur de recherche destiné à une base de connaissance
 * 
 * Elle utilise le principe de la concurence.
 * Chaque article va être analysé dans son contenu et trié pour mettre en avant l'article le plus pertinant.
 *
 * @author Sébastien DAMART <sdamart@schoolweb>
 * 
 */
class Query_Engine {

    /**
     * objet sql destiné à exécuter la requête
     * @var PDO
     */
    private $DataEngine;

    /**
     * liste des coefs
     * @var Array
     */
    private $coefs;

    /**
     * liste des critères
     * @var Array
     */
    private $criteria;

    /**
     * paramètre pour certains éléments de la requête
     * @var Array
     */
    private $param_query;

    /**
     * requête SQL obligatoirement SELECT
     * @var string
     */
    private $sqlQuery;

    /**
     * liste des autre critère et méthode de calcul
     * @var Array
     */
    private $misc_crit;

    /**
     * 
     * @param PDO $easySQL dataEngine 
     * @param Array $param tableau de paramètres
     */
    public function __construct($easySQL, $param = null, $sqlQuery = "") {
        $this->DataEngine = $easySQL;
        if (isset($param))
            $this->param_query = $param;
        else {
            $this->param_query = [
                "CONTENT" => "CONTENT_ARTICLE",
                "TITRE" => "TITRE",
                "RESUME" => "RESUME"
            ];
        }
        $this->coefs = Array();
        $this->criteria = array();
        $this->misc_crit = array();
        if ($sqlQuery) {
            $this->sqlQuery = $sqlQuery;
        } else
            $this->sqlQuery = "SELECT ID_ARTICLE as ID, TITRE_ARTICLE as TITRE,LOGO_ARTICLE, KEYWORD_ARTICLE, CONTENT_ARTICLE, DATE_ARTICLE as DATE, RESUME_ARTICLE as RESUME, CONCAT(NOM_USER,\" \",PRENOM_USER) AS AUTEUR"
                    . " FROM article_tbl a "
                    . "INNER JOIN user_tbl u ON a.ID_USER=u.ID_USER "
                    . "WHERE CONTENT_ARTICLE LIKE \"%{:query:}%\" OR TITRE_ARTICLE LIKE \"%{:query:}%\""
                    . "OR RESUME_ARTICLE LIKE \"%{:query:}%\"";
    }

    /**
     * ajouter une fonction supplémentaire de calcul
     * @param Query_Engine_method $fnc
     */
    public function addFunction($fnc) {

        $this->misc_crit[] = $fnc;
    }

    /**
     * retourne la requête SQL servant à la recup de données
     * @return string
     */
    public function getQuery() {
        return $this->sqlQuery;
    }

    /**
     * paramètre la requête SQL servant à la recup de données
     * @param string $sql requête SQL (obligatoirement SELECT)
     * @throw "not a valid query"
     */
    public function setQuery($sql) {
        if (strpos($sql, "SELECT") !== false) {
            $this->sqlQuery = $sql;
            return $this;
        } else
            throw new Exception("Il ne s'agit pas d'une requête valide !");
    }

    /**
     * paramètre un coeficient
     * @param string $name nom du coef
     * @param decimal $value valeur associée
     */
    public function setCoef($name, $value) {
        $this->coefs[$name] = $value;
        return $this;
    }

    /**
     * paramètre les coeficients
     * @param Array $array
     */
    public function COEF($array) {
        $this->coefs = $array;
        return $this;
    }

    /**
     * ajoute un critère de concurence
     * @param string $name nom du critère
     * @param string $pattern nom du pattern
     */
    public function Pertinence_criteria($name, $pattern) {
        $i = count($this->criteria);
        $this->criteria[$i]["name"] = $name;
        $this->criteria[$i]["pattern"] = $pattern;
        return $this;
    }

    /**
     * permet d'éxecuter la requête
     * @param string $query mot clef recherché
     * @param bool $competitive_approach retourne les article triés par pertinence
     * @return Array
     */
    public function query($query, $competitive_approach = true, $orderBy = true) {
        $query = str_replace(" ", "%", $query);
        $q = str_replace("{:query:}", $query, $this->sqlQuery);
        $search = $this->DataEngine->query($q);
        //var_dump($search);
        $result = $search->fetchAll();
        $i = 0;
        /* Approche concurentiel */
        if ($competitive_approach) {
            foreach ($result as $rep) {
                $count = 0;
                $search_query = explode("%", $query);
                foreach ($search_query as $query_el) {
                    // $rep["RESUME"] = $rep["RESUME"];
                    if (preg_match_all("/$query_el/is", $rep[$this->param_query["CONTENT"]], $m)) {
                        $count+=count($m[0]);
                    }
                    if (isset($rep[$this->param_query["TITRE"]])) {
                        if (preg_match_all("/$query_el/is", $rep[$this->param_query["TITRE"]], $m)) {
                            $count+=count($m[0]);
                            $result[$i]["HAS_TITLE"] = count($m[0]);
                        } else {
                            $result[$i]["HAS_TITLE"] = 0;
                        }
                    }
                    if (isset($rep[$this->param_query["RESUME"]])) {
                        if (preg_match_all("/$query_el/is", $rep[$this->param_query["RESUME"]], $m)) {
                            $result[$i]["HAS_RESUME"] = count($m[0]);
                            $result[$i][$this->param_query["RESUME"]] = preg_replace("/$query_el/is", "<b style=\"color:#00F\">$query_el</b>", $rep[$this->param_query["RESUME"]]);
                            $count+=count($m[0]);
                        } else
                            $result[$i]["HAS_RESUME"] = 0;
                    }
                    foreach ($this->criteria as $crit) {
                        $this->test_critere($crit["name"], $crit["pattern"], $rep["CONTENT_ARTICLE"], $result[$i], $count);
                    }



                    $result[$i]["COUNT_QUERY"] = $count;
                    $this->PERTINENCE_COEF($result[$i]);
                }
                if (count($this->misc_crit)) {
                    foreach ($this->misc_crit as $fnc) {
                        $fnc->trigger($result[$i]);
                    }
                }
                $i++;
            }
        }

        // var_dump($result);
        if ($orderBy) {
            usort($result, "Query_Engine::cmparr");
        }
        return $result;
    }

    private function test_critere($name, $pattern, $content, &$arr, &$count) {
        if ($c = preg_match_all($pattern, $content, $matches)) {
            $arr[$name] = count($matches[0]);
        } else
            $arr[$name] = 0;
    }

    /**
     * retourne le barème de pertinence
     * @param array $article
     */
    private function PERTINENCE_COEF(&$article) {
        $pts = ($article["COUNT_QUERY"] * $article["NB_CHAP"]) * $this->coefs["chapitre"];
        $pts+=($article["COUNT_QUERY"] * $article["COUNT_RELIEF"]) * $this->coefs["tagcontent"];
        if (isset($article["HAS_TITLE"]))
            $pts+=($article["HAS_TITLE"] * $this->coefs["title"]);
        if (isset($article["HAS_RESUME"]))
            $pts+=($article["COUNT_QUERY"] * $article["HAS_RESUME"]) * $this->coefs["resume"];
        $pts+=($article["TAG"] * $this->coefs["tag"]);
        if (isset($article["RICH_VOC"]))
            $pts+=$article["RICH_VOC"] * $this->coefs["semantic"];
        if (isset($article["HAS_SEMANTIC"]))
            $pts+=$article["HAS_SEMANTIC"] * $this->coefs["has_semantic"];
        $article["PTS"] = $pts;
    }

    private static function cmparr($a, $b) {
        return $a["PTS"] < $b["PTS"];
    }

}

Abstract class Query_Engine_method {

    private $name;
    private $coef;

    public function __construct($name, $coef) {
        $this->name = $name;
        $this->coef = $coef;
    }

    protected function getCoef() {
        return $this->coef;
    }

    public function getName() {
        return $this->name;
    }

    public function trigger(&$result) {
        
    }

}

class disvalue extends Query_Engine_method {

    public function trigger(&$result) {
        //  DEBUG::SET_FILE("_log/nvo.txt");
        $html = ("\nARTICLE : " . $result["TITRE"]);
        $nb_w = strlen($result["CONTENT_ARTICLE"]);
        $nbTag = $result["TAG"];
        $byTag = ($nbTag / $nb_w) * 100;
        $result["UNTAG"] = $byTag;
        if ($byTag > 4) {
            $result["PTS"]-=$nbTag * $this->getCoef();
        }
    }

}

class query_result extends Query_Engine_method {

    private $database;
    private $query;

    public function __construct($name, $coef, $sqlitedatabase, $query) {
        parent::__construct($name, $coef);
        $this->database = $sqlitedatabase;
        $this->query = $query;
    }

    public function trigger(&$result) {
        $ID = $result["ID"];
        $pdo = new PDO('sqlite:' . $this->database);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query_ = "SELECT COUNT(*) AS CPTE FROM article_see WHERE ID_ARTICLE=$ID AND QUERYFROM LIKE'%" . $this->query . "\%'";
        $query = $pdo->query($query_);
        $a = array();
        while ($entry = $query->fetchAll()) {
            $a[] = $entry;
        }
        $result["NB_QUERY_SEE"] = $a[0][0]["CPTE"];
        $result["PTS"]+=$a[0][0]["CPTE"] * $this->getCoef();
    }

}

class Experience_method extends Query_Engine_method {

    private $database;

    public function __construct($name, $coef, $sqlitedatabase) {
        parent::__construct($name, $coef);
        $this->database = $sqlitedatabase;
    }

    public function trigger(&$result) {
        $ID = $result["ID"];
        $pdo = new PDO('sqlite:' . $this->database);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query_ = "SELECT COUNT(*) AS CPTE FROM article_see WHERE ID_ARTICLE=$ID";
        $query = $pdo->query($query_);
        $a = array();
        while ($entry = $query->fetchAll()) {
            $a[] = $entry;
        }
        $result["NB_SEE"] = $a[0][0]["CPTE"];
        $result["PTS"]+=$a[0][0]["CPTE"] * $this->getCoef();
    }

}
